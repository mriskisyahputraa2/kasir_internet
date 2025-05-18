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
        Schema::create('pinjaman_dana', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dari'); // Foreign key ke tambah_saldo
            $table->decimal('nominal', 15, 2);
            $table->decimal('admin', 15, 2)->default(0);
            $table->decimal('saldo_awal', 15, 2);
            $table->decimal('saldo_akhir', 15, 2);
            $table->enum('status', ['dipinjam', 'dikembalikan'])->default('dipinjam');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('id_toko'); // Foreign key ke toko
            $table->unsignedBigInteger('create_by'); // User yang melakukan transaksi
            $table->timestamps();

            // Foreign Keys
            $table->foreign('dari')->references('id')->on('tambah_saldo')->onDelete('cascade');
            $table->foreign('id_toko')->references('id')->on('toko')->onDelete('cascade');
            $table->foreign('create_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjaman_dana');
    }
};
