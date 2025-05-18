<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
     * Jalankan migrasi.
     */
    public function up()
    {
        Schema::table('data_transaksi', function (Blueprint $table) {
            // Tambahkan kolom sesi_kasir_id
            $table->foreignId('sesi_kasir_id')
                ->nullable() // Kolom boleh kosong
                ->constrained('sesi_kasir') // Relasi ke tabel sesi_kasir
                ->onDelete('set null'); // Jika sesi_kasir dihapus, set null di kolom ini
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down()
    {
        Schema::table('data_transaksi', function (Blueprint $table) {
            // Hapus foreign key dan kolom sesi_kasir_id
            $table->dropForeign(['sesi_kasir_id']); // Hapus foreign key
            $table->dropColumn('sesi_kasir_id'); // Hapus kolom
        });
    }
};
