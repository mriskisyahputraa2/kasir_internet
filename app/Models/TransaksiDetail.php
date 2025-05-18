<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TransaksiDetail extends Model
{
    use HasFactory;

    protected $fillable = ['transaksi_id', 'produk_id', 'jumlah', 'harga_satuan', 'diskon', 'subtotal', 'total_harga'];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
    public static function hitungOmsetBarang()
    {
        return self::sum('subtotal');
    }
    public static function hitungProfitBarang()
    {
        return self::join('produk', 'transaksi_details.produk_id', '=', 'produk.id')
            ->sum(DB::raw('(transaksi_details.subtotal - (produks.harga_beli * transaksi_details.jumlah))'));
    }
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id', 'id');
    }
}
