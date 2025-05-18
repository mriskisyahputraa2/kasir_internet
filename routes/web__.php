<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    DashboardController,
    AbsensiController,
    DataKaryawan,
    DataToko,
    ShiftController,
    TambahSaldoController,
    DataTransaksiController,
    PindahanDanaController,
    PinjamanDanaController,
    KategoriController,
    ProdukController,
    TransaksiController,
    KelolaStokController,
    KasirTransaksiController,
    SesiKasirController
};

// Redirect root ke login
Route::get('/', fn() => redirect()->route('login'));

// ----------------------
// GUEST ROUTES
// ----------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ----------------------
// AUTH ROUTES
// ----------------------
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ----------------------
// PROTECTED ROUTES
// ----------------------
Route::middleware(['auth.session', 'check.toko'])->group(function () {

    // Pilih Toko (semua user)
    Route::get('/pilih-toko', [AuthController::class, 'pilihToko'])->name('pilih-toko');
    Route::post('/set-toko', [AuthController::class, 'setToko'])->name('set-toko');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ----------------------
    // SUPERADMIN ROUTES
    // ----------------------
    Route::middleware('superadmin')->prefix('pengaturan')->group(function () {
        Route::post('/karyawan/{id}/pilih-toko', [DataKaryawan::class, 'pilihToko'])->name('karyawan.pilih-toko');
        Route::resource('toko', DataToko::class);
        Route::resource('shift', ShiftController::class);
    });

    // Absensi
    Route::prefix('absensi')->group(function () {
        Route::get('/hari-ini', [AbsensiController::class, 'absensiHariIni'])->name('absensi.hari-ini');
        Route::post('/masuk', [AbsensiController::class, 'absenMasuk'])->name('absensi.masuk');
        Route::post('/keluar', [AbsensiController::class, 'absenKeluar'])->name('absensi.keluar');
        Route::get('/daftar', [AbsensiController::class, 'daftarAbsen'])->name('absensi.daftar');

        // Hanya superadmin
        Route::middleware('superadmin')->group(function () {
            Route::get('/karyawan', [AbsensiController::class, 'absensiKaryawan'])->name('absensi.karyawan');
            Route::get('/riwayat/{id}', [AbsensiController::class, 'riwayatAbsen'])->name('absensi.riwayat');
        });
    });

    // Karyawan
    Route::resource('karyawan', DataKaryawan::class);
    Route::post('/karyawan/{id}/pilih-toko', [DataKaryawan::class, 'pilihToko'])->name('karyawan.pilih-toko');

    // Toko dan Shift (non-superadmin)
    Route::resource('toko', DataToko::class);
    Route::resource('shift', ShiftController::class);

    // Kasir dan Transaksi
    Route::resource('kasir', DataTransaksiController::class);
    Route::get('/kasir-transaksi', [KasirTransaksiController::class, 'index'])->name('kasir.transaksi');

    // Kelola Dana
    Route::resource('tambah-saldo', TambahSaldoController::class)->except(['show']);
    Route::resource('pindahan-dana', PindahanDanaController::class);
    Route::resource('pinjaman-dana', PinjamanDanaController::class);
    Route::post('/pinjaman-dana/return/{id}', [PinjamanDanaController::class, 'returnSaldo'])->name('pinjaman-dana.return');

    // Produk
    Route::resource('kategori', KategoriController::class);
    Route::resource('produk', ProdukController::class);
    Route::resource('kelola-stok', KelolaStokController::class);

    // Transaksi
    Route::prefix('transaksi')->group(function () {
        Route::get('/', [TransaksiController::class, 'index'])->name('transaksi.index');
        Route::post('/', [TransaksiController::class, 'store'])->name('transaksi.store');
        Route::get('/pembayaran/{id}', [TransaksiController::class, 'pembayaran'])->name('transaksi.pembayaran');
        Route::post('/complete/{id}', [TransaksiController::class, 'completePayment'])->name('transaksi.complete');
        Route::get('/bayar-nanti', [TransaksiController::class, 'listBayarNanti'])->name('transaksi.bayar-nanti');
        Route::get('/lanjutkan/{id}', [TransaksiController::class, 'lanjutkanPembayaran'])->name('transaksi.lanjutkan');
        Route::post('/{id}/bayar-nanti', [TransaksiController::class, 'bayarNantiStore'])->name('transaksi.bayar.nanti');
        Route::delete('/{id}/cancel', [TransaksiController::class, 'cancel'])->name('transaksi.cancel');
    });

    // Sesi Kasir
    Route::get('/sesi-kasir', [SesiKasirController::class, 'index'])->name('sesi.kasir');
    Route::post('/buka-kasir', [SesiKasirController::class, 'bukaKasir'])->name('buka.kasir');
    Route::post('/tutup-kasir', [SesiKasirController::class, 'tutupKasir'])->name('tutup.kasir');
    Route::get('/riwayat-sesi-kasir', [SesiKasirController::class, 'riwayatSesiKasir'])->name('riwayat.sesi.kasir');
});

// ----------------------
// FALLBACK ROUTE
// ----------------------
Route::fallback(fn() => redirect()->route('dashboard'));
