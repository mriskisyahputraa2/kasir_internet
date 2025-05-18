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
        Schema::table('data_transaksi', function (Blueprint $table) {
            Schema::table('data_transaksi', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable()->constrained('karyawan_users')->onDelete('set null');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_transaksi', function (Blueprint $table) {
            Schema::table('data_transaksi', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            });
        });
    }
};
