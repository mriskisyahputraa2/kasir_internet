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
        Schema::create('sesi_kasir', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->nullable(); // Tanggal sesi kasir
            $table->foreignId('shift_id')->constrained('shift')->onDelete('cascade'); // Relasi ke tabel shift
            $table->foreignId('id_toko')->nullable()->constrained('toko')->onDelete('set null'); // Relasi ke tabel toko
            $table->decimal('saldo_awal', 15, 2)->default(0); // Saldo awal kasir
            $table->decimal('saldo_akhir', 15, 2)->default(0); // Saldo akhir kasir
            $table->decimal('dana_laci', 15, 2)->default(0); // Dana laci harian
            $table->enum('status', ['buka', 'tutup'])->default('buka'); // Status sesi kasir
            $table->foreignId('user_id')->constrained('karyawan_users')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_kasir');
    }
};
