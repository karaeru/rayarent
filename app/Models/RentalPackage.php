<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalPackage extends Model
{
    use HasFactory;

    public const SERVICES = [
        'lepas_kunci' => 'Lepas Kunci',
        'driver'      => 'Dengan Driver',
        'driver_bbm'  => 'Driver + BBM',
    ];

    protected $fillable = ['vehicle_id', 'service', 'duration_hours', 'price'];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getServiceLabelAttribute(): string
    {
        return self::SERVICES[$this->service] ?? $this->service;
    }
}
