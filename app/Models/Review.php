<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = ['order_id', 'user_id', 'vehicle_id', 'rating', 'comment', 'is_visible'];

    protected $casts = ['is_visible' => 'boolean', 'rating' => 'integer'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /** Hanya ulasan yang tidak disembunyikan admin. */
    public function scopeVisible(Builder $q): Builder
    {
        return $q->where('is_visible', true);
    }

    /** Nama yang ditampilkan publik: "Budi Santoso" -> "Budi S." (privasi). */
    public static function namaPublik(?string $nama): string
    {
        $bagian = preg_split('/\s+/', trim((string) $nama)) ?: [];
        $depan = $bagian[0] ?? '';
        if ($depan === '') return 'Pelanggan';

        return count($bagian) > 1 ? $depan . ' ' . mb_strtoupper(mb_substr(end($bagian), 0, 1)) . '.' : $depan;
    }

    public function getPublicNameAttribute(): string
    {
        return static::namaPublik($this->user?->name);
    }

    /** Rata-rata dan jumlah ulasan yang tampil. $query opsional (mis. dibatasi per kendaraan). */
    public static function ringkasan(?Builder $query = null): array
    {
        $q = $query ?? static::query();
        $q = $q->clone()->visible();

        return ['rata' => round((float) $q->clone()->avg('rating'), 1), 'jumlah' => $q->clone()->count()];
    }
}
