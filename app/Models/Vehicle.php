<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'seat_label', 'capacity', 'transmission', 'photo', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function units(): HasMany
    {
        return $this->hasMany(VehicleUnit::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(RentalPackage::class);
    }

    /** Alamat foto (diberi ?v= supaya browser memuat ulang saat foto diganti). Null bila belum ada foto. */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo
            ? route('foto.kendaraan', ['vehicle' => $this->id, 'v' => $this->updated_at?->timestamp])
            : null;
    }

    /** Varian lain dengan nama dasar sama, mis. "Brio Matic" dan "Brio Manual". */
    public function variants(): Collection
    {
        $base = static::baseName($this->name);

        return static::where('id', '!=', $this->id)->get()
            ->filter(fn (self $v) => static::baseName($v->name) === $base)
            ->values();
    }

    public static function baseName(string $name): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/', ' ', preg_replace('/\b(matic|manual)\b/i', '', $name))));
    }

    /** Kategori untuk filter di landing page: [kunci => label]. */
    public const CATEGORIES = [
        'city' => 'City car',
        'mpv' => 'MPV',
        'suv' => 'SUV',
        'minibus' => 'Minibus',
        'premium' => 'Premium',
    ];

    /** Kategori ditebak dari nama, lalu kapasitas. Tambahkan nama model baru di sini bila perlu. */
    public function getCategoryAttribute(): string
    {
        $n = mb_strtolower($this->name);
        $is = fn (string $pola) => (bool) preg_match('/\b(' . $pola . ')\b/', $n);

        return match (true) {
            $is('alphard|vellfire|camry|lexus|mercedes|bmw|land cruiser') => 'premium',
            $this->capacity >= 10 || $is('elf|hiace|bus|minibus|travello') => 'minibus',
            $is('fortuner|pajero|terios|rush|cr-?v|hr-?v|raize|rocky|almaz|xpander cross|everest') => 'suv',
            $is('avanza|xenia|innova|zenix|ertiga|xpander|mobilio|livina|veloz|calya|sigra|stargazer|voxy|serena|luxio|gran max') => 'mpv',
            $is('agya|ayla|brio|yaris|jazz|sirion|ignis|wr-?v|swift|march|picanto|city') => 'city',
            $this->capacity <= 5 => 'city',
            default => 'mpv',
        };
    }

    /** Jenis siluet untuk ikon pengganti foto. */
    public function getIconAttribute(): string
    {
        return ['city' => 'city', 'mpv' => 'mpv', 'suv' => 'suv', 'minibus' => 'van', 'premium' => 'mpv'][$this->category];
    }

    /** Potongan alamat publik dari nama, mis. "Innova Zenix" -> "innova-zenix". */
    public function getSlugAttribute(): string
    {
        return Str::slug($this->name);
    }

    /** Cari kendaraan aktif dari slug. Nama kendaraan unik, jadi slug juga unik. */
    public static function findActiveBySlug(string $slug): ?self
    {
        return static::where('is_active', true)->get()->first(fn (self $v) => $v->slug === $slug);
    }

    /** Harga paket termurah, untuk ditampilkan di katalog ("mulai Rp..."). */
    public function getMinPriceAttribute(): int
    {
        return (int) $this->packages->min('price');
    }
}
