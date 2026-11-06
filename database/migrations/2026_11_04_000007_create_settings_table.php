<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('value');
        });

        DB::table('settings')->insert([
            ['key' => 'dp_percent', 'value' => '30'],
            ['key' => 'late_fee_per_hour', 'value' => '50000'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
