<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kasir Internet</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <meta name="description" content="" />
    <meta name="keywords" content="" />

    <!-- Favicons -->
    <link href="{{ asset('assets/img/favicon.png') }}" rel="icon" />
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon" />

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet" />

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet" />

    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" />
</head>

<style>
    body {
        background-color: rgba(99, 59, 72, 0.15);
        font-family: 'Poppins', sans-serif;
    }
</style>

<body>

    @include('partials.header')

    @if ($errors->any())
        <div class="alert alert-danger"
            style="margin-top: 50px; position: absolute; top: 20px; left: 30%; width: 800px; z-index: 100;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success"
            style="margin-top: 50px; position: absolute; top: 20px; left: 30%; width: 800px; z-index: 100;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger"
            style="margin-top: 50px; position: absolute; top: 20px; left: 30%; width: 800px; z-index: 100;">
            {{ session('error') }}
        </div>
    @endif

    @php $role = auth()->user()->role; @endphp

    @if (($role === 'kasir' || $role === 'admin') && !$sesiAktif)
        <div class="alert alert-warning mb-4"
            style="margin-top: 50px; position: absolute; top: 20px; left: 30%; width: 800px; z-index: 100;">
            <i class="bi bi-exclamation-triangle-fill"></i> Kasir belum aktif - Silakan buka kasir terlebih dahulu
        </div>
    @endif

    <main class="main {{ !$sesiAktif ? 'disabled-when-closed' : '' }}" style="margin-top: 75px;">
        <div class="mx-5">
            <!-- Header: Judul dan Info Toko -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="pagetitle">
                    <h1 class="fw-bold" style="color: #633B48;">Manajemen Transaksi dan Kasir</h1>

                    @if (session()->has('id_toko'))
                        <nav style="--bs-breadcrumb-divider: ':';">
                            <ol class="breadcrumb small">
                                <li class="breadcrumb-item"><a href="#">Outlet</a></li>
                                <li class="breadcrumb-item active">{{ $toko->nama }}</li>
                            </ol>
                        </nav>
                    @else
                        <p class="text-muted">Toko: Belum dipilih</p>
                    @endif
                </div>

                <div class="text-end">
                    <h5 class="text-dark mb-0">
                        <span class="fw-normal">Total Saldo:</span>
                        <span class="fw-bold text-success">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</span>
                    </h5>
                </div>
            </div>

            <!-- Saldo dari Platform -->
            <div class="row g-3 mb-4">
                @foreach ($tambahSaldos as $saldo)
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body mt-4">
                                {{-- <p class="text-muted mb-1">Platform</p> --}}
                                <h6 class="fw-semibold text-dark">{{ $saldo->nama_platform }}</h6>
                                <p class="mb-0 fw-bold fs-5 text-primary">Rp
                                    {{ number_format($saldo->saldo, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Transaksi -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark">Pilih Barang</h5>
                    <button type="button" class="btn btn-sm text-white" style="background-color: #633B48"
                        data-bs-toggle="modal" data-bs-target="#modalBayarNanti">
                        Lanjut Transaksi
                    </button>
                </div>
                {{-- menampilkan transasksi produk --}}
                <div class="card-body">
                    @include('partials.transaksi')
                </div>
            </div>
        </div>
    </main>

    @include('partials.modals')
    @include('partials.scripts')

    @if (($role === 'kasir' || $role === 'admin') && !$sesiAktif)
        <style>
            .disabled-when-closed {
                position: relative;
                opacity: 0.7;
                pointer-events: none;
            }

            .disabled-when-closed::after {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255, 255, 255, 0.5);
                z-index: 1;
            }
        </style>
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const daftarPembelian =
                document.getElementById("daftar-pembelian");
            const totalHargaInput =
                document.getElementById("total_harga_input");
            const totalHargaDisplay =
                document.getElementById("total_harga");

            function formatRupiah(angka) {
                const numberString = angka.toString();
                const sisa = numberString.length % 3;
                let rupiah = numberString.substr(0, sisa);
                const ribuan = numberString.substr(sisa).match(/\d{3}/g);

                if (ribuan) {
                    const separator = sisa ? "." : "";
                    rupiah += separator + ribuan.join(".");
                }
                return "Rp " + rupiah;
            }

            function updateTotal() {
                let total = 0;
                document
                    .querySelectorAll(".subtotal")
                    .forEach((subtotal) => {
                        total += parseFloat(
                            subtotal.textContent.replace(/[^0-9]/g, "")
                        );
                    });
                totalHargaDisplay.value = formatRupiah(total);
                totalHargaInput.value = total;
            }

            document.querySelectorAll(".produk-card").forEach((card) => {
                card.addEventListener("click", function() {
                    const id = this.getAttribute("data-id");
                    const nama = this.getAttribute("data-nama");
                    const harga = parseFloat(
                        this.getAttribute("data-harga")
                    );
                    const diskon = parseFloat(
                        this.getAttribute("data-diskon")
                    );
                    const jumlah = 1;

                    const existingRow = document.querySelector(
                        `#row-${id}`
                    );
                    if (existingRow) {
                        const jumlahSpan =
                            existingRow.querySelector(".jumlah");
                        const jumlahInput =
                            existingRow.querySelector(".jumlah-input");
                        const subtotalSpan =
                            existingRow.querySelector(".subtotal");

                        let jumlahBaru =
                            parseInt(jumlahSpan.textContent) + 1;
                        jumlahSpan.textContent = jumlahBaru;
                        jumlahInput.value = jumlahBaru;
                        subtotalSpan.textContent = formatRupiah(
                            (harga - diskon) * jumlahBaru
                        );
                    } else {
                        const subtotal = (harga - diskon) * jumlah;

                        const row = document.createElement("tr");
                        row.setAttribute("id", `row-${id}`);
                        row.innerHTML = `
                      <td>${nama}</td>
                      <td>
                          <button type="button" class="btn btn-sm btn-danger kurang">-</button>
                          <span class="jumlah">${jumlah}</span>
                          <button type="button" class="btn btn-sm btn-success tambah">+</button>
                          <input type="hidden" name="produk_id[]" value="${id}">
                          <input type="hidden" name="jumlah[]" value="${jumlah}" class="jumlah-input">
                      </td>
                      <td><span class="subtotal">${formatRupiah(
                          subtotal
                      )}</span></td>
                      <td><button type="button" class="btn btn-danger btn-hapus">X</button></td>
                  `;
                        daftarPembelian.appendChild(row);

                        const tambahButton = row.querySelector(".tambah");
                        const kurangButton = row.querySelector(".kurang");
                        const jumlahSpan = row.querySelector(".jumlah");
                        const subtotalSpan = row.querySelector(".subtotal");
                        const jumlahInput =
                            row.querySelector(".jumlah-input");

                        tambahButton.addEventListener("click", function() {
                            let jumlah =
                                parseInt(jumlahSpan.textContent) + 1;
                            jumlahSpan.textContent = jumlah;
                            jumlahInput.value = jumlah;
                            subtotalSpan.textContent = formatRupiah(
                                (harga - diskon) * jumlah
                            );
                            updateTotal();
                        });

                        kurangButton.addEventListener("click", function() {
                            let jumlah =
                                parseInt(jumlahSpan.textContent) - 1;
                            if (jumlah >= 1) {
                                jumlahSpan.textContent = jumlah;
                                jumlahInput.value = jumlah;
                                subtotalSpan.textContent = formatRupiah(
                                    (harga - diskon) * jumlah
                                );
                                updateTotal();
                            }
                        });

                        const hapusButton = row.querySelector(".btn-hapus");
                        hapusButton.addEventListener("click", function() {
                            row.remove();
                            updateTotal();
                        });
                    }
                    updateTotal();
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const filterKategori =
                document.getElementById("filterKategori");
            const cariProduk = document.getElementById("cariProduk");
            const daftarProduk = document.getElementById("daftar-produk");

            // Fungsi untuk memfilter produk berdasarkan kategori dan pencarian
            function filterProduk() {
                const kategoriId = filterKategori.value;
                const query = cariProduk.value.toLowerCase();

                document
                    .querySelectorAll(".produk-item")
                    .forEach((produk) => {
                        const produkKategori =
                            produk.getAttribute("data-kategori");
                        const produkNama = produk.getAttribute("data-nama");

                        const tampilkan =
                            (kategoriId === "" ||
                                produkKategori === kategoriId) &&
                            produkNama.includes(query);

                        produk.style.display = tampilkan ? "block" : "none";
                    });
            }

            // Event listener untuk filter kategori
            filterKategori.addEventListener("change", filterProduk);

            // Event listener untuk pencarian produk
            cariProduk.addEventListener("input", filterProduk);
        });
    </script>

    <!-- Modal dan Script JavaScript -->
    {{-- popou kasir dan tutup kasir --}}
    @include('partials.modals')

    {{-- script kasir transaksi --}}
    @include('partials.scripts')
    <footer class="text-center py-3" style="font-size: 12px; border-top: 1px solid rgb(214, 231, 247)">
        <div class="copyright">
            {{-- &copy; Copyright <strong><span>Kasir Internet</span></strong>. All Rights Reserved --}}
            &copy; Copyright <strong><span>Kasir Internet</span></strong>. All Rights Reserved
        </div>

    </footer>

    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

    @if (session('swal_error'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: '{{ session('swal_error') }}'
                });
            });
        </script>
    @endif
</body>

</html>
