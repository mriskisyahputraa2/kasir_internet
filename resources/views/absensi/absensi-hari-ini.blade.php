@extends('layouts.app')
@section('title', 'Kasir Internet')

@section('content')
    <div class="pagetitle">
        <h1 style="color: #633B48; font-weight: 600;">Absensi Hari Ini</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Absensi</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Absensi Hari Ini - {{ now()->translatedFormat('l, d F Y') }}</h5>

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if ($errorMessage)
                            <div class="alert alert-warning">
                                {{ $errorMessage }}
                            </div>
                        @endif

                        <div class="row">
                            <!-- Absen Masuk -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Absen Masuk</h5>

                                        @if (!$shift)
                                            <div class="alert alert-warning">
                                                Shift belum ditentukan. Silakan hubungi superadmin.
                                            </div>
                                        @elseif ($showMasuk)
                                            <form action="{{ route('absensi.masuk') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn text-white btn-lg"
                                                    style="background-color: #633B48">
                                                    <i class="bi bi-box-arrow-in-right"></i> Absen Masuk
                                                </button>
                                                <p class="mt-2 text-muted">Shift: {{ $shift->nama_shift }}
                                                    ({{ $shift->waktu_mulai }} - {{ $shift->waktu_selesai }})</p>
                                            </form>
                                        @elseif($absen)
                                            <div class="alert alert-info">
                                                <p>Absen terakhir: {{ $absen->formatted_waktu_absen }}</p>
                                                <p>Waktu masuk: {{ $absen->formatted_waktu_masuk }}</p>
                                                <p>Status:
                                                    @if ($absen->status === 'Hadir')
                                                        <span class="badge bg-success">Hadir</span>
                                                    @elseif($absen->status === 'Telat')
                                                        <span class="badge bg-warning">Telat</span>
                                                    @elseif($absen->status === 'Tidak Bekerja')
                                                        <span class="badge bg-danger">Tidak Bekerja</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $absen->status }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                Tidak bisa absen karena bukan jam kerja Anda (Shift:
                                                {{ $shift->waktu_mulai }} - {{ $shift->waktu_selesai }})
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Absen Keluar -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Absen Keluar</h5>

                                        @if (!$shift)
                                            <div class="alert alert-warning">
                                                Shift belum ditentukan. Silakan hubungi superadmin.
                                            </div>
                                        @elseif ($showKeluar)
                                            <form action="{{ route('absensi.keluar') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-lg">
                                                    <i class="bi bi-box-arrow-right"></i> Absen Keluar
                                                </button>
                                                <p class="mt-2">Status: <span class="badge bg-primary">Sedang
                                                        Bekerja</span></p>
                                            </form>
                                        @elseif($absen && $absen->waktu_keluar)
                                            <div class="alert alert-success">
                                                <p>Waktu keluar: {{ $absen->formatted_waktu_keluar }}</p>
                                                <p>Status: <span class="badge bg-secondary">Selesai Bekerja</span></p>
                                                <p>Lokasi: {{ $absen->lokasi ?? '-' }}</p>
                                            </div>
                                        @else
                                            <div class="alert alert-secondary">
                                                <p>Belum bisa absen keluar</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('absensi.daftar') }}" class="btn text-white"
                                style="background-color: #633B48">
                                <i class="bi bi-list-ul"></i> Lihat Daftar Absen
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
