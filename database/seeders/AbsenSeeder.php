<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = DB::table('karyawan_users')->where('username', 'admin1')->first();
        $kasir = DB::table('karyawan_users')->where('username', 'kasir1')->first();
        $tokoA = DB::table('toko')->where('nama', 'Toko A')->first();
        $tokoB = DB::table('toko')->where('nama', 'Toko B')->first();
        DB::table('absen')->insert([
            [
                'id_user' => $admin->id,
                'tanggal_absen' => now()->toDateString(),
                'status' => 'Hadir',
                'id_toko' => $tokoA->id,
                'id_shift' => $admin->id_shift,
                'waktu_absen' => now(),
            ],
            [
                'id_user' => $kasir->id,
                'tanggal_absen' => now()->toDateString(),
                'status' => 'Hadir',
                'id_toko' => $tokoB->id,
                'id_shift' => $kasir->id_shift,
                'waktu_absen' => now(),
            ],
        ]);
    }
}