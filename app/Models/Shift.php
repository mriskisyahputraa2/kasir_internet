<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    use HasFactory;

    // Tentukan nama tabel secara manual
    protected $table = 'shift';

    protected $fillable = ['nama_shift', 'waktu_mulai', 'waktu_selesai'];

    // Relasi ke tabel absen
    public function absen()
    {
        return $this->hasMany(Absen::class, 'id_shift');
    }
}
