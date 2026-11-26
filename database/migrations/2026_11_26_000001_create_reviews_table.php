<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Ulasan pelanggan: satu ulasan per pesanan yang sudah selesai. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');                 // 1 sampai 5
            $table->text('comment')->nullable();                    // maks. 500 karakter (divalidasi)
            $table->boolean('is_visible')->default(true);          // admin bisa menyembunyikan dari halaman publik
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
