@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Daftar Transaksi Bayar Nanti</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Total Harga</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksis as $transaksi)
            <tr>
                <td>{{ $transaksi->id }}</td>
                <td>Rp{{ number_format($transaksi->total_harga, 2) }}</td>
                <td>{{ $transaksi->created_at->format('d-m-Y H:i') }}</td>
                <td>
                    <a href="{{ route('transaksi.lanjutkan', $transaksi->id) }}" class="btn btn-primary">
                        Lanjutkan Pembayaran
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection