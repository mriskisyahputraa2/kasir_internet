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
            $table->foreignId('id_shift')->nullable()->constrained('shift')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan_users', function (Blueprint $table) {
            $table->dropForeign(['id_shift']);
            $table->dropColumn('id_shift');
        });
    }
};
