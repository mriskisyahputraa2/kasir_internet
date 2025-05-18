<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absen extends Model
{
    use HasFactory;

    protected $table = 'absen';
    protected $fillable = [
        'id_user',
        'tanggal_absen',
        'status',
        'id_toko',
        'id_shift',
        'waktu_absen',
        'waktu_masuk',
        'waktu_keluar',
        'lokasi'
    ];

    protected $casts = [
        'tanggal_absen' => 'date',
        'waktu_absen' => 'datetime',
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime'
    ];

    // Format waktu dalam WIB (Asia/Jakarta)
    public function getWaktuAbsenWibAttribute()
    {
        return $this->waktu_absen
            ? $this->waktu_absen->setTimezone('Asia/Jakarta')->format('H:i:s')
            : '-';
    }

    public function getWaktuMasukWibAttribute()
    {
        return $this->waktu_masuk
            ? $this->waktu_masuk->setTimezone('Asia/Jakarta')->format('H:i:s')
            : '-';
    }

    public function getWaktuKeluarWibAttribute()
    {
        return $this->waktu_keluar
            ? $this->waktu_keluar->setTimezone('Asia/Jakarta')->format('H:i:s')
            : '-';
    }

    // Format tanggal dalam bahasa Indonesia
    public function getTanggalAbsenIndoAttribute()
    {
        if (!$this->tanggal_absen) return '-';

        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $carbonDate = Carbon::parse($this->tanggal_absen)->setTimezone('Asia/Jakarta');

        return $hari[$carbonDate->dayOfWeek] . ', ' .
               $carbonDate->day . ' ' .
               $bulan[$carbonDate->month] . ' ' .
               $carbonDate->year;
    }

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'id_shift');
    }
}
