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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_toko');
            $table->unsignedBigInteger('createBy');
            $table->decimal('total_harga', 15, 2);
            $table->decimal('bayar', 15, 2);
            $table->decimal('kembalian', 15, 2);
            $table->timestamps();
            $table->enum('status_pembayaran', ['Lunas', 'Belum Lunas', 'Bayar Nanti'])->default('Belum Lunas');
            $table->unsignedBigInteger('tujuan_dana')->nullable(); // Corrected `after` usage
        
            // Foreign key constraints
            $table->foreign('tujuan_dana')->references('id')->on('tambah_saldo')->onDelete('set null');
            $table->foreign('id_toko')->references('id')->on('toko')->onDelete('cascade');
            $table->foreign('createBy')->references('id')->on('karyawan_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
