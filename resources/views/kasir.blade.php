@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
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
    <div class="">
        <div class="d-flex justify-content-between align-items-center">
            <div class="pagetitle">
                <h1>Manajemen Transaksi</h1>
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
            <h5 class="card-title">
                <span class="fw-normal"> Total Saldo: </span> Rp
                {{ number_format($totalSaldo, 0, ',', '.') }}
            </h5>
        </div>
        <div class="">
            <div class="row p-0">
                @foreach ($tambahSaldos as $saldo)
                    <div class="col-md-3">
                        <div class="card info-card customers-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center pt-3">
                                    <div class="">
                                        <p class="text-body-emphasis fw-medium fs-5">
                                            {{ $saldo->nama_platform }}
                                        </p>
                                        <h6 class="fs-4 fw-bolder">
                                            Rp
                                            {{ number_format($saldo->saldo, 0, ',', '.') }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title">Data Transaksi</h5>
                                <div>
                                    <button class="btn btn-primary pilih-transaksi" data-jenis="tarik_tunai"
                                        data-bs-toggle="modal" data-bs-target="#createTransaksiModal">
                                        Tarik Tunai
                                    </button>
                                    <button class="btn btn-success pilih-transaksi" data-jenis="transfer"
                                        data-bs-toggle="modal" data-bs-target="#createTransaksiModal">
                                        Transfer
                                    </button>
                                    <button class="btn btn-warning pilih-transaksi" data-jenis="jasa_transfer"
                                        data-bs-toggle="modal" data-bs-target="#createTransaksiModal">
                                        Jasa Transfer
                                    </button>
                                    <button class="btn btn-info pilih-transaksi" data-jenis="mode_pulsa"
                                        data-bs-toggle="modal" data-bs-target="#createTransaksiModal">
                                        Mode Pulsa
                                    </button>
                                </div>
                            </div>

                            <!-- Table with stripped rows -->
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>Nomor</th>
                                        <th>Jenis Transaksi</th>
                                        <th>Platform</th>
                                        <th>Nominal</th>
                                        <th>Saldo Sumber</th>
                                        <th>Saldo Penerima</th>
                                        <th>Admin</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaksis as $key => $item)
                                        <tr>
                                            <td>{{ $item->nomor_transaksi }}</td>
                                            <td>{{ $item->jenis_transaksi }}</td>
                                            <td>
                                                {{ $item->sumberDana->nama_platform ?? 'Tidak Diketahui' }}
                                                <i class="bi bi-arrow-left-right"></i>
                                                {{ $item->terimaDana->nama_platform ?? 'Tidak Diketahui' }}
                                            </td>
                                            <td>
                                                Rp
                                                {{ number_format($item->nominal_transaksi, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                Rp {{ number_format($item->dana_awal_sumber, 0, ',', '.') }}
                                                <span class="text-danger">
                                                    <br>
                                                    <i class="bi bi-arrow-return-right"></i>
                                                    Rp {{ number_format($item->dana_akhir_sumber, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td>
                                                Rp {{ number_format($item->dana_awal_terima, 0, ',', '.') }}
                                                <span class="text-success">
                                                    <br>
                                                    <i class="bi bi-arrow-return-right"></i>
                                                    Rp {{ number_format($item->dana_akhir_terima, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td>
                                                <ul class="list-unstyled mb-0">
                                                    <li class="d-flex align-items-center">
                                                        <span class="text-muted small pe-2">
                                                            Admin dalam:
                                                        </span>
                                                        Rp{{ number_format($item->admin_dalam, 0, ',', '.') }}
                                                    </li>
                                                    <li class="d-flex align-items-center">
                                                        <span class="text-muted small pe-2">
                                                            Admin Luar:
                                                        </span>
                                                        Rp{{ number_format($item->admin_luar, 0, ',', '.') }}
                                                    </li>
                                                    <li class="d-flex align-items-center">
                                                        <span class="text-muted small pe-2">
                                                            Admin Bank:
                                                        </span>
                                                        Rp{{ number_format($item->admin_bank, 0, ',', '.') }}
                                                    </li>
                                                    <li class="d-flex align-items-center">
                                                        <span class="text-muted small pe-2">
                                                            Keterangan:
                                                        </span>
                                                        {{ $item->keterangan }}
                                                    </li>
                                                </ul>
                                            </td>
                                            <td>
                                                <div class="filter">
                                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                                            class="bi bi-three-dots"></i></a>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                        <li>
                                                            <form action="{{ route('kasir.destroy', $item->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Yakin ingin menghapus?')">
                                                                @csrf @method('DELETE')
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
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal Tambah Transaksi -->
        <div class="modal fade" id="createTransaksiModal" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('kasir.store') }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                Tambah Transaksi (<span id="modalJenisTransaksi"></span>)
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <!-- Jenis Transaksi (Hidden Input) -->
                            <input type="hidden" name="jenis_transaksi" id="inputJenisTransaksi" />

                            <!-- Tipe Transaksi -->
                            <div class="mb-3 Transaksi_tipe">
                                <label for="tipe_transaksi">Tipe Transaksi</label>
                                <input list="tipe_transaksi_list" name="tipe_transaksi" id="tipe_transaksi"
                                    class="form-control" placeholder="Pilih atau ketik tipe transaksi" />
                                <datalist id="tipe_transaksi_list">
                                    <option value="Antar Bank"></option>
                                    <option value="Sama Bank"></option>
                                    <option value="Lainnya"></option>
                                </datalist>
                            </div>

                            <!-- Sumber Dana -->
                            <div class="mb-3 transaksi-sumber-dana">
                                <label>Sumber Dana:</label>
                                <select name="sumber_dana" class="form-control">
                                    @foreach ($tambahSaldos as $saldo)
                                        <option value="{{ $saldo->id }}">
                                            {{ $saldo->nama_platform }} (Rp
                                            {{ number_format($saldo->saldo, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Penerima Dana -->
                            <div class="mb-3 transaksi-penerima-dana">
                                <label>Penerima Dana:</label>
                                <select name="terima_dana" class="form-control">
                                    @foreach ($tambahSaldos as $saldo)
                                        <option value="{{ $saldo->id }}">
                                            {{ $saldo->nama_platform }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Nominal Transaksi -->
                            <div class="mb-3 transaksi-nominal">
                                <label>Nominal Transaksi:</label>
                                <input type="text" name="nominal_transaksi" class="form-control" value="Rp 0" />
                            </div>

                            <!-- Admin Dalam -->
                            <div class="mb-3 transaksi-admin-dalam">
                                <label>Admin Dalam:</label>
                                <input type="text" name="admin_dalam" class="form-control" value="Rp 0" />
                            </div>

                            <!-- Admin Luar -->
                            <div class="mb-3 transaksi-admin-luar">
                                <label>Admin Luar:</label>
                                <input type="text" name="admin_luar" class="form-control" value="Rp 0" />
                            </div>

                            <!-- Admin Bank -->
                            <div class="mb-3 transaksi-admin-bank">
                                <label>Admin Bank:</label>
                                <input type="text" name="admin_bank" class="form-control" value="Rp 0" />
                            </div>

                            <!-- Nomor Tujuan -->
                            <div class="mb-3 transaksi-nomor-tujuan">
                                <label>Nomor Tujuan:</label>
                                <input type="text" name="nomor_tujuan" class="form-control" />
                            </div>

                            <!-- Keterangan -->
                            <div class="mb-3 transaksi-keterangan">
                                <label>Keterangan:</label>
                                <textarea name="keterangan" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Simpan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Fungsi untuk memformat angka ke format Rupiah
            function formatRupiah(angka) {
                const numberString = angka.toString();
                const sisa = numberString.length % 3;
                let rupiah = numberString.substr(0, sisa);
                const ribuan = numberString.substr(sisa).match(/\d{3}/g);

                if (ribuan) {
                    const separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                return 'Rp ' + rupiah;
            }

            // Fungsi untuk mengonversi format Rupiah ke angka
            function convertToAngka(rupiah) {
                return parseFloat(rupiah.replace(/[^0-9]/g, ''));
            }

            // Terapkan format Rupiah pada input yang relevan
            const inputNominal = document.querySelector('input[name="nominal_transaksi"]');
            const inputAdminDalam = document.querySelector('input[name="admin_dalam"]');
            const inputAdminLuar = document.querySelector('input[name="admin_luar"]');
            const inputAdminBank = document.querySelector('input[name="admin_bank"]');

            // Format input nominal transaksi
            if (inputNominal) {
                inputNominal.addEventListener("input", function() {
                    const angka = convertToAngka(this.value);
                    this.value = formatRupiah(angka);
                });
            }

            // Format input admin dalam
            if (inputAdminDalam) {
                inputAdminDalam.addEventListener("input", function() {
                    const angka = convertToAngka(this.value);
                    this.value = formatRupiah(angka);
                });
            }

            // Format input admin luar
            if (inputAdminLuar) {
                inputAdminLuar.addEventListener("input", function() {
                    const angka = convertToAngka(this.value);
                    this.value = formatRupiah(angka);
                });
            }

            // Format input admin bank
            if (inputAdminBank) {
                inputAdminBank.addEventListener("input", function() {
                    const angka = convertToAngka(this.value);
                    this.value = formatRupiah(angka);
                });
            }

            // Fungsi untuk menampilkan input sesuai jenis transaksi
            function tampilkanInputTransaksi(jenis) {
                // Sembunyikan semua input terlebih dahulu
                document.querySelectorAll(".modal-body .mb-3").forEach((div) => {
                    div.style.display = "none";
                });

                // Tampilkan input yang relevan berdasarkan jenis transaksi
                document.querySelector(".transaksi-nominal").style.display = "block";
                document.querySelector(".transaksi-sumber-dana").style.display = "block";
                document.querySelector(".transaksi-penerima-dana").style.display = "block";
                document.querySelector(".transaksi-admin-luar").style.display = "block";
                document.querySelector(".transaksi-keterangan").style.display = "block";

                if (jenis === "transfer") {
                    document.querySelector(".transaksi-admin-bank").style.display = "block";
                    document.querySelector(".Transaksi_tipe").style.display = "block";
                    document.querySelector(".transaksi-admin-dalam").style.display = "block";
                } else if (jenis === "tarik_tunai") {
                    document.querySelector(".transaksi-admin-dalam").style.display = "block";
                } else if (jenis === "jasa_transfer") {
                    document.querySelector(".transaksi-admin-dalam").style.display = "block";
                } else if (jenis === "mode_pulsa") {
                    document.querySelector(".transaksi-nomor-tujuan").style.display = "block";
                }
            }

            // Event listener untuk tombol pilih transaksi
            document.querySelectorAll(".pilih-transaksi").forEach((button) => {
                button.addEventListener("click", function() {
                    const jenis = this.getAttribute("data-jenis");

                    // Set judul modal sesuai jenis transaksi
                    document.getElementById("modalJenisTransaksi").textContent = this.textContent;

                    // Set nilai input jenis transaksi (hidden)
                    document.getElementById("inputJenisTransaksi").value = jenis;

                    // Tampilkan input yang relevan
                    tampilkanInputTransaksi(jenis);
                });
            });

            // Konversi nilai sebelum mengirim form
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener("submit", function(e) {
                    // Konversi nilai input ke angka sebelum submit
                    if (inputNominal) {
                        inputNominal.value = convertToAngka(inputNominal.value);
                    }
                    if (inputAdminDalam) {
                        inputAdminDalam.value = convertToAngka(inputAdminDalam.value);
                    }
                    if (inputAdminLuar) {
                        inputAdminLuar.value = convertToAngka(inputAdminLuar.value);
                    }
                    if (inputAdminBank) {
                        inputAdminBank.value = convertToAngka(inputAdminBank.value);
                    }
                });
            }
        });
    </script>
@endsection
