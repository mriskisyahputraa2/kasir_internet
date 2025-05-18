<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Http\Request;

class DataToko extends Controller
{
    public function index()
    {
        // Ambil data toko berdasarkan session
        $toko = null;
        if (session()->has('id_toko')) {
            $toko = Toko::find(session('id_toko'));
        }
        $tokos = Toko::all();
        return view('pengaturan.toko', compact('tokos', 'toko'));
    }

    // Menampilkan form tambah toko
    public function create()
    {
        return view('pengaturan.toko_create');
    }

    // Menyimpan toko baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'transaksi' => 'required|numeric',
            'total_karyawan' => 'required|integer',
            'keterangan' => 'nullable|string',
        ]);

        Toko::create($request->all());

        return redirect()->back()->with('success', 'Toko berhasil ditambahkan');
    }

    // Menampilkan data toko yang akan diedit
    public function edit(Toko $toko)
    {
        return view('pengaturan.toko_edit', compact('toko'));
    }

    // Memperbarui data toko
    public function update(Request $request, Toko $toko)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'transaksi' => 'required|numeric',
            'total_karyawan' => 'required|integer',
            'keterangan' => 'nullable|string',
        ]);

        $toko->update($request->all());

        return redirect()->back()->with('success', 'Toko berhasil diperbarui');
    }

    // Menghapus toko
    public function destroy(Toko $toko)
    {
        $toko->delete();
        return redirect()->back()->with('success', 'Toko berhasil dihapus');
    }
}