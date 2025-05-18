<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi')->unique();
            $table->decimal('nominal_transaksi', 15, 2);
            $table->string('jenis_transaksi');

            // Deklarasikan kolom sebelum foreign key
            $table->unsignedBigInteger('sumber_dana');
            $table->unsignedBigInteger('terima_dana');

            // Tambahkan foreign key setelah deklarasi kolom
            $table->foreign('sumber_dana')->references('id')->on('tambah_saldo')->onDelete('cascade');
            $table->foreign('terima_dana')->references('id')->on('tambah_saldo')->onDelete('cascade');

            $table->decimal('admin_dalam', 15, 2)->default(0);
            $table->decimal('admin_luar', 15, 2)->default(0);
            $table->decimal('admin_bank', 15, 2)->default(0);
            $table->string('tipe_transaksi');
            $table->decimal('dana_awal_sumber', 20, 2);
            $table->decimal('dana_akhir_sumber', 20, 2);
            $table->decimal('dana_awal_terima', 20, 2);
            $table->decimal('dana_akhir_terima', 20, 2);
            $table->text('keterangan')->nullable();

            // Pastikan nama tabel yang direferensikan sesuai
            $table->foreignId('id_toko')->constrained('toko')->onDelete('cascade');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_transaksi');
    }
};
