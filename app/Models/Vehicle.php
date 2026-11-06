<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    /** Harga paket termurah, untuk ditampilkan di katalog ("mulai Rp..."). */
    public function getMinPriceAttribute(): int
    {
        return (int) $this->packages->min('price');
    }
}
