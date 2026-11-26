<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'user_id', 'vehicle_unit_id', 'rental_package_id', 'quantity',
        'start_at', 'end_at', 'pickup_location', 'total_price', 'pay_mode', 'status',
        'returned_at', 'return_condition', 'late_fee', 'damage_fee', 'cancel_reason', 'terms_accepted_at', 'terms_version',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'returned_at' => 'datetime',
        'terms_accepted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(VehicleUnit::class, 'vehicle_unit_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(RentalPackage::class, 'rental_package_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /** Ulasan bisa diberikan sekali, setelah pesanan selesai. */
    public function bisaDiulas(): bool
    {
        return $this->status === 'selesai' && ! $this->review;
    }

    /** Total pembayaran yang sudah diverifikasi admin. */
    public function getPaidAmountAttribute(): int
    {
        return (int) $this->payments->where('status', 'diterima')->sum('amount');
    }

    /** Tagihan akhir = harga sewa + denda keterlambatan + biaya kerusakan. */
    public function getGrandTotalAttribute(): int
    {
        return $this->total_price + $this->late_fee + $this->damage_fee;
    }

    public function getDueAmountAttribute(): int
    {
        return max(0, $this->grand_total - $this->paid_amount);
    }

    /** Status pembayaran diturunkan dari data pembayaran, tidak disimpan terpisah. */
    public function getPaymentStatusAttribute(): string
    {
        if ($this->payments->where('status', 'menunggu')->isNotEmpty()) return 'Menunggu Verifikasi';
        if ($this->paid_amount > 0) return $this->due_amount === 0 ? 'Lunas' : 'DP Dibayar';
        return $this->payments->isNotEmpty() ? 'Ditolak' : 'Belum Bayar';
    }

    /** Batas waktu unggah bukti bayar (jam) sejak pesanan dibuat. 0 = fitur mati. */
    public static function paymentWindowHours(): int
    {
        return max(0, (int) Setting::get('batas_bayar_jam', 2));
    }

    /** Kapan pesanan ini batal otomatis bila belum ada bukti bayar. Null bila tidak berlaku. */
    public function getPayDeadlineAttribute(): ?Carbon
    {
        $jam = static::paymentWindowHours();
        if (! $jam || $this->status !== 'menunggu_konfirmasi') return null;
        if ($this->payments->whereIn('status', ['menunggu', 'diterima'])->isNotEmpty()) return null;

        return $this->created_at->copy()->addHours($jam);
    }

    /**
     * Batalkan pesanan "menunggu konfirmasi" yang lewat batas waktu tanpa bukti bayar
     * (tidak ada pembayaran menunggu/diterima). Unitnya otomatis kosong lagi.
     * Dipanggil oleh scheduler dan juga sebelum cek ketersediaan, jadi tetap jalan walau scheduler mati.
     */
    public static function cancelExpired(): int
    {
        $jam = static::paymentWindowHours();
        if (! $jam) return 0;

        return static::where('status', 'menunggu_konfirmasi')
            ->where('created_at', '<', now()->subHours($jam))
            ->whereDoesntHave('payments', fn ($p) => $p->whereIn('status', ['menunggu', 'diterima']))
            ->update(['status' => 'dibatalkan', 'cancel_reason' => 'Otomatis: tidak ada bukti bayar dalam ' . $jam . ' jam', 'updated_at' => now()]);
    }

    /** Nomor pesanan berikutnya: RR-0001, RR-0002, ... */
    public static function nextCode(): string
    {
        return 'RR-' . str_pad((string) ((static::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT);
    }

    /** Pesanan yang masih berjalan dari sisi pelanggan (belum selesai dan tidak batal). */
    public const AKTIF = ['menunggu_konfirmasi', 'dikonfirmasi', 'sedang_disewa'];

    /**
     * Langkah berikutnya untuk pelanggan: [nada, teks].
     * Nada: warn / danger = pelanggan harus bertindak, info / ok = sedang berjalan, muted = selesai atau batal.
     * Dipakai Beranda dan Pesanan saya supaya teksnya selalu sama.
     */
    public function langkahBerikutnya(): array
    {
        $rp = fn ($n) => 'Rp' . number_format($n, 0, ',', '.');

        if ($this->status === 'dibatalkan') return ['muted', $this->cancel_reason ?: 'Pesanan dibatalkan'];
        if ($this->pay_deadline) {
            return $this->payments->where('status', 'ditolak')->isNotEmpty()
                ? ['warn', 'Bukti bayar ditolak. Unggah ulang sebelum ' . $this->pay_deadline->translatedFormat('d M, H:i')]
                : ['warn', 'Unggah bukti bayar sebelum ' . $this->pay_deadline->translatedFormat('d M, H:i')];
        }
        if ($this->hasPendingPayment()) return ['info', 'Bukti bayar sedang dicek admin'];
        if ($this->status === 'menunggu_konfirmasi') return ['info', 'Menunggu konfirmasi admin'];
        if ($this->status === 'dikonfirmasi') return ['ok', 'Siap diambil ' . $this->start_at->translatedFormat('d M, H:i')];
        if ($this->status === 'sedang_disewa' && ! $this->returned_at) {
            return $this->end_at->isPast()
                ? ['danger', 'Lewat jadwal kembali ' . $this->end_at->translatedFormat('d M, H:i')]
                : ['ok', 'Kembalikan sebelum ' . $this->end_at->translatedFormat('d M, H:i')];
        }
        if ($this->due_amount > 0) return ['warn', 'Sisa tagihan ' . $rp($this->due_amount)];

        return ['muted', 'Selesai ' . ($this->returned_at ?? $this->end_at)->translatedFormat('d M Y')];
    }

    public function hasPendingPayment(): bool
    {
        return $this->payments->where('status', 'menunggu')->isNotEmpty();
    }

    /**
     * Jalankan perubahan status di dalam transaksi. Baris pesanan dikunci dan
     * dimuat ulang dulu, jadi dua admin yang menekan tombol bersamaan tidak
     * bisa saling menimpa. Pelanggaran aturan dilempar sebagai DomainException.
     */
    private function transition(callable $change): void
    {
        DB::transaction(function () use ($change) {
            $fresh = static::with('payments', 'unit')->lockForUpdate()->findOrFail($this->id);
            $change($fresh);
            $this->setRawAttributes($fresh->getAttributes(), true);
            $this->setRelation('payments', $fresh->payments);
        });
    }

    /** Admin mengonfirmasi pesanan. Syarat: sudah ada pembayaran yang diverifikasi. */
    public function confirm(): void
    {
        $this->transition(function (Order $o) {
            if ($o->status !== 'menunggu_konfirmasi') {
                throw new \DomainException('Hanya pesanan yang menunggu konfirmasi yang bisa dikonfirmasi.');
            }
            if ($o->paid_amount <= 0) {
                throw new \DomainException('Verifikasi pembayaran dulu sebelum mengonfirmasi pesanan.');
            }
            $o->update(['status' => 'dikonfirmasi']);
        });
    }

    /** Serah terima kendaraan ke pelanggan. */
    public function startRental(): void
    {
        $this->transition(function (Order $o) {
            if ($o->status !== 'dikonfirmasi') {
                throw new \DomainException('Hanya pesanan yang sudah dikonfirmasi yang bisa diserahterimakan.');
            }
            $o->update(['status' => 'sedang_disewa']);
            $o->unit->update(['status' => 'disewa']);
        });
    }

    /** Pembatalan oleh admin. Pesanan yang sudah dibayar harus ditangani manual (refund). */
    public function cancelByAdmin(): void
    {
        $this->transition(function (Order $o) {
            if (! in_array($o->status, ['menunggu_konfirmasi', 'dikonfirmasi'], true)) {
                throw new \DomainException('Pesanan ini tidak bisa dibatalkan.');
            }
            if ($o->paid_amount > 0) {
                throw new \DomainException('Pesanan sudah ada pembayaran yang diterima. Selesaikan pengembalian dana dulu (di luar sistem), lalu hubungi pengembang untuk membatalkan.');
            }
            $o->update(['status' => 'dibatalkan']);
        });
    }

    /**
     * Catat pengembalian kendaraan: hitung denda telat (dibulatkan ke atas per jam),
     * simpan biaya kerusakan, bebaskan unit. Pesanan baru "selesai" setelah lunas.
     */
    public function recordReturn(Carbon $returnedAt, ?string $condition, int $damageFee): void
    {
        $this->transition(function (Order $o) use ($returnedAt, $condition, $damageFee) {
            if ($o->status !== 'sedang_disewa' || $o->returned_at) {
                throw new \DomainException('Pengembalian hanya bisa dicatat untuk pesanan yang sedang disewa.');
            }
            $jamTelat = $returnedAt->greaterThan($o->end_at)
                ? (int) ceil($o->end_at->diffInMinutes($returnedAt) / 60)
                : 0;

            $o->update([
                'returned_at' => $returnedAt,
                'return_condition' => $condition,
                'late_fee' => $jamTelat * (int) Setting::get('late_fee_per_hour', 50000),
                'damage_fee' => $damageFee,
            ]);
            $o->unit->update(['status' => 'tersedia']);
            $o->load('payments');
            $o->finishIfSettled();
        });
    }

    /** Selesai otomatis bila kendaraan sudah kembali dan tagihan lunas. */
    public function finishIfSettled(): void
    {
        if ($this->status === 'sedang_disewa' && $this->returned_at && $this->due_amount === 0) {
            $this->update(['status' => 'selesai']);
        }
    }

    /** Admin mencatat pembayaran tunai di tempat sebesar sisa tagihan. */
    public function recordCashPayment(int $adminId): void
    {
        $this->transition(function (Order $o) use ($adminId) {
            if (in_array($o->status, ['dibatalkan', 'selesai'], true) || $o->due_amount <= 0) {
                throw new \DomainException('Tidak ada tagihan yang perlu dilunasi.');
            }
            $o->payments()->create([
                'amount' => $o->due_amount,
                'status' => 'diterima',
                'verified_by' => $adminId,
                'verified_at' => now(),
            ]);
            $o->load('payments');
            $o->finishIfSettled();
        });
    }

    /** Verifikasi bukti bayar. Menolak penerimaan yang membuat total bayar melebihi tagihan. */
    public function verifyPayment(int $paymentId, bool $accept, int $adminId): void
    {
        $this->transition(function (Order $o) use ($paymentId, $accept, $adminId) {
            $p = $o->payments->firstWhere('id', $paymentId);

            if (! $p || $p->status !== 'menunggu') {
                throw new \DomainException('Pembayaran ini sudah diproses.');
            }
            if ($accept && $o->status === 'dibatalkan') {
                throw new \DomainException('Pesanan sudah dibatalkan, pembayaran tidak bisa diterima.');
            }
            if ($accept && $p->amount > $o->due_amount) {
                throw new \DomainException('Jumlah melebihi sisa tagihan. Tolak pembayaran ini atau periksa nominalnya.');
            }

            $p->update([
                'status' => $accept ? 'diterima' : 'ditolak',
                'verified_by' => $adminId,
                'verified_at' => now(),
            ]);
            $o->load('payments');
            $o->finishIfSettled();
        });
    }
}