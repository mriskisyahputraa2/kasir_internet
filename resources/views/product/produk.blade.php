@extends('layouts.app')

@section('title', 'Kasir Internet')

@section('content')
    <div class="pagetitle">
        <h1 style="color: #633B48; font-weight: 600;">Produk</h1>
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
                    <h5 class="card-title m-0">Kelola Produk</h5>
                    @if ($user['role'] === 'superadmin' || $user['role'] === 'admin')
                        <button class="btn text-white" style="background-color: #633B48"data-bs-toggle="modal"
                            data-bs-target="#modalCreate">
                            Tambah Produk
                        </button>
                    @endif
                </div>

                <!-- Form Filter dengan Select Options -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form action="{{ route('produk.index') }}" method="GET" id="filterForm">
                            <div class="input-group">
                                <span class="input-group-text">Filter</span>
                                <select class="form-select" name="kategori" id="kategoriFilter">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}"
                                            {{ $kategoriFilter == $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" name="barcode" id="barcodeFilter" class="form-control"
                                    placeholder="Cari barcode" value="{{ $barcodeFilter }}">
                                <a href="{{ route('produk.index') }}" class="btn btn-outline-danger">Reset</a>
                            </div>
                        </form>


                    </div>
                </div>

                <!-- Table with stripped rows -->
                <div class="table-responsive">
                    <table class="table datatable" id="myTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Barcode</th>
                                @if ($user['role'] === 'superadmin' || $user['role'] === 'admin')
                                    <th>Harga Beli</th>
                                @endif
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Tgl Kadaluarsa</th>
                                <th>Diskon Global</th>
                                <th>Foto</th>
                                @if ($user['role'] === 'superadmin' || $user['role'] === 'admin')
                                    <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produks as $key => $produk)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $produk->nama }}</td>
                                    <td>{{ $produk->kategoriRelasi->name }}</td>
                                    <td>{{ $produk->barcode }}</td>
                                    @if ($user['role'] === 'superadmin' || $user['role'] === 'admin')
                                        <td>Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}</td>
                                    @endif
                                    <td>Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                                    <td>{{ $produk->stok }}</td>
                                    <td>{{ $produk->tgl_kadaluarsa ?? '-' }}</td>
                                    <td>Rp {{ number_format($produk->diskon_global, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($produk->foto)
                                            <img src="{{ asset('storage/' . $produk->foto) }}" width="50">
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @if ($user['role'] === 'superadmin' || $user['role'] === 'admin')
                                        <td>
                                            <form action="{{ route('produk.destroy', $produk->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
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
        <div class="modal fade text-dark" id="modalCreate" tabindex="-1" aria-labelledby="modalCreateLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data"
                        id="formTambahProduk">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nama Produk</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Kategori</label>
                                <select name="kategori" class="form-control" required>
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Barcode</label>
                                <input type="text" name="barcode" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Harga Beli</label>
                                <input type="text" name="harga_beli" id="harga_beli" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Harga Jual</label>
                                <input type="text" name="harga_jual" id="harga_jual" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Stok</label>
                                <input type="number" name="stok" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Tgl Kadaluarsa</label>
                                <input type="date" name="tgl_kadaluarsa" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Foto Produk</label>
                                <input type="file" name="foto" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Diskon Global</label>
                                <input type="text" name="diskon_global" id="diskon_global" class="form-control"
                                    value="0" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button " class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn text-white"
                                style="background-color: #633B48">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Load Cleave.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Format input harga_beli ke mata uang (Rp)
                new Cleave('#harga_beli', {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    prefix: 'Rp ',
                    noImmediatePrefix: true,
                    numeralDecimalMark: ',',
                    delimiter: '.'
                });

                // Format input harga_jual ke mata uang (Rp)
                new Cleave('#harga_jual', {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    prefix: 'Rp ',
                    noImmediatePrefix: true,
                    numeralDecimalMark: ',',
                    delimiter: '.'
                });

                // Format input diskon_global ke mata uang (Rp)
                new Cleave('#diskon_global', {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    prefix: 'Rp ',
                    noImmediatePrefix: true,
                    numeralDecimalMark: ',',
                    delimiter: '.'
                });

                // Konversi nilai ke angka sebelum submit
                document.getElementById('formTambahProduk').addEventListener('submit', function(e) {
                    var hargaBeliInput = document.getElementById('harga_beli');
                    var hargaJualInput = document.getElementById('harga_jual');
                    var diskonGlobalInput = document.getElementById('diskon_global');

                    // Hapus format mata uang dan simpan nilai angka murni
                    hargaBeliInput.value = hargaBeliInput.value.replace(/[^0-9]/g, '');
                    hargaJualInput.value = hargaJualInput.value.replace(/[^0-9]/g, '');
                    diskonGlobalInput.value = diskonGlobalInput.value.replace(/[^0-9]/g, '');
                });

                // Script untuk Auto Submit Form Filter
                var kategoriFilter = document.getElementById('kategoriFilter');
                var barcodeFilter = document.getElementById('barcodeFilter');

                kategoriFilter.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });

                barcodeFilter.addEventListener('input', function() {
                    document.getElementById('filterForm').submit();
                });
            });
        </script>
    @endsection
