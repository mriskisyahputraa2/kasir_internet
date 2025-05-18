<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TambahSaldo extends Model
{
    use HasFactory;

    protected $table = 'tambah_saldo'; // ⚠ Pastikan nama tabel benar
    protected $fillable = ['id_toko', 'id_user', 'nama_platform', 'saldo', 'logo', 'keterangan'];

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko');
    }

    public function user()
    {
        return $this->belongsTo(KaryawanUser::class, 'id_user');
    }
}
