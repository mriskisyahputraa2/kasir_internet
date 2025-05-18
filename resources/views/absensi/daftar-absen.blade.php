@extends('layouts.app')
@section('title', 'Kasir Internet')

@section('content')
    <div class="pagetitle">
        <h1 style="color: #633B48; font-weight: 600;">Daftar Absen</h1>
        @if (session()->has('id_toko'))
            <nav style="--bs-breadcrumb-divider: ':'">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Outlet</a></li>
                    <li class="breadcrumb-item active">{{ $toko->nama }}</li>
                </ol>
            </nav>
        @else
            <p>Toko: Belum dipilih</p>
        @endif
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Riwayat Absensi Anda</h5>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Shift</th>
                                        <th>Status</th>
                                        <th>Waktu Masuk</th>
                                        <th>Waktu Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($absenList as $key => $absen)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $absen->tanggal_absen->format('d/m/Y') }}</td>
                                            <td>
                                                @if ($absen->shift)
                                                    {{ $absen->shift->nama_shift }} ({{ $absen->shift->waktu_mulai }} -
                                                    {{ $absen->shift->waktu_selesai }})
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($absen->status === 'Hadir')
                                                    <span class="badge bg-success">Hadir</span>
                                                @elseif($absen->status === 'Telat')
                                                    <span class="badge bg-warning">Telat</span>
                                                @else
                                                    <span class="badge bg-danger">{{ $absen->status }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $absen->waktu_masuk ? $absen->waktu_masuk->format('H:i:s') : ($absen->shift ? Carbon\Carbon::createFromFormat('H:i:s', $absen->shift->waktu_mulai)->format('H:i:s') : '-') }}
                                            </td>
                                            <td>{{ $absen->waktu_keluar ? $absen->waktu_keluar->format('H:i:s') : ($absen->shift ? Carbon\Carbon::createFromFormat('H:i:s', $absen->shift->waktu_selesai)->format('H:i:s') : '-') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $absenList->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
