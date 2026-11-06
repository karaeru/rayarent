<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 12)->unique();                       // RR-0001
            $table->foreignId('user_id')->constrained();
            $table->foreignId('vehicle_unit_id')->constrained('vehicle_units');
            $table->foreignId('rental_package_id')->constrained('rental_packages');
            $table->unsignedTinyInteger('quantity')->default(1);        // jumlah periode
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('pickup_location')->nullable();
            $table->unsignedInteger('total_price');
            $table->enum('pay_mode', ['dp', 'lunas']);
            $table->enum('status', ['menunggu_konfirmasi', 'dikonfirmasi', 'sedang_disewa', 'selesai', 'dibatalkan'])
                  ->default('menunggu_konfirmasi');

            // pengembalian
            $table->dateTime('returned_at')->nullable();
            $table->string('return_condition')->nullable();
            $table->unsignedInteger('late_fee')->default(0);
            $table->unsignedInteger('damage_fee')->default(0);

            $table->timestamps();

            // mempercepat pengecekan bentrok jadwal unit
            $table->index(['vehicle_unit_id', 'start_at', 'end_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
