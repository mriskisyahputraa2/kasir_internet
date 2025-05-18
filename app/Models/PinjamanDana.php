<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinjamanDana extends Model
{
    use HasFactory;

    protected $table = 'pinjaman_dana';

    protected $fillable = [
        'dari',
        'nominal',
        'admin',
        'saldo_awal',
        'saldo_akhir',
        'status',
        'keterangan',
        'id_toko',
        'create_by',
    ];

    // Relasi ke TambahSaldo (sumber saldo)
    public function sumber()
    {
        return $this->belongsTo(TambahSaldo::class, 'dari');
    }

    // Relasi ke Toko
    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko');
    }

    // Relasi ke User (pencatat transaksi)
    public function creator()
    {
        return $this->belongsTo(KaryawanUser::class, 'create_by');
    }
}
