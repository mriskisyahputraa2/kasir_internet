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
        Schema::create('user_toko', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('karyawan_users')->onDelete('cascade');
            $table->foreignId('id_toko')->constrained('toko')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
             // Hapus foreign key constraints
             Schema::table('user_toko', function (Blueprint $table) {
                $table->dropForeign(['id_user']);
                $table->dropForeign(['id_toko']);
            });

            // Hapus tabel
            Schema::dropIfExists('user_toko');
    }
};
