<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\KaryawanUser;
use App\Models\Shift;
use App\Models\Toko;
use App\Models\UserToko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DataKaryawan extends Controller
{
    // Menampilkan data karyawan
     // Menampilkan data karyawan
     public function index()
    {
          $toko = null;
    if (session()->has('id_toko')) {
        $toko = Toko::find(session('id_toko'));
    }

    $karyawans = KaryawanUser::with('toko')->get(); // Ambil data karyawan beserta toko
    $tokos = Toko::all(); // Ambil semua toko untuk dropdown
    $shifts = Shift::all();

    // Ambil data absen untuk setiap karyawan
    $today = now()->toDateString();
    foreach ($karyawans as $karyawan) {
        $karyawan->absen_hari_ini = Absen::where('id_user', $karyawan->id)
            ->where('tanggal_absen', $today)
            ->first();
    }

    return view('pengaturan.karyawan', compact('karyawans', 'toko', 'tokos', 'shifts'));
     }

     // Menyimpan data karyawan baru
     public function store(Request $request)
     {
         $request->validate([
             'nama' => 'required|string|max:255',
             'username' => 'required|string|max:255|unique:karyawan_users,username',
             'password' => 'required|string|min:6',
             'role' => 'required|in:superadmin,admin,kasir',
             'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
            'id_shift' => 'nullable|exists:shift,id', // Validasi id_shift

         ]);

         // Simpan foto jika ada
         $fotoPath = null;
         if ($request->hasFile('foto')) {
             $fotoPath = $request->file('foto')->store('foto_karyawan', 'public');
         }

         // Buat data karyawan baru
         KaryawanUser::create([
             'nama' => $request->nama,
             'username' => $request->username,
             'password' => bcrypt($request->password),
             'role' => $request->role,
             'foto' => $fotoPath,
            'id_shift' => $request->id_shift,

         ]);

         return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil ditambahkan.');
     }

     // Menampilkan form edit karyawan
public function edit($id)
{
    $karyawan = KaryawanUser::findOrFail($id);
    return response()->json($karyawan); // Mengembalikan data karyawan dalam format JSON
}

// Mengupdate data karyawan
public function update(Request $request, $id)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:karyawan_users,username,' . $id,
        'password' => 'nullable|string|min:6',
        'role' => 'required|in:superadmin,admin,kasir',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
        'id_shift' => 'nullable|exists:shift,id', // Validasi id_shift
    ]);

    $karyawan = KaryawanUser::findOrFail($id);

    // Update foto jika ada
    $fotoPath = $karyawan->foto;
    if ($request->hasFile('foto')) {
        // Hapus foto lama jika ada
        if ($fotoPath) {
            Storage::disk('public')->delete($fotoPath);
        }
        $fotoPath = $request->file('foto')->store('foto_karyawan', 'public');
    }

    // Update data karyawan
    $data = [
        'nama' => $request->nama,
        'username' => $request->username,
        'role' => $request->role,
        'foto' => $fotoPath,
        'id_shift' => $request->id_shift, // Ambil nilai id_shift dari request
    ];

    // Update password hanya jika diisi
    if ($request->filled('password')) {
        $data['password'] = bcrypt($request->password);
    }

    $karyawan->update($data);

    return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
}

     // Menyimpan pilihan toko untuk karyawan
     public function pilihToko(Request $request, $id)
     {
         // Validasi role superadmin
         if (auth()->user()->role !== 'superadmin') {
             return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk fitur ini.');
         }

         $request->validate([
             'id_toko' => 'required|exists:toko,id',
         ]);

         // Simpan atau update hubungan karyawan dan toko
         UserToko::updateOrCreate(
             ['id_user' => $id], // ID karyawan
             ['id_toko' => $request->id_toko] // ID toko yang dipilih
         );

         return redirect()->route('karyawan.index')->with('success', 'Toko berhasil dipilih untuk karyawan.');
     }

     // Menghapus data karyawan
     public function destroy($id)
     {
         $karyawan = KaryawanUser::findOrFail($id);

         // Hapus foto jika ada
         if ($karyawan->foto) {
             Storage::disk('public')->delete($karyawan->foto);
         }

         $karyawan->delete();

         return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil dihapus.');
     }

}