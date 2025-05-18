@extends('layouts.app')
@section('title', 'Kasir Internet')

<style>
    .btn-action-group {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .btn-action-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .btn-action-group .btn i {
        font-size: 1rem;
        /* Ukuran sama untuk semua ikon */
        line-height: 1.5;
        /* Untuk vertikal alignment yang sama */
        width: 1.25rem;
        /* Lebar tetap */
        text-align: center;
        /* Posisi tengah */
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
                <h1 style="color: #633B48; font-weight: 600;">Manajemen Shift</h1>
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
                            <h5 class="card-title">Data Shift</h5>
                            <button class="btn text-white" style="background-color: #633B48" data-bs-toggle="modal"
                                data-bs-target="#createShiftModal">
                                Tambah Shift
                            </button>
                        </div>

                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Shift</th>
                                    <th>Waktu Mulai</th>
                                    <th>Waktu Selesai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shifts as $key => $shift)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $shift->nama_shift }}</td>
                                        <td>{{ $shift->waktu_mulai }}</td>
                                        <td>{{ $shift->waktu_selesai }}</td>
                                        <td>
                                            <div class="btn-action-group">
                                                <!-- Tombol Edit -->
                                                <button class="btn btn-warning btn-sm btn-edit"
                                                    data-id="{{ $shift->id }}" data-nama_shift="{{ $shift->nama_shift }}"
                                                    data-waktu_mulai="{{ $shift->waktu_mulai }}"
                                                    data-waktu_selesai="{{ $shift->waktu_selesai }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <!-- Tombol Hapus -->
                                                <form action="{{ route('shift.destroy', $shift->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus shift ini?')">
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

    <!-- Modal Tambah Shift -->
    <div class="modal fade" id="createShiftModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('shift.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Shift</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Shift:</label>
                            <input type="text" name="nama_shift" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Waktu Mulai:</label>
                            <input type="time" name="waktu_mulai" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Waktu Selesai:</label>
                            <input type="time" name="waktu_selesai" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Shift -->
    <div class="modal fade text-dark" id="editShiftModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="editShiftForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Shift</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Shift:</label>
                            <input type="text" name="nama_shift" id="editNamaShift" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Waktu Mulai:</label>
                            <input type="time" name="waktu_mulai" id="editWaktuMulai" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Waktu Selesai:</label>
                            <input type="time" name="waktu_selesai" id="editWaktuSelesai" class="form-control" required>
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
                    document.getElementById('editNamaShift').value = this.getAttribute(
                        'data-nama_shift');
                    document.getElementById('editWaktuMulai').value = this.getAttribute(
                        'data-waktu_mulai');
                    document.getElementById('editWaktuSelesai').value = this.getAttribute(
                        'data-waktu_selesai');

                    // Set form action
                    document.getElementById('editShiftForm').action =
                        `/shift/${this.getAttribute('data-id')}`;

                    // Show modal
                    new bootstrap.Modal(document.getElementById('editShiftModal')).show();
                });
            });
        });
    </script>
@endsection
