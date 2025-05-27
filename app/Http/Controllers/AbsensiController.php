<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\Shift;
use App\Models\KaryawanUser;
use App\Models\Toko;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    // Tampilan absensi hari ini
    public function absensiHariIni()
    {
        $user = auth()->user();
        $today = now()->toDateString();
        $currentTime = now()->setTimezone('Asia/Jakarta');

        $absen = Absen::with(['shift', 'toko'])
            ->where('id_user', $user->id)
            ->where('tanggal_absen', $today)
            ->first();

        $shift = $user->shift;

        $showMasuk = false;
        $showKeluar = false;
        $errorMessage = null;
        $autoKeluar = false;

        if (!$shift) {
            $errorMessage = "Shift belum ditentukan. Silakan hubungi superadmin.";
        } else {
            $shiftStart = Carbon::createFromFormat('H:i:s', $shift->waktu_mulai, 'Asia/Jakarta');
            $shiftEnd = Carbon::createFromFormat('H:i:s', $shift->waktu_selesai, 'Asia/Jakarta');

            // Jika shift melewati tengah malam
            if ($shiftEnd->lessThan($shiftStart)) {
                $shiftEnd->addDay();
            }

            // Cek apakah sekarang sudah melewati waktu shift berakhir
            if ($currentTime->greaterThan($shiftEnd)) {
                $autoKeluar = true;

                // Jika belum ada absen keluar, update otomatis
                if ($absen && !$absen->waktu_keluar) {
                    $absen->update([
                        'waktu_keluar' => $shiftEnd,
                        'lokasi' => request()->ip()
                    ]);
                    $absen->refresh();
                }
            }

            // Cek apakah sekarang dalam jam kerja
            if ($currentTime->between($shiftStart, $shiftEnd)) {
                if (!$absen) {
                    $showMasuk = true;
                } elseif ($absen && !$absen->waktu_keluar) {
                    $showKeluar = true;
                }
            } else {
                $errorMessage = "Tidak bisa absen karena bukan jam kerja Anda (Shift: {$shift->nama_shift} {$shift->waktu_mulai} - {$shift->waktu_selesai})";
            }
        }

        return view('absensi.absensi-hari-ini', compact(
            'absen',
            'shift',
            'showMasuk',
            'showKeluar',
            'errorMessage',
            'autoKeluar'
        ));
    }

    // Proses absen masuk
    public function absenMasuk(Request $request)
    {
        $user = auth()->user();
        $today = now()->toDateString();
        $currentTime = now()->setTimezone('Asia/Jakarta');

        if (session()->has('id_toko')) {
            $toko = \App\Models\Toko::find(session('id_toko'));
        }


        // Validasi apakah sudah absen hari ini
        $existingAbsen = Absen::where('id_user', $user->id)
            ->where('tanggal_absen', $today)
            ->first();

        if ($existingAbsen) {
            return redirect()
                ->route('absensi.hari-ini')
                ->with('error', 'Anda sudah absen hari ini.');
        }

        // Validasi shift
        $shift = $user->shift;
        if (!$shift) {
            return redirect()
                ->route('absensi.hari-ini')
                ->with('error', 'Shift belum ditentukan. Silakan hubungi superadmin.');
        }

        $shiftStart = Carbon::createFromFormat('H:i:s', $shift->waktu_mulai, 'Asia/Jakarta');
        $shiftEnd = Carbon::createFromFormat('H:i:s', $shift->waktu_selesai, 'Asia/Jakarta');

        // Jika shift melewati tengah malam
        if ($shiftEnd->lessThan($shiftStart)) {
            $shiftEnd->addDay();
        }

        // Cek apakah dalam jam kerja
        if (!$currentTime->between($shiftStart, $shiftEnd)) {
            // Buat record dengan status "Tidak Bekerja"
            Absen::create([
                'id_user' => $user->id,
                'tanggal_absen' => $today,
                'waktu_absen' => $currentTime,
                'id_toko' => session('id_toko'),
                'id_shift' => $shift->id,
                'status' => 'Tidak Bekerja',
                'lokasi' => $toko ? $toko->lokasi : '',
            ]);

            return redirect()
                ->route('absensi.hari-ini')
                ->with('error', 'Tidak bisa absen karena bukan jam kerja Anda (Shift: ' . $shift->waktu_mulai . ' - ' . $shift->waktu_selesai . ')');
        }

        // Tentukan status berdasarkan keterlambatan
        $status = 'Hadir';
        $waktuMulaiCompare = Carbon::createFromFormat('H:i:s', $shift->waktu_mulai, 'Asia/Jakarta');

        if ($currentTime->diffInMinutes($waktuMulaiCompare, false) < -15) {
            $status = 'Telat';
        }

        // Buat record absen baru
        Absen::create([
            'id_user' => $user->id,
            'tanggal_absen' => $today,
            'waktu_absen' => $currentTime,
            'waktu_masuk' => $currentTime,
            'id_toko' => session('id_toko'),
            'id_shift' => $shift->id,
            'status' => $status,
            'lokasi' => $request->ip()
        ]);

        return redirect()
            ->route('absensi.hari-ini')
            ->with('success', 'Absen masuk berhasil dicatat. Status: ' . $status);
    }

    // Proses absen keluar
    public function absenKeluar(Request $request)
    {
        $user = auth()->user();
        $today = now()->toDateString();
        $currentTime = now()->setTimezone('Asia/Jakarta');
        if (session()->has('id_toko')) {
            $toko = \App\Models\Toko::find(session('id_toko'));
        }


        // Cari absen hari ini
        $absen = Absen::where('id_user', $user->id)
            ->where('tanggal_absen', $today)
            ->first();

        if (!$absen) {
            return redirect()
                ->route('absensi.hari-ini')
                ->with('error', 'Anda belum absen masuk hari ini.');
        }

        if ($absen->waktu_keluar) {
            return redirect()
                ->route('absensi.hari-ini')
                ->with('error', 'Anda sudah absen keluar hari ini.');
        }

        // Validasi shift
        $shift = $user->shift;
        if (!$shift) {
            return redirect()
                ->route('absensi.hari-ini')
                ->with('error', 'Shift belum ditentukan. Silakan hubungi superadmin.');
        }

        $shiftStart = Carbon::createFromFormat('H:i:s', $shift->waktu_mulai, 'Asia/Jakarta');
        $shiftEnd = Carbon::createFromFormat('H:i:s', $shift->waktu_selesai, 'Asia/Jakarta');

        // Jika shift melewati tengah malam
        if ($shiftEnd->lessThan($shiftStart)) {
            $shiftEnd->addDay();
        }

        // Update absen keluar
        $absen->update([
            'waktu_keluar' => $currentTime,
            'lokasi' => $toko ? $toko->lokasi : '-',
        ]);

        return redirect()
            ->route('absensi.hari-ini')
            ->with('success', 'Absen keluar berhasil dicatat.');
    }

    // Daftar absen karyawan
    public function daftarAbsen()
    {
        $user = auth()->user();
        $toko = null;
        if (session()->has('id_toko')) {
            $toko = Toko::find(session('id_toko'));
        }

        $absenList = Absen::with('shift')
            ->where('id_user', $user->id)
            ->orderBy('tanggal_absen', 'desc')
            ->paginate(10);

        return view('absensi.daftar-absen', compact('absenList', 'toko'));
    }

    // Monitoring absensi karyawan (untuk superadmin)
    public function absensiKaryawan()
    {
        $karyawans = KaryawanUser::where('role', '!=', 'superadmin')
            ->with(['toko', 'absen' => function ($query) {
                $query->with('shift')->orderBy('tanggal_absen', 'desc');
            }])
            ->paginate(10);

        $toko = null;
        if (session()->has('id_toko')) {
            $toko = Toko::find(session('id_toko'));
        }

        return view('absensi.absensi-karyawan', compact('karyawans', 'toko'));
    }

    // Riwayat absen per karyawan
    public function riwayatAbsen($id)
    {
        $karyawan = KaryawanUser::findOrFail($id);
        $absenList = Absen::with('shift')
            ->where('id_user', $id)
            ->orderBy('tanggal_absen', 'desc')
            ->paginate(10);

        $toko = null;
        if (session()->has('id_toko')) {
            $toko = Toko::find(session('id_toko'));
        }

        return view('absensi.riwayat-absen', compact('karyawan', 'absenList', 'toko'));
    }
}
