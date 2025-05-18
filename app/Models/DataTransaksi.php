<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
class DataTransaksi extends Model
{
    use HasFactory;

    protected $table = 'data_transaksi'; // ⬅️ Menentukan nama tabel yang benar

    protected $fillable = [
        'nomor_transaksi',
        'nominal_transaksi',
        'jenis_transaksi',
        'sumber_dana',
        'terima_dana',
        'admin_dalam',
        'admin_luar',
        'admin_bank',
        'tipe_transaksi',
        'dana_awal_sumber',
        'dana_akhir_sumber',
        'dana_awal_terima',
        'dana_akhir_terima',
        'id_toko',
        'keterangan',
        'sesi_kasir_id',
        'created_by'
    ];

    // Relasi ke TambahSaldo untuk sumber dana
    // Relasi ke sumber dana
    public function user()
    {
        return $this->belongsTo(KaryawanUser::class, 'created_by');
    }
    public function sumberDana(): BelongsTo
    {
        return $this->belongsTo(TambahSaldo::class, 'sumber_dana')->withDefault();
    }

    // Relasi ke penerima dana
    public function terimaDana(): BelongsTo
    {
        return $this->belongsTo(TambahSaldo::class, 'terima_dana')->withDefault();
    }

    // Relasi ke toko
    public function toko(): BelongsTo
    {
        return $this->belongsTo(Toko::class, 'id_toko')->withDefault();
    }

    public static function hitungOmsetTransaksi()
    {
        return self::sum('nominal_transaksi');
    }

    public static function hitungProfitTransaksi()
    {
        return self::sum(DB::raw('admin_dalam + admin_luar'));
    }

    public function sesiKasir()
    {
        return $this->belongsTo(SesiKasir::class, 'sesi_kasir_id');
    }
    

}
