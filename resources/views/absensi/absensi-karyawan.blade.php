@extends('layouts.app')
@section('title', 'Kasir Internet')

@section('content')
    <div class="pagetitle">
        <h1 style="color: #633B48; font-weight: 600;">Absensi Karyawan</h1>
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
                        <h5 class="card-title">Daftar Karyawan</h5>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Toko</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($karyawans as $key => $karyawan)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $karyawan->nama }}</td>
                                            <td>{{ ucfirst($karyawan->role) }}</td>
                                            <td>{{ $karyawan->toko ? $karyawan->toko->nama : '-' }}</td>
                                            <td>
                                                <a href="{{ route('absensi.riwayat', $karyawan->id) }}"
                                                    class="btn text-white" style="background-color: #633B48">
                                                    <i class="bi bi-clock-history"></i> Riwayat
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $karyawans->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
