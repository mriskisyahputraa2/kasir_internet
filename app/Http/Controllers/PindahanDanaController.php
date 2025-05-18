<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PindahanDana;
use App\Models\TambahSaldo;
use App\Models\Toko;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PindahanDanaController extends Controller
{
    public function index(Request $request)
    {
        $idToko = session('id_toko'); // Ambil ID toko dari sesi

        // Ambil parameter filter tanggal dari request
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // Query dasar
        $query = PindahanDana::with(['dariSaldo', 'tujuanSaldo', 'toko', 'user'])
            ->where('id_toko', $idToko);

        // Filter berdasarkan tanggal
        if ($tanggalMulai && $tanggalAkhir) {
            $query->whereBetween('created_at', [
                Carbon::parse($tanggalMulai)->startOfDay(),
                Carbon::parse($tanggalAkhir)->endOfDay()
            ]);
        }

        // Ambil data
        $data = $query->get();

        // Ambil saldo hanya dari toko yang dipilih
        $saldo = TambahSaldo::where('id_toko', $idToko)->get();
        $toko = Toko::where('id', $idToko)->first();

        // Kirim data ke view
        return view('dashboard.pindahan_dana', compact('data', 'saldo', 'toko', 'tanggalMulai', 'tanggalAkhir'));
    }



    public function store(Request $request)
    {
        $idToko = session('id_toko');

        $request->validate([
            'dari' => 'required|exists:tambah_saldo,id',
            'tujuan' => 'required|exists:tambah_saldo,id',
            'nominal' => 'required|numeric|min:1',
        ]);

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Ambil saldo awal dari sumber (dari) dan tujuan
            $saldoDari = TambahSaldo::lockForUpdate()->findOrFail($request->dari);
            $saldoTujuan = TambahSaldo::lockForUpdate()->findOrFail($request->tujuan);

            // Cek apakah saldo cukup untuk dipindahkan
            if ($saldoDari->saldo < $request->nominal) {
                return redirect()->back()->with('error', 'Saldo tidak mencukupi untuk dipindahkan.');
            }

            // Simpan saldo awal
            $saldoAwalDari = $saldoDari->saldo;
            $saldoAwalTujuan = $saldoTujuan->saldo;

            // Hitung saldo setelah transaksi
            $saldoAkhirDari = $saldoAwalDari - $request->nominal;
            $saldoAkhirTujuan = $saldoAwalTujuan + $request->nominal;

            // Update saldo di tabel tambah_saldo
            $saldoDari->update(['saldo' => $saldoAkhirDari]);
            $saldoTujuan->update(['saldo' => $saldoAkhirTujuan]);

            // Simpan transaksi pindahan dana
            PindahanDana::create([
                'dari' => $request->dari,
                'saldo_awal_dari' => $saldoAwalDari,
                'saldo_akhir_dari' => $saldoAkhirDari,
                'tujuan' => $request->tujuan,
                'saldo_awal_tujuan' => $saldoAwalTujuan,
                'saldo_akhir_tujuan' => $saldoAkhirTujuan,
                'id_toko' => $idToko,
                'nominal' => $request->nominal,
                'operasional' => $request->operasional ?? 0,
                'admin' => $request->admin ?? 0,
                'keterangan' => $request->keterangan,
                'create_by' => Auth::id(),
            ]);

            // Commit transaksi jika berhasil
            DB::commit();

            return redirect()->back()->with('success', 'Pindahan saldo berhasil ditambahkan.');
        } catch (\Exception $e) {
            // Rollback jika terjadi error
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'dari' => 'required',
            'tujuan' => 'required',
            'id_toko' => 'required',
            'nominal' => 'required|numeric',
        ]);

        $data = PindahanDana::findOrFail($id);
        $data->update($request->all());

        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        PindahanDana::destroy($id);
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}