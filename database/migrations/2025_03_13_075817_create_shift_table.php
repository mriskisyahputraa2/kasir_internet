<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shift', function (Blueprint $table) {
            $table->id();
            $table->string('nama_shift'); // Nama shift (misal: Pagi, Sore, Malam)
            $table->time('waktu_mulai'); // Waktu mulai shift (misal: 08:00:00)
            $table->time('waktu_selesai'); // Waktu selesai shift (misal: 16:00:00)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift');
    }
};