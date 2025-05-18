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
        Schema::create('pindahan_dana', function (Blueprint $table) {
          
                $table->id();
                $table->foreignId('dari')->constrained('tambah_saldo')->onDelete('cascade');
                $table->foreignId('tujuan')->constrained('tambah_saldo')->onDelete('cascade');
                $table->foreignId('id_toko')->constrained('toko')->onDelete('cascade');
                $table->decimal('nominal', 15, 2);
                $table->decimal('operasional', 15, 2)->default(0);
                $table->decimal('admin', 15, 2)->default(0);
                $table->decimal('saldo_awal', 15, 2);
                $table->decimal('saldo_akhir', 15, 2);
                $table->text('keterangan')->nullable();
                $table->foreignId('create_by')->constrained('karyawan_users')->onDelete('cascade');
                $table->timestamps();
  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pindahan_dana');
    }
};
