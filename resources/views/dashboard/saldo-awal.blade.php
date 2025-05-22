@extends('layouts.app')

@section('title', 'Kasir Internet')

@section('content')
    <!-- Modal Tambah -->

    <!-- Tambah Saldo Modal -->
    <div class="pagetitle">
        <h1 style="color: #633B48; font-weight: 600;">Manajemen Saldo Awal</h1>
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
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title m-0">Saldo Awal</h5>
                            @if ($user['role'] === 'superadmin' || $user['role'] === 'admin')
                                <button type="button" class="btn text-white" style="background-color: #633B48"
                                    data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    Tambah Platform
                                </button>
                            @endif
                        </div>

                        <!-- Table with stripped rows -->
                        <div class="table-responsive">
                            <table id="saldoAwalTable" class="table table-bordered display nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Logo</th>
                                        <th><b>N</b>ama Platform</th>
                                        <th>Saldo</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tambahSaldos as $saldo)
                                        <tr>
                                            <td>
                                                {{-- <img src="{{ asset('storage/' . $saldo->logo) }}" width="50" /> --}}
                                                <img src="{{ asset('storage/' . $saldo->logo) }}" width="50" />
                                            </td>
                                            <td>{{ $saldo->nama_platform }}</td>
                                            <td>{{ 'Rp ' . number_format($saldo->saldo, 0, ',', '.') }}</td>
                                            <td>
                                                <div class="filter">
                                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                                            class="bi bi-three-dots"></i></a>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                        <li>
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                data-bs-target="#editModal{{ $saldo->id }}">Edit</a>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('tambah-saldo.destroy', $saldo->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Yakin ingin menghapus?')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    Hapus
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal Form Update Saldo -->
                                        <div class="modal fade" id="editModal{{ $saldo->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <form action="{{ route('tambah-saldo.update', $saldo->id) }}" method="POST"
                                                    enctype="multipart/form-data">
                                                    @csrf @method('PUT')

                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Saldo</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row g-3 mt-2">
                                                                <div class="col-md-12">
                                                                    <label for="nama_platform" class="form-label">Nama
                                                                        Platform</label>
                                                                    <input type="text" class="form-control"
                                                                        id="nama_platform" name="nama_platform"
                                                                        value="{{ $saldo->nama_platform }}" required />
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="inputSaldo" class="form-label">Saldo</label>
                                                                    <input type="text" class="form-control"
                                                                        id="inputSaldo" name="saldo"
                                                                        value="{{ number_format($saldo->saldo, 0, ',', '.') }}"
                                                                        required oninput="formatRupiah(this)" />
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="inputLogo" class="form-label">Logo</label>
                                                                    <input type="file" class="form-control"
                                                                        id="inputLogo" name="logo" />
                                                                </div>
                                                                <div class="col-12">
                                                                    <label for="inputKeterangan"
                                                                        class="form-label">Keterangan</label>
                                                                    <textarea name="keterangan" class="form-control" id="inputKeterangan">{{ $saldo->keterangan }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn text-white"
                                                                style="background-color: #633B48">Simpan</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tambah Platform Modal -->
    <div class="modal fade" id="verticalycentered" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" action="{{ route('tambah-saldo.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-dark">Tambah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3 mt-2">
                                <div class="col-md-12">
                                    <label for="nama_platform" class="form-label">Nama Platform</label>
                                    <input type="text" class="form-control" id="nama_platform" name="nama_platform"
                                        required />
                                </div>
                                <div class="col-md-6">
                                    <label for="inputSaldo" class="form-label">Saldo</label>
                                    <input type="text" class="form-control" id="inputSaldo" name="saldo" required
                                        oninput="formatRupiah(this)" />
                                </div>
                                <div class="col-md-6">
                                    <label for="inputLogo" class="form-label">Logo</label>
                                    <input type="file" class="form-control" id="inputLogo" name="logo" />
                                </div>
                                <div class="col-12">
                                    <label for="inputKeterangan" class="form-label">Keterangan</label>
                                    <textarea name="keterangan" class="form-control" id="inputKeterangan"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white" style="background-color: #633B48">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function formatRupiah(element) {
            let value = element.value.replace(/[^0-9]/g, ''); // Hanya angka
            let formatted = new Intl.NumberFormat('id-ID').format(value); // Format ke Rupiah
            element.value = formatted;
        }
        $(document).ready(function() {
            $('#saldoAwalTable').DataTable({
                responsive: true,
                language: {
                    "lengthMenu": "Tampilkan _MENU_ entri per halaman",
                    "zeroRecords": "Tidak ditemukan data yang cocok",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Tidak ada data tersedia",
                    "infoFiltered": "(disaring dari total _MAX_ entri)",
                    "search": "Cari:",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });
        });
    </script>

    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
@endsection
