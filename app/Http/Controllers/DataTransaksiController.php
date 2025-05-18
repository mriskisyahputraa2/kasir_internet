<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataTransaksi;
use App\Models\TambahSaldo;
use App\Models\Toko;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DataTransaksiController extends Controller
{
    /**
     * Menampilkan daftar transaksi.
     */
    public function index(Request $request)
    {
        $toko = null;
        $idToko = $request->id_toko ?? session('id_toko');

        if ($idToko) {
            $toko = Toko::find($idToko);
        }

        // Filter transaksi berdasarkan id_toko dan urutkan berdasarkan created_at DESC
        $transaksis = DataTransaksi::with(['sumberDana', 'terimaDana', 'toko'])
            ->where('id_toko', $idToko)
            ->orderBy('created_at', 'desc') // Data terbaru di atas
            ->get();

        // Filter saldo dan toko yang sesuai, urutkan berdasarkan created_at DESC
        $tambahSaldos = TambahSaldo::with('toko', 'user')
            ->where('id_toko', $idToko)
            ->orderBy('created_at', 'desc') // Data terbaru di atas
            ->get();

        // Ambil semua toko untuk pilihan
        $tokos = Toko::all();

        // Hitung total saldo
        $totalSaldo = $tambahSaldos->sum('saldo');

        return view('kasir', compact('transaksis', 'tambahSaldos', 'tokos', 'idToko', 'totalSaldo', 'toko'));
    }

    /**
     * Menyimpan transaksi baru.
     */
    public function store(Request $request)
    {
        $idToko = session('id_toko');

        // Fungsi untuk mengonversi format Rupiah ke angka
        function convertToAngka($rupiah)
        {
            return (float) str_replace(['Rp ', '.'], '', $rupiah);
        }

        // Konversi nilai dari format Rupiah ke angka
        $request->merge([
            'nominal_transaksi' => convertToAngka($request->nominal_transaksi),
            'admin_dalam' => convertToAngka($request->admin_dalam),
            'admin_luar' => convertToAngka($request->admin_luar),
            'admin_bank' => convertToAngka($request->admin_bank),
        ]);

        // Validasi input
        $request->validate([
            'nominal_transaksi' => 'required|numeric|min:1',
            'jenis_transaksi' => 'required|string',
            'tipe_transaksi' => 'nullable|string',
            'sumber_dana' => 'required|exists:tambah_saldo,id',
            'terima_dana' => 'required|exists:tambah_saldo,id',
            'admin_dalam' => 'nullable|numeric|min:0',
            'admin_luar' => 'nullable|numeric|min:0',
            'admin_bank' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ], [
            'jenis_transaksi.required' => 'Jenis transaksi wajib diisi.',
            'tipe_transaksi.required' => 'Tipe transaksi wajib diisi.',
            'sumber_dana.required' => 'Sumber dana wajib dipilih.',
            'terima_dana.required' => 'Penerima dana wajib dipilih.',
            'terima_dana.different' => 'Sumber dana dan penerima dana tidak boleh sama.',
        ]);

        // Gunakan transaction untuk menjaga konsistensi data
        DB::beginTransaction();

        try {
            // Ambil saldo sumber & tujuan
            $sumberSaldo = TambahSaldo::findOrFail($request->sumber_dana);
            $terimaSaldo = TambahSaldo::findOrFail($request->terima_dana);

            // Simpan saldo awal
            $dana_awal_sumber = $sumberSaldo->saldo;
            $dana_awal_terima = $terimaSaldo->saldo;

            // Hitung nominal utama
            $nominal = (float) $request->nominal_transaksi;
            $admin_dalam = (float) $request->admin_dalam;
            $admin_luar = (float) $request->admin_luar;
            $admin_bank = (float) $request->admin_bank;

            // Hitung total yang dipotong dari sumber (nominal transaksi saja)
            $totalDipotong = $nominal;

            if ($sumberSaldo->saldo < $totalDipotong) {
                return redirect()->back()->withErrors(['saldo' => 'Saldo tidak mencukupi untuk transaksi ini.']);
            }

            // **Kurangi saldo sumber hanya berdasarkan nominal transaksi**
            $dana_akhir_sumber = $dana_awal_sumber - $totalDipotong;

            // **Tambahkan admin dalam ke saldo sumber (karena tetap ada di sumber)**
            $dana_akhir_sumber += $admin_dalam;

            // **Tambah saldo tujuan sesuai nominal transaksi**
            $dana_akhir_terima = $dana_awal_terima + $nominal;

            // Update saldo sumber dan tujuan
            $sumberSaldo->update(['saldo' => $dana_akhir_sumber]);
            $terimaSaldo->update(['saldo' => $dana_akhir_terima]);

            // Jika ada admin luar atau admin bank, tambahkan ke saldo "Laci"
            if ($admin_luar > 0 || $admin_bank > 0) {
                $laciSaldo = TambahSaldo::where('nama_platform', 'Laci')->first();
                if ($laciSaldo) {
                    $totalAdminLuarBank = $admin_luar + $admin_bank;
                    $laciSaldo->increment('saldo', $totalAdminLuarBank);
                }
            }

            // Simpan transaksi
            $user = Auth::user();
            $tipe_transaksi = $request->tipe_transaksi ?? '-';
            DataTransaksi::create([
                'nomor_transaksi' => 'TRX' . now()->timestamp . rand(100, 999),
                'nominal_transaksi' => $nominal,
                'jenis_transaksi' => $request->jenis_transaksi,
                'sumber_dana' => $request->sumber_dana,
                'terima_dana' => $request->terima_dana,
                'dana_awal_sumber' => $dana_awal_sumber,
                'dana_akhir_sumber' => $dana_akhir_sumber,
                'dana_awal_terima' => $dana_awal_terima,
                'dana_akhir_terima' => $dana_akhir_terima,
                'admin_dalam' => $admin_dalam,
                'admin_luar' => $admin_luar,
                'admin_bank' => $admin_bank,
                'tipe_transaksi' => $tipe_transaksi,
                'id_toko' => $idToko,
                'keterangan' => $request->keterangan,
                'created_by' => $user->id,
            ]);

            // Commit transaksi jika semua berhasil
            DB::commit();

            return redirect()->route('kasir.transaksi')->with('success', 'Transaksi berhasil ditambahkan.');
        } catch (\Exception $e) {
            // Rollback jika ada kesalahan
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Hapus transaksi.
     */
    public function destroy($id)
    {
        // Cari transaksi berdasarkan ID
        $dataTransaksi = DataTransaksi::find($id);

        // Jika transaksi tidak ditemukan, kembalikan response error
        if (!$dataTransaksi) {
            return redirect()->route('kasir.index')->with('error', 'Transaksi tidak ditemukan.');
        }

        // Pastikan sumber dan tujuan saldo ditemukan
        $sumberDana = $dataTransaksi->sumberDana;
        $terimaDana = $dataTransaksi->terimaDana;

        if (!$sumberDana || !$terimaDana) {
            return redirect()->route('kasir.index')->with('error', 'Sumber atau tujuan saldo tidak ditemukan.');
        }

        // Hapus transaksi
        $dataTransaksi->delete();

        return redirect()->route('kasir.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
