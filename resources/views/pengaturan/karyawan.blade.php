@extends('layouts.app')
@section('title', 'Kasir Internet')

<style>
    .foto-karyawan {
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
    <!-- Alert Section (tetap sama) -->
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

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header Section (tetap sama) -->
    <div class="">
        <div class="d-flex justify-content-between align-items-center">
            <div class="pagetitle">
                <h1 style="color: #633B48; font-weight: 600;">Manajemen Karyawan</h1>
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
                            <h5 class="card-title">Data Karyawan</h5>
                            <div>
                                <button class="btn text-white" style="background-color: #633B48"data-bs-toggle="modal"
                                    data-bs-target="#createKaryawanModal">
                                    Tambah Karyawan
                                </button>
                            </div>
                        </div>

                        <!-- Table -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Toko</th>
                                    <th>Jam Shift</th>
                                    <th>Foto</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($karyawans as $key => $karyawan)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $karyawan->nama }}</td>
                                        <td>{{ $karyawan->username }}</td>
                                        <td>
                                            <span
                                                class="badge
                                                @if ($karyawan->role === 'superadmin') bg-danger
                                                @elseif($karyawan->role === 'admin') bg-primary
                                                @else bg-success @endif">
                                                {{ $karyawan->role }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($karyawan->role === 'superadmin')
                                                -
                                            @else
                                                @if ($karyawan->toko)
                                                    {{ $karyawan->toko->nama }}
                                                @else
                                                    <span class="badge bg-warning">Belum dipilih</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($karyawan->shift)
                                                {{ $karyawan->shift->waktu_mulai }} - {{ $karyawan->shift->waktu_selesai }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($karyawan->foto)
                                                <img src="{{ asset('storage/' . $karyawan->foto) }}" alt="Foto Karyawan"
                                                    class="foto-karyawan">
                                            @else
                                                <img src="{{ asset('assets/img/default-user.png') }}" alt="Default Foto"
                                                    class="foto-karyawan">
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-action-group">
                                                @if (auth()->user()->role === 'superadmin' && in_array($karyawan->role, ['admin', 'kasir']))
                                                    <button class="btn btn-info btn-sm btn-pilih-toko"
                                                        data-id="{{ $karyawan->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#pilihTokoModal" title="Pilih Toko">
                                                        <i class="bi bi-shop"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-warning btn-sm btn-edit"
                                                    data-id="{{ $karyawan->id }}" data-nama="{{ $karyawan->nama }}"
                                                    data-username="{{ $karyawan->username }}"
                                                    data-role="{{ $karyawan->role }}"
                                                    data-id_shift="{{ $karyawan->id_shift }}" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="{{ route('karyawan.destroy', $karyawan->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?')"
                                                        title="Hapus">
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

    <!-- Modal Tambah Karyawan -->
    <div class="modal fade text-dark" id="createKaryawanModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('karyawan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama:</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username:</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role:</label>
                            <select name="role" id="roleSelect" class="form-select" required>
                                <option value="kasir">Kasir</option>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Super Admin</option>
                            </select>
                        </div>
                        <div class="mb-3" id="shiftField">
                            <label class="form-label">Shift:</label>
                            <select name="id_shift" class="form-select">
                                <option value="">Pilih Shift</option>
                                @foreach ($shifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->nama_shift }}
                                        ({{ $shift->waktu_mulai }} - {{ $shift->waktu_selesai }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto:</label>
                            <input type="file" name="foto" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn text-white" style="background-color: #633B48">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Karyawan -->
    <div class="modal fade text-dark" id="editKaryawanModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="editKaryawanForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama:</label>
                            <input type="text" name="nama" id="editNama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username:</label>
                            <input type="text" name="username" id="editUsername" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password:</label>
                            <input type="password" name="password" id="editPassword" class="form-control">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role:</label>
                            <select name="role" id="editRole" class="form-select" required>
                                <option value="kasir">Kasir</option>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Super Admin</option>
                            </select>
                        </div>
                        <div class="mb-3" id="editShiftField">
                            <label class="form-label">Shift:</label>
                            <select name="id_shift" id="editIdShift" class="form-select">
                                <option value="">Pilih Shift</option>
                                @foreach ($shifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->nama_shift }}
                                        ({{ $shift->waktu_mulai }} - {{ $shift->waktu_selesai }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto:</label>
                            <input type="file" name="foto" class="form-control">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
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

    <!-- Modal Pilih Toko -->
    <div class="modal fade text-dark" id="pilihTokoModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="pilihTokoForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pilih Toko untuk Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih Toko:</label>
                            <select name="id_toko" class="form-select" required>
                                <option value="">Pilih Toko</option>
                                @foreach ($tokos as $toko)
                                    <option value="{{ $toko->id }}">{{ $toko->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn text-white" style="background-color: #633B48">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle role selection for create modal
            const roleSelect = document.getElementById('roleSelect');
            const shiftField = document.getElementById('shiftField');

            roleSelect.addEventListener('change', function() {
                if (this.value === 'superadmin') {
                    shiftField.style.display = 'none';
                    shiftField.querySelector('select').value = '';
                } else {
                    shiftField.style.display = 'block';
                }
            });

            // Set initial state for create modal
            if (roleSelect.value === 'superadmin') {
                shiftField.style.display = 'none';
            }

            // Handle edit button click
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function() {
                    const karyawanId = this.getAttribute('data-id');
                    const karyawanNama = this.getAttribute('data-nama');
                    const karyawanUsername = this.getAttribute('data-username');
                    const karyawanRole = this.getAttribute('data-role');
                    const karyawanShift = this.getAttribute('data-id_shift');

                    // Fill edit form
                    document.getElementById('editNama').value = karyawanNama;
                    document.getElementById('editUsername').value = karyawanUsername;
                    document.getElementById('editRole').value = karyawanRole;
                    document.getElementById('editIdShift').value = karyawanShift;

                    // Show/hide shift field based on role
                    const editShiftField = document.getElementById('editShiftField');
                    if (karyawanRole === 'superadmin') {
                        editShiftField.style.display = 'none';
                        document.getElementById('editIdShift').value = '';
                    } else {
                        editShiftField.style.display = 'block';
                    }

                    // Set form action
                    document.getElementById('editKaryawanForm').action = `/karyawan/${karyawanId}`;

                    // Show modal
                    new bootstrap.Modal(document.getElementById('editKaryawanModal')).show();
                });
            });

            // Handle role change in edit modal
            document.getElementById('editRole').addEventListener('change', function() {
                const editShiftField = document.getElementById('editShiftField');
                if (this.value === 'superadmin') {
                    editShiftField.style.display = 'none';
                    document.getElementById('editIdShift').value = '';
                } else {
                    editShiftField.style.display = 'block';
                }
            });

            // Handle pilih toko button
            document.querySelectorAll('.btn-pilih-toko').forEach(button => {
                button.addEventListener('click', function() {
                    const karyawanId = this.getAttribute('data-id');
                    const form = document.getElementById('pilihTokoForm');
                    form.action = `/karyawan/${karyawanId}/pilih-toko`;
                });
            });
        });
    </script>
@endsection
