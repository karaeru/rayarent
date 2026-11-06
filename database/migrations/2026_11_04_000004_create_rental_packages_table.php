<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->enum('service', ['lepas_kunci', 'driver', 'driver_bbm']);
            $table->unsignedTinyInteger('duration_hours');   // 12 atau 24
            $table->unsignedInteger('price');                // rupiah, tanpa desimal
            $table->timestamps();

            $table->unique(['vehicle_id', 'service', 'duration_hours']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_packages');
    }
};
