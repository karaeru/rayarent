<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleUnit extends Model
{
    use HasFactory;

    public const AKTIF = ['menunggu_konfirmasi', 'dikonfirmasi', 'sedang_disewa'];

    protected $fillable = ['vehicle_id', 'plate_number', 'status'];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Unit yang bisa disewa pada rentang waktu tertentu:
     * tidak sedang perawatan dan tidak bentrok dengan pesanan aktif lain.
     * Dua periode bentrok jika: mulai_a < selesai_b DAN selesai_a > mulai_b.
     */
    public function scopeAvailableBetween(Builder $q, $start, $end): Builder
    {
        return $q->where('status', '!=', 'perawatan')
            ->whereDoesntHave('orders', function (Builder $o) use ($start, $end) {
                $o->whereIn('status', self::AKTIF)
                  ->where('start_at', '<', $end)
                  ->where('end_at', '>', $start);
            });
    }
}
