@extends('layouts.app')

@section('title', 'Kasir Internet')

@section('content')
    <div class="pagetitle">
        <h1 style="color: #633B48; font-weight: 600;">Manajemen Pindahan Saldo</h1>
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
                            <h5 class="card-title m-0">Pindahan Saldo</h5>
                            <button type="button" class="btn text-white"
                                style="background-color: #633B48"data-bs-toggle="modal" data-bs-target="#tambahModal">
                                Pindah Saldo
                            </button>
                        </div>

                        <!-- Form Filter Tanggal -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form action="{{ route('pindahan-dana.index') }}" method="GET" id="filterForm">
                                    <div class="input-group">
                                        <span class="input-group-text">Dari</span>
                                        <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai"
                                            value="{{ request('tanggal_mulai', date('Y-m-d')) }}">
                                        <span class="input-group-text">Sampai</span>
                                        <input type="date" class="form-control" name="tanggal_akhir" id="tanggal_akhir"
                                            value="{{ request('tanggal_akhir', date('Y-m-d')) }}">
                                        <button class="btn btn-outline-secondary" type="submit">Filter</button>
                                        <a href="{{ route('pindahan-dana.index') }}"
                                            class="btn btn-outline-danger">Reset</a>
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
                                        <th>Pemindah</th>
                                        <th>Platform</th>
                                        <th>Saldo Pengirim</th>
                                        <th>Saldo Penerima</th>
                                        <th>Nominal</th>
                                        <th>Detail</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $itemData)
                                        <tr>
                                            <td>{{ $itemData->id }}</td>
                                            <td>
                                                <ul class="list-unstyled mb-0">
                                                    <li class="d-flex align-items-center">
                                                        <i class="bi bi-person me-2 text-primary"></i>
                                                        {{ optional($itemData->user)->nama }}
                                                    </li>
                                                    <li class="d-flex align-items-center">
                                                        <i class="bi bi-calendar me-2 text-success"></i>
                                                        {{ $itemData->updated_at->translatedFormat('l, d M Y') }}
                                                    </li>
                                                </ul>
                                            </td>
                                            <td>
                                                {{ optional($itemData->dariSaldo)->nama_platform ?? 'Tidak Ditemukan' }}
                                                <i class="bi bi-arrow-right-short"></i>
                                                {{ optional($itemData->tujuanSaldo)->nama_platform ?? 'Tidak Ditemukan' }}
                                            </td>
                                            <td>
                                                Rp{{ number_format($itemData->saldo_awal_dari) }}
                                                <span class="text-danger">
                                                    <br />
                                                    <i class="bi bi-arrow-return-right"></i>
                                                    Rp{{ number_format($itemData->saldo_akhir_dari, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                Rp{{ number_format($itemData->saldo_awal_tujuan, 2) }}
                                                <span class="text-success">
                                                    <br />
                                                    <i class="bi bi-arrow-return-right"></i>
                                                    Rp{{ number_format($itemData->saldo_akhir_tujuan, 2) }}
                                                </span>
                                            </td>
                                            <td>Rp{{ number_format($itemData->nominal, 2) }}</td>
                                            <td>
                                                <ul class="list-unstyled mb-0">
                                                    <li class="d-flex align-items-center">
                                                        <span class="text-muted small pe-2">Operasional:</span>
                                                        Rp{{ number_format($itemData->operasional) }}
                                                    </li>
                                                    <li class="d-flex align-items-center">
                                                        <span class="text-muted small pe-2">Admin:</span>
                                                        Rp{{ number_format($itemData->admin) }}
                                                    </li>
                                                    <li class="d-flex align-items-center">
                                                        <span class="text-muted small pe-2">Keterangan:</span>
                                                        {{ $itemData->keterangan }}
                                                    </li>
                                                </ul>
                                            </td>
                                            <td>
                                                <div class="filter">
                                                    <a class="icon" href="#" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                        <li>
                                                            <form
                                                                action="{{ route('pindahan-dana.destroy', $itemData->id) }}"
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <!-- Modal Tambah -->
    <div class="modal fade text-dark " id="tambahModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header ">
                    <h5 class="modal-title">Tambah Pindahan Saldo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('pindahan-dana.store') }}" method="POST" id="formTambahPindahan">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Dari (Sumber)</label>
                            <select name="dari" class="form-control" required>
                                <option value="">-- Pilih Sumber --</option>
                                @foreach ($saldo as $itemSaldo)
                                    <option value="{{ $itemSaldo->id }}">
                                        {{ $itemSaldo->nama_platform }} - Rp{{ number_format($itemSaldo->saldo, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tujuan</label>
                            <select name="tujuan" class="form-control" required>
                                <option value="">-- Pilih Tujuan --</option>
                                @foreach ($saldo as $itemSaldo)
                                    <option value="{{ $itemSaldo->id }}">
                                        {{ $itemSaldo->nama_platform }} - Rp{{ number_format($itemSaldo->saldo, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nominal</label>
                            <input type="text" name="nominal" id="nominal" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
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

            // Konversi nilai ke angka sebelum submit
            document.getElementById('formTambahPindahan').addEventListener('submit', function(e) {
                var nominalInput = document.getElementById('nominal');
                var nominalValue = nominalInput.value.replace(/[^0-9]/g,
                    ''); // Hapus semua karakter non-angka
                nominalInput.value = nominalValue; // Set nilai asli ke input
            });

            // $(document).ready(function() {
            //     $('#saldoAwalTable').DataTable({
            //         responsive: true,
            //         language: {
            //             "lengthMenu": "Tampilkan _MENU_ entri per halaman",
            //             "zeroRecords": "Tidak ditemukan data yang cocok",
            //             "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            //             "infoEmpty": "Tidak ada data tersedia",
            //             "infoFiltered": "(disaring dari total _MAX_ entri)",
            //             "search": "Cari:",
            //             "paginate": {
            //                 "first": "Pertama",
            //                 "last": "Terakhir",
            //                 "next": "Berikutnya",
            //                 "previous": "Sebelumnya"
            //             }
            //         }
            //     });
            // });
            // Inisialisasi DataTables
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
        });
    </script>
@endsection
