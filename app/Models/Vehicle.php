<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /** Harga paket termurah, untuk ditampilkan di katalog ("mulai Rp..."). */
    public function getMinPriceAttribute(): int
    {
        return (int) $this->packages->min('price');
    }
}
