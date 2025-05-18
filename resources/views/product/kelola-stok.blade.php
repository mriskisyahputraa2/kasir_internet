@extends('layouts.app')

@section('title', 'Kasir Internet')

@section('content')
    <div class="pagetitle">
        <h1 style="color: #633B48; font-weight: 600;">Kelola Stok</h1>
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
                    <h5 class="card-title m-0">Kelola Stok Produk</h5>
                </div>

                <!-- Form Filter dengan Select Options -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form action="{{ route('kelola-stok.index') }}" method="GET" id="filterForm">
                            <div class="input-group">
                                <span class="input-group-text">Filter</span>
                                <select class="form-select" name="filter" id="filter">
                                    <option value="semua" {{ request('filter') == 'semua' ? 'selected' : '' }}>Semua Produk
                                    </option>
                                    <option value="stok_sedikit"
                                        {{ request('filter') == 'stok_sedikit' ? 'selected' : '' }}>Stok Sedikit (≤ 5)
                                    </option>
                                    <option value="kadaluarsa" {{ request('filter') == 'kadaluarsa' ? 'selected' : '' }}>
                                        Kadaluarsa (≤ 5 Hari Lagi)</option>
                                </select>
                                <a href="{{ route('kelola-stok.index') }}" class="btn btn-outline-danger">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table with stripped rows -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nama Produk</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Stok</th>
                            <th scope="col">Tanggal Kadaluarsa</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produks as $key => $produk)
                            <tr>
                                <th scope="row">{{ $key + 1 }}</th>
                                <td>{{ $produk->nama }}</td>
                                <td>{{ $produk->kategoriRelasi->name }}</td>
                                <td>{{ $produk->stok }}</td>
                                <td>{{ $produk->tgl_kadaluarsa ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editProduk({{ json_encode($produk) }})"
                                        data-bs-toggle="modal" data-bs-target="#modalEdit">Edit</button>
                                    <form action="{{ route('produk.destroy', $produk->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- End Table with stripped rows -->
            </div>
        </div>
    </div>

    <!-- Modal Edit Produk -->
    <div class="modal fade text-dark" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Edit Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEdit" action="{{ route('kelola-stok.update', ['kelola_stok' => 'id']) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editId">
                        <div class="mb-3">
                            <label>Nama Produk</label>
                            <input type="text" name="nama" id="editNama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Kategori</label>
                            <select name="kategori" id="editKategori" class="form-control" required>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Stok</label>
                            <input type="number" name="stok" id="editStok" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Tanggal Kadaluarsa</label>
                            <input type="date" name="tgl_kadaluarsa" id="editTglKadaluarsa" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn text-white" style="background-color: #633B48">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script untuk Auto Submit Form dan Edit Produk -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto submit filter
            var filterSelect = document.getElementById('filter');
            filterSelect.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            // Fungsi untuk mengisi form edit
            window.editProduk = function(produk) {
                document.getElementById('editId').value = produk.id;
                document.getElementById('editNama').value = produk.nama;
                document.getElementById('editKategori').value = produk.kategori;
                document.getElementById('editStok').value = produk.stok;
                document.getElementById('editTglKadaluarsa').value = produk.tgl_kadaluarsa;

                // Set action form edit
                document.getElementById('formEdit').action = `/kelola-stok/${produk.id}`;

                // Buka modal edit
                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            };
        });
    </script>
@endsection
