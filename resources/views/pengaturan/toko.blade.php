@extends('layouts.app')
@section('title', 'Kasir Internet')

<style>
    .foto-toko {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #ddd;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-action-group {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .btn-action-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>

@section('content')
    <!-- Notifikasi -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header Section -->
    <div class="">
        <div class="d-flex justify-content-between align-items-center">
            <div class="pagetitle">
                <h1 style="color: #633B48; font-weight: 600;">Manajemen Toko</h1>
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
        </div>
    </div>

    <!-- Main Content Section -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Data Toko</h5>
                            <button class="btn text-white" style="background-color: #633B48" data-bs-toggle="modal"
                                data-bs-target="#createTokoModal">
                                Tambah Toko
                            </button>
                        </div>

                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Lokasi</th>
                                    <th>Transaksi</th>
                                    <th>Total Karyawan</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tokos as $key => $toko)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $toko->nama }}</td>
                                        <td>{{ $toko->lokasi }}</td>
                                        <td>{{ $toko->transaksi }}</td>
                                        <td>{{ $toko->total_karyawan }}</td>
                                        <td>{{ $toko->keterangan ?? '-' }}</td>
                                        <td>
                                            <div class="btn-action-group">
                                                <!-- Tombol Edit -->
                                                <button class="btn btn-warning btn-sm btn-edit"
                                                    data-id="{{ $toko->id }}" data-nama="{{ $toko->nama }}"
                                                    data-lokasi="{{ $toko->lokasi }}"
                                                    data-transaksi="{{ $toko->transaksi }}"
                                                    data-karyawan="{{ $toko->total_karyawan }}"
                                                    data-keterangan="{{ $toko->keterangan }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <!-- Tombol Hapus -->
                                                <form action="{{ route('toko.destroy', $toko->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus toko ini?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Tambah Toko -->
    <div class="modal fade text-dark" id="createTokoModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('toko.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Toko</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama:</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi:</label>
                            <input type="text" name="lokasi" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Transaksi:</label>
                            <input type="number" name="transaksi" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Karyawan:</label>
                            <input type="number" name="total_karyawan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan:</label>
                            <textarea name="keterangan" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn text-white" style="background-color: #633B48"">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Toko -->
    <div class="modal fade text-dark" id="editTokoModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="editTokoForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Toko</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama:</label>
                            <input type="text" name="nama" id="editNama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi:</label>
                            <input type="text" name="lokasi" id="editLokasi" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Transaksi:</label>
                            <input type="number" name="transaksi" id="editTransaksi" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Karyawan:</label>
                            <input type="number" name="total_karyawan" id="editKaryawan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan:</label>
                            <textarea name="keterangan" id="editKeterangan" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn text-white" style="background-color: #633B48">Simpan
                            Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle edit button click
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function() {
                    // Set form values
                    document.getElementById('editNama').value = this.getAttribute('data-nama');
                    document.getElementById('editLokasi').value = this.getAttribute('data-lokasi');
                    document.getElementById('editTransaksi').value = this.getAttribute(
                        'data-transaksi');
                    document.getElementById('editKaryawan').value = this.getAttribute(
                        'data-karyawan');
                    document.getElementById('editKeterangan').value = this.getAttribute(
                        'data-keterangan');

                    // Set form action
                    document.getElementById('editTokoForm').action =
                        `/toko/${this.getAttribute('data-id')}`;

                    // Show modal
                    new bootstrap.Modal(document.getElementById('editTokoModal')).show();
                });
            });
        });
    </script>
@endsection
