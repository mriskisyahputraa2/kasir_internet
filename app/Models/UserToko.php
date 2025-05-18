<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserToko extends Model
{
    use HasFactory;

    protected $table = 'user_toko';
    protected $fillable = ['id_user', 'id_toko'];

      // Relasi ke model KaryawanUser
      public function karyawan()
      {
          return $this->belongsTo(KaryawanUser::class, 'id_user');
      }

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko');
    }
}
