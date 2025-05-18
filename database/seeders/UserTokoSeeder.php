<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTokoSeeder extends Seeder
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

        if ($admin && $kasir && $tokoA && $tokoB) {
            DB::table('user_toko')->updateOrInsert(
                ['id_user' => $admin->id, 'id_toko' => $tokoA->id],
                []
            );

            DB::table('user_toko')->updateOrInsert(
                ['id_user' => $kasir->id, 'id_toko' => $tokoB->id],
                []
            );

            echo "Seeder UserTokoSeeder berhasil dijalankan.\n";
        } else {
            echo "Data karyawan atau toko tidak ditemukan! Pastikan seeder `KaryawanUserSeeder` dan `TokoSeeder` sudah dijalankan.\n";
        }
        // DB::table('user_toko')->insert([
        //     ['id_user' => $admin->id, 'id_toko' => $tokoA->id],
        //     ['id_user' => $kasir->id, 'id_toko' => $tokoB->id],
        // ]);
    }
}