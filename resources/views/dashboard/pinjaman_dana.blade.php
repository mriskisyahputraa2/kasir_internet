@extends('layouts.app')

@section('title', 'Kasir Internet')

@section('content')
    <div class="pagetitle">
        <h1 style="color: #633B48; font-weight: 600;">Manajemen Pinjaman</h1>
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
                    <h5 class="card-title m-0">Pinjaman Dana</h5>
                    <div>
                        <button type="button"class="btn text-white" style="background-color: #633B48"
                            data-bs-toggle="modal" data-bs-target="#modalTambahPinjaman">
                            Pinjaman
                        </button>
                    </div>
                </div>

                <!-- Form Filter Tanggal -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form action="{{ route('pinjaman-dana.index') }}" method="GET" id="filterForm">
                            <div class="input-group">
                                <span class="input-group-text">Dari</span>
                                <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai"
                                    value="{{ request('tanggal_mulai', date('Y-m-d')) }}">
                                <span class="input-group-text">Sampai</span>
                                <input type="date" class="form-control" name="tanggal_akhir" id="tanggal_akhir"
                                    value="{{ request('tanggal_akhir', date('Y-m-d')) }}">
                                <button class="btn btn-outline-secondary" type="submit">Filter</button>
                                <a href="{{ route('pinjaman-dana.index') }}" class="btn btn-outline-danger">Reset</a>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end">
                            <select class="form-select" style="width: auto;" id="statusFilter">
                                <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status
                                </option>
                                <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam
                                </option>
                                <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>
                                    Dikembalikan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table with stripped rows -->
                <div class="table-responsive">
                    <table class="table datatable" id="myTable">

                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Peminjam</th>
                                <th scope="col">Sumber Dana</th>
                                <th scope="col">Saldo Awal</th>
                                <th scope="col">Saldo Akhir</th>
                                <th scope="col">Detail</th>
                                <th scope="col">Status</th>
                                @if ($user['role'] === 'superadmin')
                                    <th scope="col"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <th scope="row">{{ $item->id }}</th>
                                    <td>
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-flex align-items-center">
                                                <i class="bi bi-person me-2 text-primary"></i> {{ $item->creator->nama }}
                                            </li>
                                            <li class="d-flex align-items-center">
                                                <i class="bi bi-calendar me-2 text-success"></i>
                                                {{ $item->updated_at->translatedFormat('l, d M Y') }}
                                            </li>
                                        </ul>
                                    </td>
                                    <td>{{ $item->sumber->nama_platform }}</td>
                                    <td>Rp{{ number_format($item->saldo_awal, 0, ',', '.') }}</td>
                                    <td>Rp{{ number_format($item->saldo_akhir, 0, ',', '.') }}</td>
                                    <td>
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-flex align-items-center">
                                                <span class="text-muted small pe-2">Nominal:</span>
                                                Rp{{ number_format($item->nominal, 0, ',', '.') }}
                                            </li>
                                            <li class="d-flex align-items-center">
                                                <span class="text-muted small pe-2">Admin:</span>
                                                Rp{{ number_format($item->admin, 0, ',', '.') }}
                                            </li>
                                            <li class="d-flex align-items-center">
                                                <span class="text-muted small pe-2">Keterangan:</span>
                                                {{ $item->keterangan }}
                                            </li>
                                        </ul>
                                    </td>
                                    <td>
                                        <span
                                            class="badge rounded-pill {{ $item->status == 'dipinjam' ? 'bg-danger' : 'bg-success' }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    @if ($user['role'] === 'superadmin')
                                        <td>
                                            <div class="filter">
                                                <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                                        class="bi bi-three-dots"></i></a>
                                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                    @if ($item->status == 'dipinjam')
                                                        <li>
                                                            <button type="button" class="dropdown-item text-success"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalKembalikanPinjaman"
                                                                data-id="{{ $item->id }}">Kembalikan</button>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <form action="{{ route('pinjaman-dana.destroy', $item->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Yakin ingin menghapus?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="dropdown-item text-danger">Hapus</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
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

        <!-- Modal Tambah Pinjaman -->
        <div class="modal fade text-dark" id="modalTambahPinjaman" tabindex="-1" aria-labelledby="modalTambahPinjamanLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahPinjamanLabel">Tambah Pinjaman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('pinjaman-dana.store') }}" method="POST" id="formTambahPinjaman">
                            @csrf
                            <div class="mb-3">
                                <label for="dari" class="form-label">Dari</label>
                                <select name="dari" class="form-control" required>
                                    <option value="">-- Pilih Sumber Saldo --</option>
                                    @foreach ($saldo as $s)
                                        <option value="{{ $s->id }}">
                                            {{ $s->nama }} (Rp {{ number_format($s->saldo, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nominal" class="form-label">Nominal</label>
                                <input type="text" name="nominal" id="nominal" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label for="admin" class="form-label">Admin</label>
                                <input type="text" name="admin" id="admin" class="form-control"
                                    value="0" />
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control"></textarea>
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

        <!-- Modal Konfirmasi Kembalikan Pinjaman -->
        <div class="modal fade text-dark" id="modalKembalikanPinjaman" tabindex="-1"
            aria-labelledby="modalKembalikanPinjamanLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalKembalikanPinjamanLabel">Konfirmasi Pengembalian Pinjaman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formKembalikanPinjaman" method="POST" action="">
                            @csrf
                            <div class="mb-3">
                                <label for="tujuan" class="form-label">Tujuan Pengembalian</label>
                                <select name="tujuan" class="form-control" required>
                                    <option value="">-- Pilih Tujuan Saldo --</option>
                                    @foreach ($saldo as $s)
                                        <option value="{{ $s->id }}">
                                            {{ $s->nama }} (Rp {{ number_format($s->saldo, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Kembalikan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Load Cleave.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Format input nominal ke mata uang (Rp)
                new Cleave('#nominal', {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    prefix: 'Rp ',
                    noImmediatePrefix: true,
                    numeralDecimalMark: ',',
                    delimiter: '.'
                });

                // Format input admin ke mata uang (Rp)
                new Cleave('#admin', {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    prefix: 'Rp ',
                    noImmediatePrefix: true,
                    numeralDecimalMark: ',',
                    delimiter: '.'
                });

                // Konversi nilai ke angka sebelum submit
                document.getElementById('formTambahPinjaman').addEventListener('submit', function(e) {
                    var nominalInput = document.getElementById('nominal');
                    var adminInput = document.getElementById('admin');

                    // Hapus format mata uang dan simpan nilai angka murni
                    nominalInput.value = nominalInput.value.replace(/[^0-9]/g, '');
                    adminInput.value = adminInput.value.replace(/[^0-9]/g, '');
                });

                // Script untuk filter status
                var statusFilter = document.getElementById('statusFilter');
                statusFilter.addEventListener('change', function() {
                    var form = document.getElementById('filterForm');
                    var statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'status';
                    statusInput.value = this.value;

                    // Remove any existing status input
                    var existingInput = form.querySelector('input[name="status"]');
                    if (existingInput) {
                        form.removeChild(existingInput);
                    }

                    form.appendChild(statusInput);
                    form.submit();
                });

                // Set action form pengembalian berdasarkan ID pinjaman
                document.getElementById('modalKembalikanPinjaman').addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget; // Button yang memicu modal
                    var id = button.getAttribute('data-id'); // Ambil ID dari atribut data-id
                    var form = document.getElementById('formKembalikanPinjaman');
                    form.action = `/pinjaman-dana/return/${id}`;
                });
            });

            $('#myTable').DataTable({
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
        </script>
    @endsection
