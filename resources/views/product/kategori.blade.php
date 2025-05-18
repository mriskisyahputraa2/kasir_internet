@extends('layouts.app') @section('title', 'Kasir Internet') @section('content')
<div class="pagetitle">
    <h1 style="color: #633B48; font-weight: 600;">Kategori</h1>
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

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0">Daftar Kategori</h5>
                @if ($user['role'] === 'superadmin' || $user['role'] === 'admin')
                    <button class="btn text-white" style="background-color: #633B48"data-bs-toggle="modal"
                        data-bs-target="#createModal">
                        Tambah Kategori
                    </button>
                @endif
            </div>

            <!-- Table with stripped rows -->
            <div class="table-responsive">
                <table class="table datatable" id="myTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Kategori</th>
                            @if ($user['role'] === 'superadmin' || $user['role'] === 'admin')
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kategoris as $key => $kategori)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $kategori->name }}</td>
                                @if ($user['role'] === 'superadmin' || $user['role'] === 'admin')
                                    <td>
                                        <button class="btn btn-warning edit-btn" data-id="{{ $kategori->id }}"
                                            data-name="{{ $kategori->name }}" data-bs-toggle="modal"
                                            data-bs-target="#editModal">
                                            Edit
                                        </button>
                                        <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST"
                                            style="display: inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger"
                                                onclick="return confirm('Yakin ingin menghapus?')">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- End Table with stripped rows -->
            </div>
        </div>
    </div>

    <!-- Modal Create -->
    <div class="modal fade text-dark" id="createModal" tabindex="-1" aria-labelledby="createModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">
                        Tambah Kategori
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('kategori.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" id="name" name="name" required />
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn text-white"
                                style="background-color: #633B48">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade text-dark" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" id="edit_id" name="id" />
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required />
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-3"> <button type="submit"
                                class="btn text-white" style="background-color: #633B48">Simpan</button> <button
                                type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".edit-btn").forEach((button) => {
                button.addEventListener("click", function() {
                    let id = this.getAttribute("data-id");
                    let name = this.getAttribute("data-name");

                    document.getElementById("edit_id").value = id;
                    document.getElementById("edit_name").value = name;
                    document.getElementById("editForm").action = "/kategori/" + id;
                });
            });
        });
    </script>
@endsection
