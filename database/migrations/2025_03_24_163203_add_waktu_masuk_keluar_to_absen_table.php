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
        Schema::table('absen', function (Blueprint $table) {
            $table->timestamp('waktu_masuk')->nullable()->after('waktu_absen');
            $table->timestamp('waktu_keluar')->nullable()->after('waktu_masuk');
            $table->string('lokasi')->nullable()->after('waktu_keluar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absen', function (Blueprint $table) {
            $table->dropColumn(['waktu_masuk', 'waktu_keluar', 'lokasi']);
        });
    }
};