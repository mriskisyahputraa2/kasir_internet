<?php
namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Toko;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $idToko = session('id_toko');
        if (!$idToko) {
            return redirect()->back()->with('error', 'Toko tidak ditemukan.');
        }

        // Ambil data kategori
        $kategoris = Kategori::where('id_toko', $idToko)->get();

        // Ambil parameter filter dari request
        $kategoriFilter = $request->input('kategori');
        $barcodeFilter = $request->input('barcode');

        // Query dasar
        $query = Produk::where('id_toko', $idToko)
            ->with('kategoriRelasi');

        // Filter berdasarkan kategori
        if ($kategoriFilter) {
            $query->where('kategori', $kategoriFilter);
        }

        // Filter berdasarkan barcode
        if ($barcodeFilter) {
            $query->where('barcode', 'like', '%' . $barcodeFilter . '%');
        }

        // Ambil data produk
        $produks = $query->get();
        $toko = Toko::where('id', $idToko)->first();
        // Kirim data ke view
        return view('product.produk', compact('produks', 'kategoris', 'kategoriFilter', 'barcodeFilter', 'toko'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|exists:kategoris,id',
            'barcode' => 'nullable|string|max:50|unique:produks,barcode',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'tgl_kadaluarsa' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'diskon_global' => 'nullable|numeric|min:0',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('produk', 'public');
        }

        Produk::create([
            'id_toko' => session('id_toko'),
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'barcode' => $request->barcode ?? '-',
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'tgl_kadaluarsa' => $request->tgl_kadaluarsa,
            'foto' => $fotoPath,
            'diskon_global' => $request->diskon_global ?? 0,
            'createBy' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|exists:kategoris,id',
            'barcode' => 'nullable|string|max:50|unique:produks,barcode,' . $id,
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'tgl_kadaluarsa' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'diskon_global' => 'nullable|numeric|min:0|max:100',
        ]);

        $produk = Produk::where('id', $id)->where('id_toko', session('id_toko'))->first();
        if (!$produk) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan atau bukan milik toko Anda.');
        }

        if ($request->hasFile('foto')) {
            if ($produk->foto) {
                Storage::disk('public')->delete($produk->foto);
            }
            $produk->foto = $request->file('foto')->store('produk', 'public');
        }

        $produk->update($request->except('foto'));

        return redirect()->back()->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $produk = Produk::where('id', $id)->where('id_toko', session('id_toko'))->first();
        if (!$produk) {
            return redirect()->route('produk.index')->with('error', 'Produk tidak ditemukan atau bukan milik toko Anda.');
        }

        if ($produk->foto) {
            Storage::disk('public')->delete($produk->foto);
        }

        $produk->delete();
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}
