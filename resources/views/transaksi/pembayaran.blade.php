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

    <style>
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .fixed-top {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1030;
        }

        .container {
            margin-top: 80px;
        }

        .form-control[readonly] {
            background-color: #f8f9fa;
        }

        .btn-action-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn-action-group .btn {
            flex: 1;
        }

        @media (min-width: 768px) {
            .btn-action-group .btn {
                flex: none;
            }
        }

        .btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center logo gap-3">
            <a href="/dashboard" class="d-flex align-items-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" />
                <span class="d-none d-lg-block">Kasir Internet</span>
            </a>
            <a href="/dashboard">
                <button class="btn btn-primary btn-circle"
                    style="
                            width: 40px;
                            height: 40px;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 24px;
                            color: white;
                        ">
                    <i class="bi bi-inboxes-fill text-white"></i>
                </button>
            </a>
        </div>

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">
                <li class="nav-item dropdown pe-3">
                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#"
                        data-bs-toggle="dropdown">
                        <span class="d-none d-md-block ps-2">{{ session('user')['nama'] }}</span>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0 text-white">
                            Detail Transaksi #{{ $transaksi->id }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaksi->details as $detail)
                                        <tr>
                                            <td>
                                                {{ $detail->produk->nama_produk }}
                                            </td>
                                            <td>
                                                Rp{{ number_format($detail->harga_satuan, 0, ',', '.') }}
                                            </td>
                                            <td>{{ $detail->jumlah }}</td>
                                            <td>
                                                Rp{{ number_format($detail->subtotal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-active">
                                        <th colspan="3" class="text-end">
                                            Total:
                                        </th>
                                        <th>
                                            Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0 text-white">Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('transaksi.complete', $transaksi->id) }}" method="POST"
                            id="paymentForm">
                            @csrf
                            <div class="mb-3">
                                <label for="total_harga" class="form-label">Total Harga:</label>
                                <input type="text" id="total_harga" class="form-control"
                                    value="Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}" readonly />
                            </div>

                            <div class="mb-3">
                                <label for="diskon" class="form-label">Diskon:</label>
                                <input type="text" name="diskon" id="diskon" class="form-control"
                                    value="Rp 0" />
                            </div>

                            <div class="mb-3">
                                <label for="total_setelah_diskon" class="form-label">Total Setelah Diskon:</label>
                                <input type="text" id="total_setelah_diskon" class="form-control" readonly />
                            </div>

                            <div class="mb-3">
                                <label for="bayar" class="form-label">Jumlah Bayar:</label>
                                <input type="text" name="bayar" id="bayar" class="form-control"
                                    value="Rp 0" required />
                            </div>

                            <div class="mb-3 pt-2 border-top">
                                <label for="kembalian" class="form-label">Kembalian:</label>
                                <input type="text" id="kembalian" class="form-control fw-bold" readonly />
                            </div>

                            <div class="btn-action-group">
                                <form action="{{ route('transaksi.bayar.nanti', $transaksi->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-clock-history me-2"></i>Bayar Nanti
                                    </button>
                                </form>

                                <form action="{{ route('transaksi.cancel', $transaksi->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger me-2">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </button>
                                </form>

                                <button type="submit" class="btn btn-success" id="payButton" disabled>
                                    <i class="bi bi-cash-stack me-2"></i>Bayar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const totalHarga = parseFloat("{{ $transaksi->total_harga }}");
            const diskonInput = document.getElementById("diskon");
            const totalSetelahDiskonDisplay = document.getElementById(
                "total_setelah_diskon"
            );
            const bayarInput = document.getElementById("bayar");
            const kembalianDisplay = document.getElementById("kembalian");
            const payButton = document.getElementById("payButton");

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

            function convertToAngka(rupiah) {
                return parseFloat(rupiah.replace(/[^0-9]/g, ""));
            }

            function validatePayment() {
                const bayar = convertToAngka(bayarInput.value) || 0;

                // Tombol bayar aktif hanya jika:
                // 1. Jumlah bayar > 0
                // 2. Tujuan saldo sudah dipilih
                payButton.disabled = bayar <= 0;
            }

            function updatePembayaran() {
                const diskon = convertToAngka(diskonInput.value) || 0;
                const totalSetelahDiskon = totalHarga - diskon;
                const bayar = convertToAngka(bayarInput.value) || 0;
                const kembalian = bayar - totalSetelahDiskon;

                totalSetelahDiskonDisplay.value =
                    formatRupiah(totalSetelahDiskon);
                kembalianDisplay.value =
                    kembalian >= 0 ?
                    formatRupiah(kembalian) :
                    formatRupiah(0);

                validatePayment();
            }

            // Event listeners
            diskonInput.addEventListener("input", function() {
                const angka = convertToAngka(this.value);
                this.value = formatRupiah(angka);
                updatePembayaran();
            });

            bayarInput.addEventListener("input", function() {
                const angka = convertToAngka(this.value);
                this.value = formatRupiah(angka);
                updatePembayaran();
            });

            // Validasi awal
            updatePembayaran();
        });
    </script>
</body>

</html>
