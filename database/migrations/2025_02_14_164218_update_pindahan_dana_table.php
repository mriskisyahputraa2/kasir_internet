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
        Schema::table('pindahan_dana', function (Blueprint $table) {
            $table->decimal('saldo_awal_tujuan', 15, 2)->after('tujuan');
            $table->decimal('saldo_akhir_tujuan', 15, 2)->after('saldo_awal_tujuan');
            $table->decimal('saldo_awal_dari', 15, 2)->after('dari');
            $table->decimal('saldo_akhir_dari', 15, 2)->after('saldo_awal_dari');

            // Hapus kolom saldo_awal dan saldo_akhir yang lama
            $table->dropColumn(['saldo_awal', 'saldo_akhir']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pindahan_dana', function (Blueprint $table) {
            $table->decimal('saldo_awal', 15, 2)->after('admin');
            $table->decimal('saldo_akhir', 15, 2)->after('saldo_awal');

            $table->dropColumn(['saldo_awal_tujuan', 'saldo_akhir_tujuan', 'saldo_awal_dari', 'saldo_akhir_dari']);
        });
    }
};
