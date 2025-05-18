<?php
namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Toko;

class KategoriController extends Controller
{
    public function index()
    {
        $idToko = session('id_toko'); // Ambil ID toko dari session
        if (!$idToko) {
            return redirect()->back()->with('error', 'Toko tidak ditemukan.');
        }

        $kategoris = Kategori::where('id_toko', $idToko)->get();
        $toko = Toko::where('id', $idToko)->first();
        return view('product.kategori', compact('kategoris', 'toko'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Kategori::create([
            'id_toko' => session('id_toko'),
            'name' => $request->name,
            'createBy' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $idToko = session('id_toko');
        $kategori = Kategori::where('id', $id)->where('id_toko', $idToko)->first();

        if (!$kategori) {
            return redirect()->back()->with('error', 'Kategori tidak ditemukan atau bukan milik toko Anda.');
        }

        $kategori->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $idToko = session('id_toko');
        $kategori = Kategori::where('id', $id)->where('id_toko', $idToko)->first();

        if (!$kategori) {
            return redirect()->route('kategori.index')->with('error', 'Kategori tidak ditemukan atau bukan milik toko Anda.');
        }

        $kategori->delete();
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}