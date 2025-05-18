<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PinjamanDana;
use App\Models\TambahSaldo;
use App\Models\Toko;
use Carbon\Carbon;

class PinjamanDanaController extends Controller
{
    // Menampilkan daftar pinjaman
    public function index(Request $request)
    {
        $idToko = session('id_toko'); // Ambil ID toko dari sesi

        // Ambil parameter filter dari request
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $status = $request->input('status');

        // Query dasar
        $query = PinjamanDana::with(['sumber', 'toko', 'creator'])
            ->where('id_toko', $idToko);

        // Filter berdasarkan tanggal
        if ($tanggalMulai && $tanggalAkhir) {
            $query->whereBetween('created_at', [
                Carbon::parse($tanggalMulai)->startOfDay(),
                Carbon::parse($tanggalAkhir)->endOfDay()
            ]);
        }

        // Filter berdasarkan status
        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }

        // Ambil data
        $data = $query->get();

        // Ambil saldo hanya dari toko yang dipilih
        $saldo = TambahSaldo::where('id_toko', $idToko)->get();
        $toko = Toko::where('id', $idToko)->first();

        // Kirim data ke view
        return view('dashboard.pinjaman_dana', compact('data', 'saldo', 'toko', 'tanggalMulai', 'tanggalAkhir', 'status'));
    }

    // Fungsi Ambil Saldo (Pinjam Dana)
    public function store(Request $request)
    {
        $idToko = session('id_toko');

        $request->validate([
            'dari' => 'required|exists:tambah_saldo,id',
            'nominal' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            $saldoDari = TambahSaldo::lockForUpdate()->findOrFail($request->dari);

            if ($saldoDari->saldo < $request->nominal) {
                return redirect()->back()->with('error', 'Saldo tidak mencukupi untuk dipinjam.');
            }

            $saldoAwal = $saldoDari->saldo;
            $saldoAkhir = $saldoAwal - $request->nominal;

            $saldoDari->update(['saldo' => $saldoAkhir]);

            PinjamanDana::create([
                'dari' => $request->dari,
                'saldo_awal' => $saldoAwal,
                'saldo_akhir' => $saldoAkhir,
                'nominal' => $request->nominal,
                'admin' => $request->admin ?? 0,
                'status' => 'dipinjam',
                'keterangan' => $request->keterangan,
                'id_toko' => $idToko,
                'create_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Saldo berhasil dipinjam.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Fungsi Kembalikan Saldo
    public function returnSaldo(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $pinjaman = PinjamanDana::findOrFail($id);
            $saldoTujuan = TambahSaldo::lockForUpdate()->findOrFail($request->tujuan);

            $saldoTujuan->update(['saldo' => $saldoTujuan->saldo + $pinjaman->nominal]);

            $pinjaman->update([
                'saldo_akhir' => $saldoTujuan->saldo,
                'status' => 'dikembalikan',
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Saldo berhasil dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Fungsi Hapus Pinjaman
    public function destroy($id)
    {
        $pinjaman = PinjamanDana::findOrFail($id);
        $pinjaman->delete();

        return redirect()->back()->with('success', 'Pinjaman berhasil dihapus.');
    }
}