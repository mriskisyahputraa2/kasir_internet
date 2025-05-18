<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TambahSaldo;
use App\Models\Toko;
use App\Models\KaryawanUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;


class TambahSaldoController extends Controller
{

    public function index(Request $request)
    {
        $idToko = session('id_toko'); // Ambil ID toko dari sesi

        // Ambil parameter filter tanggal dari request
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // Query dasar
        $query = TambahSaldo::with(['toko', 'user'])
            ->where('id_toko', $idToko);

        // Filter berdasarkan tanggal
        if ($tanggalMulai && $tanggalAkhir) {
            $query->whereBetween('created_at', [
                Carbon::parse($tanggalMulai)->startOfDay(),
                Carbon::parse($tanggalAkhir)->endOfDay()
            ]);
        }

        // Ambil data
        $tambahSaldos = $query->get();

        // Ambil daftar toko dan user
        $tokoList = Toko::all();
        $userList = KaryawanUser::all();
        $toko = Toko::where('id', $idToko)->first();

        // Kirim data ke view
        return view('dashboard.saldo-awal', compact('tambahSaldos', 'tokoList', 'userList', 'toko', 'tanggalMulai', 'tanggalAkhir'));
    }
    public function store(Request $request)
    {
        $idToko = session('id_toko');

        $request->validate([
            'nama_platform' => 'required|string|max:255',
            'saldo' => 'required|string', // Diubah ke string agar bisa diproses dulu
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string'
        ]);

        // Hapus titik pada saldo sebelum disimpan ke database
        $cleanSaldo = str_replace('.', '', $request->saldo);

        // Pastikan saldo adalah angka valid
        if (!is_numeric($cleanSaldo)) {
            return redirect()->back()->withErrors(['saldo' => 'Saldo harus berupa angka valid.']);
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        TambahSaldo::create([
            'id_toko' => $idToko,
            'nama_platform' => $request->nama_platform,
            'saldo' => $cleanSaldo, // Pakai saldo yang sudah bersih
            'logo' => $logoPath,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('tambah-saldo.index')->with('success', 'Saldo berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $saldo = TambahSaldo::findOrFail($id);
        $idToko = session('id_toko');

        $request->validate([
            'nama_platform' => 'required|string|max:255',
            'saldo' => 'required|string', // Diubah ke string agar bisa diproses sebelum validasi angka
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string'
        ]);

        // Hapus format titik dari input saldo sebelum menyimpan ke database
        $cleanSaldo = str_replace('.', '', $request->saldo);

        // Pastikan saldo adalah angka valid
        if (!is_numeric($cleanSaldo)) {
            return redirect()->back()->withErrors(['saldo' => 'Saldo harus berupa angka valid.']);
        }

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $saldo->logo = $logoPath;
        }

        $saldo->update([
            'id_toko' => $idToko,
            'nama_platform' => $request->nama_platform,
            'saldo' => $cleanSaldo, // Gunakan saldo yang sudah dibersihkan
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('tambah-saldo.index')->with('success', 'Saldo berhasil diperbarui!');
    }


    public function destroy($id)
    {
        $saldo = TambahSaldo::findOrFail($id);

        // Hapus file logo dari storage jika ada
        if ($saldo->logo) {
            Storage::delete('storage/' . $saldo->logo);
        }

        // Hapus data saldo dari database
        $saldo->delete();

        return redirect()->route('tambah-saldo.index')->with('success', 'Saldo dan logo berhasil dihapus!');
    }
}
