<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DataKaryawan;
use App\Http\Controllers\DataToko;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TambahSaldoController;
use App\Http\Controllers\DataTransaksiController;
use App\Http\Controllers\PindahanDanaController;
use App\Http\Controllers\PinjamanDanaController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KelolaStokController;
use App\Http\Controllers\KasirTransaksiController;
use App\Http\Controllers\SesiKasirController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view("welcome");
// });

Route::get('/', function () {
    return redirect()->route('login');
});

// --------------------------
// ROUTE UNTUK GUEST (BELUM LOGIN)
// --------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// --------------------------
// ROUTE AUTHENTIKASI
// --------------------------
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --------------------------
// ROUTE YANG MEMBUTUHKAN LOGIN
// --------------------------
Route::middleware(['auth.session', 'check.toko'])->group(function () {
    Route::get('/pilih-toko', [AuthController::class, 'pilihToko'])->name('pilih-toko');
    Route::post('/set-toko', [AuthController::class, 'setToko'])->name('set-toko');
    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // PILIH TOKO (KHUSUS SUPERADMIN)
    Route::middleware('superadmin')->group(function () {
        Route::get('/pilih-toko', [AuthController::class, 'pilihToko'])->name('pilih-toko');
        Route::post('/set-toko', [AuthController::class, 'setToko'])->name('set-toko');
    });

    // ABSENSI
    Route::prefix('absensi')->group(function () {
        // UNTUK SEMUA YANG LOGIN (ADMIN/KASIR)
        Route::get('/hari-ini', [AbsensiController::class, 'absensiHariIni'])->name('absensi.hari-ini');
        Route::post('/masuk', [AbsensiController::class, 'absenMasuk'])->name('absensi.masuk');
        Route::post('/keluar', [AbsensiController::class, 'absenKeluar'])->name('absensi.keluar');
        Route::get('/daftar', [AbsensiController::class, 'daftarAbsen'])->name('absensi.daftar');

        // KHUSUS SUPERADMIN
        Route::middleware('superadmin')->group(function () {
            Route::get('/karyawan', [AbsensiController::class, 'absensiKaryawan'])->name('absensi.karyawan');
            Route::get('/riwayat/{id}', [AbsensiController::class, 'riwayatAbsen'])->name('absensi.riwayat');
        });
    });


    Route::resource('kasir', DataTransaksiController::class);
    // KELOLA DANA
    Route::resource('tambah-saldo', TambahSaldoController::class)->except(['show']);
    Route::resource('pindahan-dana', PindahanDanaController::class);
    Route::resource('pinjaman-dana', PinjamanDanaController::class);
    Route::post('/pinjaman-dana/return/{id}', [PinjamanDanaController::class, 'returnSaldo'])->name('pinjaman-dana.return');

    // KELOLA PRODUK
    Route::resource('kategori', KategoriController::class);
    Route::resource('produk', ProdukController::class);
    Route::resource('kelola-stok', KelolaStokController::class);


    // TRANSAKSI
    Route::get('/kasir', [KasirTransaksiController::class, 'index'])->name('kasir.transaksi');
    Route::get('/', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::post('/', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/pembayaran/{id}', [TransaksiController::class, 'pembayaran'])->name('transaksi.pembayaran');
    Route::post('/complete/{id}', [TransaksiController::class, 'completePayment'])->name('transaksi.complete');
    Route::get('/bayar-nanti', [TransaksiController::class, 'listBayarNanti'])->name('transaksi.bayar-nanti');
    Route::get('/lanjutkan/{id}', [TransaksiController::class, 'lanjutkanPembayaran'])->name('transaksi.lanjutkan');


    // PENGATURAN (KHUSUS SUPERADMIN)
    Route::middleware('superadmin')->prefix('pengaturan')->group(function () {
        Route::post('/karyawan/{id}/pilih-toko', [DataKaryawan::class, 'pilihToko'])
            ->name('karyawan.pilih-toko');
        Route::resource('toko', DataToko::class);
        Route::resource('shift', ShiftController::class);
    });
    Route::resource('karyawan', DataKaryawan::class);


    Route::resource('shift', ShiftController::class);

    // Route khusus untuk pemilihan toko
    Route::post('/karyawan/{id}/pilih-toko', [DataKaryawan::class, 'pilihToko'])->name('karyawan.pilih-toko');


    Route::resource('toko', DataToko::class);

    Route::resource('/kelola-stok', KelolaStokController::class);
    Route::get('/kasir-transaksi', [KasirTransaksiController::class, 'index'])->name('kasir.transaksi');

    // Transaksi
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/pembayaran/{id}', [TransaksiController::class, 'pembayaran'])->name('transaksi.pembayaran');
    Route::post('/transaksi/complete/{id}', [TransaksiController::class, 'completePayment'])->name('transaksi.complete');
    Route::get('/transaksi/bayar-nanti', [TransaksiController::class, 'listBayarNanti'])->name('transaksi.bayar-nanti');
    Route::get('/transaksi/lanjutkan/{id}', [TransaksiController::class, 'lanjutkanPembayaran'])->name('transaksi.lanjutkan');




    Route::get('/sesi-kasir', [SesiKasirController::class, 'index'])->name('sesi.kasir');
    Route::post('/buka-kasir', [SesiKasirController::class, 'bukaKasir'])->name('buka.kasir');
    Route::post('/tutup-kasir', [SesiKasirController::class, 'tutupKasir'])->name('tutup.kasir');
    Route::get('/riwayat-sesi-kasir', [SesiKasirController::class, 'riwayatSesiKasir'])->name('riwayat.sesi.kasir');


    // Ubah route untuk Bayar Nanti
    Route::post('/transaksi/{id}/bayar-nanti', [TransaksiController::class, 'bayarNantiStore'])
        ->name('transaksi.bayar.nanti');


    // Ubah route untuk Cancel
    Route::delete('/transaksi/{id}/cancel', [TransaksiController::class, 'cancel'])
        ->name('transaksi.cancel');
});

// --------------------------
// ROUTE FALLBACK
// --------------------------
Route::fallback(function () {
    return redirect()->route('dashboard');
});
