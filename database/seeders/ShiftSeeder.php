<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shift::create([
            'nama_shift' => 'Pagi',
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '16:00:00',
        ]);

        Shift::create([
            'nama_shift' => 'Sore',
            'waktu_mulai' => '16:00:00',
            'waktu_selesai' => '00:00:00',
        ]);

        Shift::create([
            'nama_shift' => 'Malam',
            'waktu_mulai' => '00:00:00',
            'waktu_selesai' => '08:00:00',
        ]);
    }
}
