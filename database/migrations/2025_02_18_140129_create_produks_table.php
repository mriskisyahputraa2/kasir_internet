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
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_toko');
            $table->string('nama');
            $table->unsignedBigInteger('kategori');
            $table->string('barcode')->unique();
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('harga_jual', 15, 2);
            $table->integer('stok')->default(0);
            $table->date('tgl_kadaluarsa')->nullable();
            $table->string('foto')->nullable();
            $table->decimal('diskon_global', 15, 2)->default(0);
            $table->unsignedBigInteger('createBy');
            $table->timestamps();

            $table->foreign('id_toko')->references('id')->on('toko')->onDelete('cascade');
            $table->foreign('kategori')->references('id')->on('kategoris')->onDelete('cascade');
            $table->foreign('createBy')->references('id')->on('karyawan_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
