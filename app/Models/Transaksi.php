<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = ['id_toko', 'createBy', 'total_harga', 'bayar', 'kembalian', 'status_pembayaran', 'tujuan_dana'];

    public function details()
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id');
    }
    public function tujuanSaldo()
    {
        return $this->belongsTo(TambahSaldo::class, 'tujuan_dana');
    }
}
