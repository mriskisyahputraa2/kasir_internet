<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiKasir extends Model
{
    use HasFactory;

    protected $table = 'sesi_kasir';
    protected $fillable = [
        'tanggal',
        'shift_id',
        'id_toko',
        'saldo_awal',
        'saldo_akhir',
        'dana_laci',
        'status',
        'user_id',
    ];

    // Relasi ke tabel shift
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    // Relasi ke tabel toko
    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko');
    }

    // Relasi ke tabel user
    public function user()
    {
        return $this->belongsTo(KaryawanUser::class, 'user_id');
    }

    // Relasi ke tabel data_transaksi
    public function transaksi()
    {
        return $this->hasMany(DataTransaksi::class, 'sesi_kasir_id');
    }
}