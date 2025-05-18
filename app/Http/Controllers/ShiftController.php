<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Toko;

class ShiftController extends Controller
{
    // Menampilkan semua data shift
    public function index()
    {
        $toko = null;
        if (session()->has('id_toko')) {
            $toko = Toko::find(session('id_toko'));
        }
        $shifts = Shift::all();
        return view('pengaturan.shift', compact('shifts', 'toko'));
    }

    // Menampilkan form tambah shift
    public function create()
    {
        return view('shift.create');
    }

    // Menyimpan data shift baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_shift' => 'required|string|max:255',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
        ]);

        Shift::create($request->all());

        return redirect()->route('shift.index')->with('success', 'Shift berhasil ditambahkan.');
    }

    // Menampilkan form edit shift
    public function edit($id)
    {
        $shift = Shift::findOrFail($id);
        return view('shift.edit', compact('shift'));
    }

    // Mengupdate data shift
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_shift' => 'required|string|max:255',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
        ]);

        $shift = Shift::findOrFail($id);
        $shift->update($request->all());

        return redirect()->route('shift.index')->with('success', 'Shift berhasil diperbarui.');
    }

    // Menghapus data shift
    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();

        return redirect()->route('shift.index')->with('success', 'Shift berhasil dihapus.');
    }
}