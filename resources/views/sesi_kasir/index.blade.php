@extends('layouts.app') @section('content')
    <div class="container">
        <h2>Manajemen Sesi Kasir</h2>

        <!-- Form Buka Kasir -->
        @if (!$sesiAktif)
            <form action="{{ route('buka.kasir') }}" method="POST" class="mb-4">
                @csrf
                <div class="mb-3">
                    <label for="shift_id" class="form-label">Shift</label>
                    <select name="shift_id" class="form-select" required>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}">
                                {{ $shift->nama_shift }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="dana_laci" class="form-label">Dana Laci</label>
                    <input type="number" name="dana_laci" class="form-control" required />
                </div>
                <button type="submit" class="btn btn-primary">Buka Kasir</button>
            </form>
        @else
            <!-- Tombol Tutup Kasir -->
            <form action="{{ route('tutup.kasir') }}" method="POST" class="mb-4">
                @csrf
                <button type="submit" class="btn btn-danger">Tutup Kasir</button>
            </form>
        @endif

        <!-- Riwayat Sesi Kasir -->
        <h3>Riwayat Sesi Kasir</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Shift</th>

                    <th>Saldo Awal</th>
                    <th>Saldo Akhir</th>
                    <th>Dana Laci</th>
                    <th>Status</th>
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
                        <td>
                            @if ($sesi->status == 'buka')
                                <span class="badge bg-success">Buka</span>
                            @else
                                <span class="badge bg-danger">Tutup</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
