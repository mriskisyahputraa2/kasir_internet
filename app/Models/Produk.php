<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_toko',
        'nama',
        'kategori',
        'barcode',
        'harga_beli',
        'harga_jual',
        'stok',
        'tgl_kadaluarsa',
        'foto',
        'diskon_global',
        'createBy'
    ];

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko');
    }

    public function kategoriRelasi()
    {
        return $this->belongsTo(Kategori::class, 'kategori');
    }

    // public function kategori()
    // {
    //     return $this->belongsTo(Kategori::class, 'kategori_id');
    // }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori');
    }
}
