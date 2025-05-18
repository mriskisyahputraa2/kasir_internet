<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PindahanDana extends Model
{
    protected $table = 'pindahan_dana';
    protected $fillable = [
        'dari', 'saldo_awal_dari', 'saldo_akhir_dari',
        'tujuan', 'saldo_awal_tujuan', 'saldo_akhir_tujuan',
        'id_toko', 'nominal', 'operasional', 'admin',
        'keterangan', 'create_by'
    ];

    public function dariSaldo()
    {
        return $this->belongsTo(TambahSaldo::class, 'dari');
    }

    public function tujuanSaldo()
    {
        return $this->belongsTo(TambahSaldo::class, 'tujuan');
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko');
    }

    public function user()
    {
        return $this->belongsTo(KaryawanUser::class, 'create_by');
    }
}



