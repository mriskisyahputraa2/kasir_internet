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
        Schema::table('pinjaman_dana', function (Blueprint $table) {
            // Hapus foreign key lama
            $table->dropForeign(['create_by']);

            // Ubah referensi ke tabel karyawanuser
            $table->foreign('create_by')
                ->references('id')
                ->on('karyawan_users')
                ->onDelete('cascade'); // Bisa juga set null dengan onDelete('set null')
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pinjaman_dana', function (Blueprint $table) {
            // Hapus foreign key baru
            $table->dropForeign(['create_by']);

            // Kembalikan ke referensi users jika rollback
            $table->foreign('create_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
