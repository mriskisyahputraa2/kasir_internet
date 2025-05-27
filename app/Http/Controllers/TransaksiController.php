<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Support\Facades\Auth;
use App\Models\TambahSaldo;

class TransaksiController extends Controller
{
    /**
     * Menampilkan halaman khusus transaksi "Bayar Nanti".
     */
    public function bayarNanti()
    {
        $idToko = session('id_toko');
        $transaksis = Transaksi::where('id_toko', $idToko)->where('status_pembayaran', 'Bayar Nanti')->get();
        return view('transaksi.bayar_nanti', compact('transaksis'));
    }

    /**
     * Menampilkan halaman pilih barang.
     */
    public function index()
    {
        $idToko = session('id_toko');
        $produks = Produk::where('id_toko', $idToko)->get();
        $transaksis = Transaksi::where('id_toko', $idToko)->where('status_pembayaran', 'Bayar Nanti')->get();
        return view('transaksi.index', compact('produks', 'transaksis'));
    }

    /**
     * Menyimpan transaksi sementara (tanpa pembayaran).
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'produk_id.*' => 'required|exists:produks,id',
        //     'jumlah.*' => 'required|integer|min:1',
        //     'status_pembayaran' => 'required|in:Belum Lunas,Bayar Nanti',
        // ]);
        $request->validate(
            [
                'produk_id' => 'required|array|min:1',
                'produk_id.*' => 'required|exists:produks,id',
                'jumlah' => 'required|array|min:1',
                'jumlah.*' => 'required|integer|min:1',
                'status_pembayaran' => 'required|in:Belum Lunas,Bayar Nanti',
            ],
            [
                'produk_id.required' => 'Silakan tambahkan minimal satu produk sebelum memproses transaksi.',
                'jumlah.required' => 'Jumlah produk tidak boleh kosong.',
            ],
        );

        $idToko = session('id_toko');
        $totalHarga = 0;

        // Buat transaksi baru
        $transaksi = Transaksi::create([
            'id_toko' => $idToko,
            'createBy' => Auth::id(),
            'total_harga' => 0,
            'bayar' => 0,
            'kembalian' => 0,
            'diskon' => 0,
            'status_pembayaran' => $request->status_pembayaran,
        ]);

        foreach ($request->produk_id as $key => $produkId) {
            $produk = Produk::findOrFail($produkId);
            $jumlah = $request->jumlah[$key];
            $hargaSetelahDiskon = $produk->harga_jual - ($produk->diskon_global ?? 0);
            $subtotal = $hargaSetelahDiskon * $jumlah;

            TransaksiDetail::create([
                'transaksi_id' => $transaksi->id,
                'produk_id' => $produkId,
                'jumlah' => $jumlah,
                'harga_satuan' => $produk->harga_jual,
                'diskon' => $produk->diskon_global ?? 0,
                'total_harga' => $hargaSetelahDiskon,
                'subtotal' => $subtotal,
            ]);

            $totalHarga += $subtotal;

            // Kurangi stok produk
            $produk->decrement('stok', $jumlah);
        }

        // Update total harga
        $transaksi->update([
            'total_harga' => $totalHarga,
        ]);

        // Jika status "Bayar Nanti", simpan transaksi dan kembali ke halaman awal
        if ($request->status_pembayaran === 'Bayar Nanti') {
            return redirect()->route('kasir.transaksi')->with('success', 'Transaksi disimpan sebagai Bayar Nanti.');
        }

        // Jika status "Belum Lunas", arahkan ke halaman pembayaran
        return redirect()->route('transaksi.pembayaran', $transaksi->id);
    }

    /**
     * Menampilkan halaman pembayaran.
     */
    public function pembayaran($id)
    {
        $transaksi = Transaksi::with('details.produk')->findOrFail($id);
        // Hitung total diskon dari semua detail
        $totalDiskon = $transaksi->details->sum(function ($detail) {
            return ($detail->diskon ?? 0) * $detail->jumlah;
        });

        return view('transaksi.pembayaran', compact('transaksi', 'totalDiskon'));
    }
    /**
     * Menyelesaikan pembayaran.
     */
    public function completePayment(Request $request, $id)
    {
        // $request->validate([
        //     'bayar' => 'required|string',
        //     'diskon' => 'nullable|string',
        // ]);

        // // Fungsi untuk mengonversi format Rupiah ke angka
        // function convertToAngka($rupiah)
        // {
        //     return (int) str_replace(['Rp ', '.'], '', $rupiah);
        // }

        // // Konversi input diskon dan bayar ke angka
        // $diskon = $request->diskon ? convertToAngka($request->diskon) : 0;
        // $bayar = convertToAngka($request->bayar);

        // // Ambil data transaksi
        // $transaksi = Transaksi::findOrFail($id);
        // $totalHarga = $transaksi->total_harga;

        // // Hitung total setelah diskon
        // $totalSetelahDiskon = $totalHarga - $diskon;

        // // Update transaksi
        // $transaksi->update([
        //     'diskon' => $diskon,
        //     'total_harga' => $totalSetelahDiskon,
        //     'bayar' => $bayar,
        //     'kembalian' => $bayar - $totalSetelahDiskon,
        //     'status_pembayaran' => 'Lunas',
        // ]);

        // // Tambahkan total pembayaran ke saldo tujuan
        // $tujuanSaldo = TambahSaldo::find(1);
        // if ($tujuanSaldo) {
        //     $tujuanSaldo->increment('saldo', $totalSetelahDiskon);
        // }

        $request->validate([
            'bayar' => 'required|string',
        ]);

        function convertToAngka($rupiah)
        {
            return (int) str_replace(['Rp ', '.'], '', $rupiah);
        }

        $bayar = convertToAngka($request->bayar);

        $transaksi = Transaksi::with('details')->findOrFail($id);

        // Hitung total diskon dari detail
        $totalDiskon = $transaksi->details->sum(function ($detail) {
            return ($detail->diskon ?? 0) * $detail->jumlah;
        });

        $totalHargaAwal = $transaksi->total_harga + $totalDiskon;
        $totalSetelahDiskon = $totalHargaAwal - $totalDiskon;

        $transaksi->update([
            'diskon' => $totalDiskon,
            'total_harga' => $totalSetelahDiskon,
            'bayar' => $bayar,
            'kembalian' => $bayar - $totalSetelahDiskon,
            'status_pembayaran' => 'Lunas',
        ]);

        $tujuanSaldo = TambahSaldo::find(1);
        if ($tujuanSaldo) {
            $tujuanSaldo->increment('saldo', $totalSetelahDiskon);
        }
        return redirect()->route('kasir.transaksi')->with('success', 'Pembayaran berhasil dilanjutkan!');
    }

    /**
     * Menampilkan daftar transaksi "Bayar Nanti".
     */
    public function listBayarNanti()
    {
        $idToko = session('id_toko');
        $transaksis = Transaksi::where('id_toko', $idToko)->where('status_pembayaran', 'Bayar Nanti')->get();
        return view('transaksi.bayar_nanti', compact('transaksis'));
    }

    /**
     * Melanjutkan pembayaran transaksi "Bayar Nanti".
     */
    public function lanjutkanPembayaran($id)
    {
        // $transaksi = Transaksi::with('details.produk')->findOrFail($id);
        // return view('transaksi.pembayaran', compact('transaksi'));
        $transaksi = Transaksi::with('details.produk')->findOrFail($id);
        $totalDiskon = $transaksi->details->sum(function ($detail) {
            return ($detail->diskon ?? 0) * $detail->jumlah;
        });
        return view('transaksi.pembayaran', compact('transaksi', 'totalDiskon'));
    }

    /**
     * Menyimpan transaksi sebagai Bayar Nanti
     */
    // Untuk Bayar Nanti
    public function bayarNantiStore(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        // Pastikan hanya mengupdate status pembayaran
        $transaksi->update([
            'status_pembayaran' => 'Bayar Nanti',
            'bayar' => 0,
            'kembalian' => 0,
            'diskon' => 0,
        ]);

        return redirect()->route('kasir.transaksi')->with('success', 'Transaksi disimpan sebagai Bayar Nanti.');
    }

    // Untuk Cancel
    public function cancel(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        // Kembalikan stok produk
        foreach ($transaksi->details as $detail) {
            $produk = Produk::find($detail->produk_id);
            if ($produk) {
                $produk->increment('stok', $detail->jumlah);
            }
        }

        // Hapus transaksi
        $transaksi->delete();

        return redirect()->route('kasir.transaksi')->with('success', 'Transaksi dibatalkan.');
    }
}
