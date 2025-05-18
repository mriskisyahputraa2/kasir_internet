<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataTransaksi;
use App\Models\TambahSaldo;
use App\Models\Toko;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\SesiKasir;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class KasirTransaksiController extends Controller
{
    public function index(Request $request)
    {
        $toko = null;
        $idToko = $request->id_toko ?? session('id_toko');

        if ($idToko) {
            $toko = Toko::find($idToko);
        }

        // Get active cashier session
        $sesiAktif = SesiKasir::where('status', 'buka')->first();

        // Data for cashier section
        $transaksisKasir = DataTransaksi::with(['sumberDana', 'terimaDana', 'toko'])
            ->where('id_toko', $idToko)
            ->orderBy('created_at', 'desc')
            ->get();

        $tambahSaldos = TambahSaldo::with('toko', 'user')
            ->where('id_toko', $idToko)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSaldo = $tambahSaldos->sum('saldo');

        // Data for transaction section
        $produks = Produk::where('id_toko', $idToko)->get();
        $kategoris = Kategori::where('id_toko', $idToko)->get();
        $transaksis = Transaksi::where('id_toko', $idToko)
            ->where('status_pembayaran', 'Bayar Nanti')
            ->get();

        // Get shifts for open cashier form
        $shifts = Shift::all();
        $totalSaldoTambahSaldo = TambahSaldo::sum('saldo');
        return view('kasir_transaksi', compact(
            'transaksisKasir',
            'tambahSaldos',
            'toko',
            'totalSaldo',
            'produks',
            'kategoris',
            'transaksis',
            'sesiAktif',
            'shifts',
            'totalSaldoTambahSaldo'
        ));
    }

    public function bukaKasir(Request $request)
    {
        $request->validate([
            'shift_id' => 'required|exists:shift,id',
            'dana_laci' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $idToko = session('id_toko');

        if (SesiKasir::where('status', 'buka')->exists()) {
            return back()->with('error', 'Masih ada sesi kasir yang terbuka');
        }

        $totalSaldo = TambahSaldo::sum('saldo');
        $saldoAwal = $totalSaldo + $request->dana_laci;

        // Update cash drawer
        TambahSaldo::find(1)?->update(['saldo' => $request->dana_laci]);

        // Create new session
        SesiKasir::create([
            'tanggal' => now()->toDateString(),
            'shift_id' => $request->shift_id,
            'id_toko' => $idToko,
            'saldo_awal' => $saldoAwal,
            'dana_laci' => $request->dana_laci,
            'status' => 'buka',
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'Sesi kasir berhasil dibuka');
    }

    public function tutupKasir()
    {
        $sesiAktif = SesiKasir::where('status', 'buka')->first();

        if (!$sesiAktif) {
            return back()->with('error', 'Tidak ada sesi kasir yang terbuka');
        }

        // Calculate final balance from tambah_saldo table
        $saldoAkhir = TambahSaldo::sum('saldo');

        // Update session
        $sesiAktif->update([
            'saldo_akhir' => $saldoAkhir,
            'status' => 'tutup',
        ]);

        // Reset cash drawer
        TambahSaldo::find(1)?->update(['saldo' => 0]);

        return back()->with('success', 'Sesi kasir berhasil ditutup');
    }
}
