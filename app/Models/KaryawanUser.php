<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KaryawanUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'karyawan_users';

    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',
        'jumlah_absen',
        'foto',
        'remember_token',
        'id_shift',
    ];

    protected $hidden = [
        'password',
    ];

    // Relasi ke tabel absen
    public function absen()
    {
        return $this->hasMany(Absen::class, 'id_user');
    }

    // Relasi ke tabel shift
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'id_shift');
    }

    // Relasi ke tabel user_toko
    public function userToko()
    {
        return $this->hasOne(UserToko::class, 'id_user');
    }

    // Relasi ke tabel toko melalui user_toko
    public function toko()
    {
        return $this->hasOneThrough(
            Toko::class,
            UserToko::class,
            'id_user',
            'id',
            'id',
            'id_toko'
        );
    }
}
