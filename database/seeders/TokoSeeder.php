<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TokoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('toko')->insert([
            [
                'nama' => 'Toko A',
                'lokasi' => 'Jakarta',
                'transaksi' => 100,
                'total_karyawan' => 5,
                'keterangan' => 'Toko cabang utama di Jakarta',
            ],
            [
                'nama' => 'Toko B',
                'lokasi' => 'Bandung',
                'transaksi' => 80,
                'total_karyawan' => 3,
                'keterangan' => 'Cabang kedua di Bandung',
            ],
        ]);
    }
}
