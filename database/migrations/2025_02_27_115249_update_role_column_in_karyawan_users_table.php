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
        Schema::table('karyawan_users', function (Blueprint $table) {
            // Ubah ENUM untuk menambahkan nilai 'karyawan'
            $table->enum('role', ['superadmin', 'admin', 'kasir', 'karyawan'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan_users', function (Blueprint $table) {
            // Kembalikan ke nilai ENUM sebelumnya (opsional)
            $table->enum('role', ['superadmin', 'admin', 'kasir'])->change();
        });
    }
};
