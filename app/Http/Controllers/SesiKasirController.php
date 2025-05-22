<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SesiKasir;
use App\Models\Shift;
use App\Models\DataTransaksi;
use App\Models\TambahSaldo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SesiKasirController extends Controller
{
    /**
     * Tampilkan halaman manajemen sesi kasir.
     */
    public function index()
    {
        // Ambil sesi kasir yang masih aktif (status 'buka')
        $sesiAktif = SesiKasir::where('status', 'buka')->first();

        // Ambil semua shift untuk dropdown form buka kasir
        $shifts = Shift::all();

        // Ambil riwayat sesi kasir
        $sesiKasir = SesiKasir::with(['shift', 'toko', 'user'])->orderBy('tanggal', 'desc')->get();

        return view('sesi_kasir.index', compact('sesiAktif', 'shifts', 'sesiKasir'));
    }

    /**
     * Buka sesi kasir.
     */
    // public function bukaKasir(Request $request)
    // {
    //     // Validasi input
    //     $request->validate([
    //         'shift_id' => 'required|exists:shift,id',
    //         'dana_laci' => 'required|numeric|min:0',
    //     ]);

    //     // Ambil id_toko dari session
    //     $idToko = session('id_toko');
    //     if (!$idToko) {
    //         return redirect()->back()->withErrors(['error' => 'Toko tidak ditemukan dalam session.']);
    //     }

    //     // Cek apakah ada sesi kasir yang masih terbuka
    //     $sesiAktif = SesiKasir::where('status', 'buka')->first();
    //     if ($sesiAktif) {
    //         return redirect()->back()->withErrors(['error' => 'Masih ada sesi kasir yang terbuka.']);
    //     }

    //     // Hitung saldo awal dari total kolom saldo di tabel tambah_saldo
    //     $totalSaldo = TambahSaldo::sum('saldo');

    //     // Hitung saldo awal dengan menambahkan dana laci yang diinput
    //     $saldoAwal = $totalSaldo + $request->dana_laci;

    //     // Update dana laci di record dengan id = 1 di tabel tambah_saldo
    //     $tambahSaldo = TambahSaldo::find(1); // Ambil record dengan id = 1
    //     if ($tambahSaldo) {
    //         $tambahSaldo->update([
    //             'saldo' => $request->dana_laci, // Ganti saldo dengan nilai baru
    //         ]);
    //     }

    //     // Buat sesi kasir baru
    //     $sesiKasir = SesiKasir::create([
    //         'tanggal' => now()->toDateString(), // Tanggal hari ini
    //         'shift_id' => $request->shift_id,
    //         'id_toko' => $idToko, // Gunakan id_toko dari session
    //         'saldo_awal' => $saldoAwal, // Simpan saldo awal yang sudah ditambah dana laci
    //         'dana_laci' => $request->dana_laci, // Simpan dana laci yang diinput
    //         'status' => 'buka',
    //         'user_id' => Auth::id(),
    //     ]);

    //     return redirect()->back()->with('success', 'Sesi kasir berhasil dibuka.');
    // }
    public function bukaKasir(Request $request)
    {
        $request->validate([
            'shift_id' => 'required|exists:shift,id',
            'dana_laci' => 'required|numeric|min:0',
        ]);

        $idToko = session('id_toko');
        if (!$idToko) {
            return redirect()->back()->withErrors(['error' => 'Toko tidak ditemukan dalam session.']);
        }

        // // Cek saldo awal Laci
        // $laciSaldo = TambahSaldo::where('nama_platform', 'Laci')->first();
        // if (!$laciSaldo || $laciSaldo->saldo <= 0) {
        //     return redirect()->back()->with('swal_error', 'Harap mengisikan saldo awal terlebih dahulu pada Laci, baru membuka kasir');
        // }

        // Cek saldo awal Laci
        $laciSaldo = TambahSaldo::where('nama_platform', 'Laci')->first();
        if (!$laciSaldo) {
            return redirect()->back()->with('swal_error', 'Platform Laci belum tersedia, hubungi admin.');
        }
        if ($laciSaldo->saldo < 0) {
            return redirect()->back()->with('swal_error', 'Saldo Laci tidak boleh minus.');
        }
        // Cek sesi kasir aktif
        $sesiAktif = SesiKasir::where('status', 'buka')->first();
        if ($sesiAktif) {
            return redirect()->back()->withErrors(['error' => 'Masih ada sesi kasir yang terbuka.']);
        }

        // Simpan saldo awal sebelum penambahan dana laci
        $saldoAwal = $laciSaldo->saldo;

        // Tambahkan dana laci ke saldo Laci
        $laciSaldo->increment('saldo', $request->dana_laci);

        SesiKasir::create([
            'tanggal' => now()->toDateString(),
            'shift_id' => $request->shift_id,
            'id_toko' => $idToko,
            'saldo_awal' => $saldoAwal,
            'dana_laci' => $request->dana_laci,
            'status' => 'buka',
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Sesi kasir berhasil dibuka.');
    }
    /**
     * Tutup sesi kasir.
     */

    public function tutupKasir(Request $request)
    {
        $sesiAktif = SesiKasir::where('status', 'buka')->first();
        if (!$sesiAktif) {
            return redirect()->back()->withErrors(['error' => 'Tidak ada sesi kasir yang terbuka.']);
        }

        $laciSaldo = TambahSaldo::where('nama_platform', 'Laci')->first();
        $saldoAkhir = $laciSaldo ? $laciSaldo->saldo : 0;

        $sesiAktif->update([
            'saldo_akhir' => $saldoAkhir,
            'status' => 'tutup',
        ]);

        if ($laciSaldo) {
            $laciSaldo->update(['saldo' => 0]);
        }

        return redirect()->back()->with('success', 'Sesi kasir berhasil ditutup.');
    }
    // public function tutupKasir(Request $request)
    // {
    //     // Cek sesi kasir yang masih terbuka
    //     $sesiAktif = SesiKasir::where('status', 'buka')->first();
    //     if (!$sesiAktif) {
    //         return redirect()->back()->withErrors(['error' => 'Tidak ada sesi kasir yang terbuka.']);
    //     }

    //     // Ambil total saldo terakhir dari tabel tambah_saldo
    //     $saldoAkhir = TambahSaldo::sum('saldo');

    //     // Update sesi kasir
    //     $sesiAktif->update([
    //         'saldo_akhir' => $saldoAkhir, // Gunakan total saldo dari tambah_saldo
    //         'status' => 'tutup',
    //     ]);

    //     // Reset dana laci di record dengan id = 1
    //     TambahSaldo::find(1)->update(['saldo' => 0]);

    //     return redirect()->back()->with('success', 'Sesi kasir berhasil ditutup.');
    // }

    /**
     * Tampilkan riwayat sesi kasir.
     */
    public function riwayatSesiKasir()
    {
        $sesiKasir = SesiKasir::with(['shift', 'toko', 'user'])->orderBy('tanggal', 'desc')->get();
        return view('sesi_kasir.riwayat', compact('sesiKasir'));
    }
}
