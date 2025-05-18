<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KaryawanUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class KaryawanUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

     public function run(): void
     {
         $users = [
             [
                 'username' => 'superadmin',
                 'nama' => 'Super Admin',
                 'password' => Hash::make('password123'),
                 'role' => 'superadmin',
                 'jumlah_absen' => 0,
                 'foto' => 'default.png',
             ],
             [
                 'username' => 'admin1',
                 'nama' => 'Admin 1',
                 'password' => Hash::make('password123'),
                 'role' => 'admin',
                 'jumlah_absen' => 0,
                 'foto' => 'default.png',
             ],
             [
                 'username' => 'kasir1',
                 'nama' => 'Kasir 1',
                 'password' => Hash::make('password123'),
                 'role' => 'kasir',
                 'jumlah_absen' => 0,
                 'foto' => 'default.png',
             ],
         ];

         foreach ($users as $user) {
             KaryawanUser::updateOrCreate(
                 ['username' => $user['username']],
                 $user
             );
         }
     }

}