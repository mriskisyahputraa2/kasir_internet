@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Riwayat Sesi Kasir</h2>

        <!-- Tabel Riwayat Sesi Kasir -->
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Shift</th>
                    <th>Saldo Awal</th>
                    <th>Saldo Akhir</th>
                    <th>Dana Laci</th>
                    <th>Status</th>
                    <th>Kasir</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sesiKasir as $sesi)
                    <tr>
                        <td>{{ $sesi->tanggal }}</td>
                        <td>{{ $sesi->shift->nama_shift }}</td>
                        <td>{{ formatRupiah($sesi->saldo_awal) }}</td>
                        <td>{{ formatRupiah($sesi->saldo_akhir) }}</td>
                        <td>{{ formatRupiah($sesi->dana_laci) }}</td>
                        <td>{{ $sesi->status }}</td>
                        <td>{{ $sesi->user->name }}</td> <!-- Nama kasir yang membuka/tutup sesi -->
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
