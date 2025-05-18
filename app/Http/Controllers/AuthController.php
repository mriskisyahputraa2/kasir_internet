<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserToko;
use App\Models\Toko;
use App\Models\KaryawanUser;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            session(['user' => $user, 'role' => $user->role]);

            // Jika Superadmin, arahkan ke halaman pilih toko
            if ($user->role === 'superadmin') {
                return redirect()->route('pilih-toko');
            }

            // Jika Admin atau Kasir, arahkan langsung ke halaman absensi hari ini
            if ($user->role === 'admin' || $user->role === 'kasir') {
                $userToko = UserToko::where('id_user', $user->id)->first();

                if ($userToko) {
                    session(['id_toko' => $userToko->id_toko]);
                    return redirect()->route('absensi.hari-ini');
                }

                return redirect()->route('login')->withErrors(['error' => 'Anda belum terdaftar di toko mana pun.']);
            }

            $userToko = UserToko::where('id_user', $user->id)->first();

            if ($userToko) {
                session(['id_toko' => $userToko->id_toko]);
                return redirect()->route('dashboard');
            }

            return redirect()->route('login')->withErrors(['error' => 'Anda belum terdaftar di toko mana pun.']);
        }

        return redirect()->route('login')->withErrors(['loginError' => 'Username atau password salah.']);
    }

    public function pilihToko()
    {
        $tokoList = Toko::all();
        return view('auth.pilih-toko', compact('tokoList'));
    }

    public function setToko(Request $request)
    {
        $request->validate([
            'toko_id' => 'required|exists:toko,id'
        ]);

        session(['id_toko' => $request->toko_id]);
        return redirect()->route('dashboard');
    }

    public function absenLanjut(Request $request)
  {
    $user = Auth::user();
    $today = now()->toDateString(); // Tanggal hari ini

    // Cek apakah user sudah absen hari ini
    $existingAbsen = Absen::where('id_user', $user->id)
        ->where('tanggal_absen', $today)
        ->first();

    if ($existingAbsen) {
        return redirect()->route('dashboard')->with('error', 'Anda sudah absen hari ini.');
    }

    // Ambil id_toko dari session
    $idToko = session('id_toko');

    if (!$idToko) {
        return redirect()->route('absen')->withErrors(['error' => 'Toko tidak ditemukan. Silakan login kembali.']);
    }

    // Jika superadmin, langsung set status Hadir
    if ($user->role === 'superadmin') {
        Absen::create([
            'id_user' => $user->id,
            'tanggal_absen' => $today,
            'status' => 'Hadir',
            'id_toko' => $idToko,
            'id_shift' => null, // Superadmin tidak memiliki shift
            'waktu_absen' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Absen berhasil dicatat.');
    }

    // Ambil shift karyawan
    $shift = $user->shift;

    if (!$shift) {
        return redirect()->route('absen')->withErrors(['error' => 'Shift belum ditentukan.']);
    }

    // Hitung status absen (tepat waktu, telat, atau tidak hadir)
    $waktuAbsen = now();
    $waktuMulaiShift = \Carbon\Carbon::createFromFormat('H:i:s', $shift->waktu_mulai);
    $waktuAbsenCarbon = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $waktuAbsen);

    $status = 'Hadir';
    if ($waktuAbsenCarbon->gt($waktuMulaiShift->addMinutes(20))) {
        $status = 'Telat';
    }

    // Simpan data absen ke database
    Absen::create([
        'id_user' => $user->id,
        'tanggal_absen' => $today,
        'status' => $status,
        'id_toko' => $idToko,
        'id_shift' => $shift->id,
        'waktu_absen' => $waktuAbsen,
    ]);

    // Update jumlah absen karyawan
    $user->increment('jumlah_absen');

    // Redirect ke dashboard setelah absen
    return redirect()->route('dashboard')->with('success', 'Absen berhasil dicatat.');
  }

    public function logout(Request $request)
    {
        Auth::logout();
        session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('message', 'Anda telah logout.');
    }
}
