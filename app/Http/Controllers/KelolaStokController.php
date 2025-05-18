<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Toko;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class KelolaStokController extends Controller
{
    public function index(Request $request)
    {
        $idToko = session('id_toko');
        if (!$idToko) {
            return redirect()->back()->with('error', 'Toko tidak ditemukan.');
        }

        // Ambil data toko berdasarkan id_toko
        $toko = Toko::find($idToko);
        if (!$toko) {
            return redirect()->back()->with('error', 'Toko tidak ditemukan.');
        }

        // Ambil data kategori
        $kategoris = Kategori::where('id_toko', $idToko)->get();

        // Query dasar untuk produk dengan stok ≤ 5 atau kadaluarsa ≤ 5 hari lagi
        $produks = Produk::where('id_toko', $idToko)
            ->where(function ($query) {
                $query->where('stok', '<=', 5) // Stok sedikit (≤ 5)
                    ->orWhereDate('tgl_kadaluarsa', '<=', Carbon::now()->addDays(5)); // Kadaluarsa ≤ 5 hari lagi
            })
            ->with('kategoriRelasi')
            ->get();

        // Kirim data ke view
        return view('product.kelola-stok', compact('produks', 'toko', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|exists:kategoris,id',
            'stok' => 'required|integer|min:0',
            'tgl_kadaluarsa' => 'nullable|date',
        ]);

        $produk = Produk::where('id', $id)->where('id_toko', session('id_toko'))->first();
        if (!$produk) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan atau bukan milik toko Anda.');
        }

        // Update foto jika ada
        if ($request->hasFile('foto')) {
            if ($produk->foto) {
                Storage::disk('public')->delete($produk->foto);
            }
            $produk->foto = $request->file('foto')->store('produk', 'public');
        }

        // Update data produk
        $produk->update($request->except('foto'));

        // Redirect ke halaman produk jika stok > 5 dan tanggal kadaluarsa > 5 hari lagi
        if ($produk->stok > 5 && Carbon::parse($produk->tgl_kadaluarsa)->gt(Carbon::now()->addDays(5))) {
            return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui dan dihapus dari kelola stok.');
        }

        return redirect()->back()->with('success', 'Produk berhasil diperbarui.');
    }
}