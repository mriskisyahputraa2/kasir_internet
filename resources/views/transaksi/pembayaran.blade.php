{{-- filepath: resources/views/transaksi/pembayaran.blade.php --}}
@extends('layouts.app')

@section('title', 'Kasir Internet')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header" style="background-color: #633B48; color: #fff;">
                        <h5 class="card-title mb-0 text-white">
                            Detail Transaksi #{{ $transaksi->id }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead style="background-color: #633B48; color: #fff;">
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
                                            <td>{{ $detail->produk->nama }}</td>
                                            <td>Rp{{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                            <td>{{ $detail->jumlah }}</td>
                                            <td>Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #f5e9ec;">
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th>Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header" style="background-color: #633B48; color: #fff;">
                        <h5 class="card-title mb-0 text-white">Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <!-- Form Pembayaran Utama -->
                        <form action="{{ route('transaksi.complete', $transaksi->id) }}" method="POST" id="paymentForm">
                            @csrf
                            <div class="mb-3">
                                <label for="total_harga" class="form-label">Total Harga:</label>
                                <input type="text" id="total_harga" class="form-control"
                                    value="Rp{{ number_format($transaksi->total_harga + $totalDiskon, 0, ',', '.') }}"
                                    readonly />
                            </div>
                            <div class="mb-3">
                                <label for="diskon" class="form-label">Total Diskon:</label>
                                <input type="text" id="diskon" class="form-control"
                                    value="Rp{{ number_format($totalDiskon, 0, ',', '.') }}" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="total_setelah_diskon" class="form-label">Total Setelah Diskon:</label>
                                <input type="text" id="total_setelah_diskon" class="form-control"
                                    value="Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="bayar" class="form-label">Jumlah Bayar:</label>
                                <input type="text" name="bayar" id="bayar" class="form-control" value="Rp 0"
                                    required />
                                <div id="bayar-error" class="text-danger small" style="display: none;"></div>
                            </div>
                            <div class="mb-3 pt-2 border-top">
                                <label for="kembalian" class="form-label">Kembalian:</label>
                                <input type="text" id="kembalian" class="form-control fw-bold" readonly />
                            </div>

                            <!-- Hidden inputs untuk nilai asli -->
                            <input type="hidden" name="diskon_value" id="diskon_value" value="0">
                            <input type="hidden" name="bayar_value" id="bayar_value" value="0">

                            {{-- <div class="btn-action-group d-grid gap-2">
                                <button type="submit" class="btn btn-success" id="payButton" disabled>
                                    <span id="payButtonText">
                                        <i class="bi bi-cash-stack me-2"></i>Bayar
                                    </span>
                                    <span id="payButtonLoading" style="display: none;">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Memproses...
                                    </span>
                                </button>
                            </div> --}}
                            <div class="btn-action-group d-grid gap-2">
                                <button type="submit" class="btn btn-success" id="payButton" disabled>
                                    <span id="payButtonText">
                                        <i class="bi bi-cash-stack me-2"></i>Bayar
                                    </span>
                                    <span id="payButtonLoading" style="display: none;">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Memproses...
                                    </span>
                                </button>
                            </div>
                        </form>

                        <!-- Form Terpisah untuk Aksi Lain -->
                        <div class="mt-3 d-flex gap-2">
                            <form action="{{ route('transaksi.bayar.nanti', $transaksi->id) }}" method="POST"
                                class="flex-fill">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100" id="bayarNantiBtn">
                                    <span id="bayarNantiText">
                                        <i class="bi bi-clock-history me-2"></i>Bayar Nanti
                                    </span>
                                    <span id="bayarNantiLoading" style="display: none;">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Memproses...
                                    </span>
                                </button>
                            </form>

                            <form action="{{ route('transaksi.cancel', $transaksi->id) }}" method="POST" class="flex-fill"
                                onsubmit="return confirmCancel()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100" id="cancelBtn">
                                    <span id="cancelText">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </span>
                                    <span id="cancelLoading" style="display: none;">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Memproses...
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Ambil total setelah diskon dari backend (sudah otomatis)
            const totalSetelahDiskon = parseInt("{{ $transaksi->total_harga }}");
            const bayarInput = document.getElementById("bayar");
            const bayarError = document.getElementById("bayar-error");
            const kembalianDisplay = document.getElementById("kembalian");
            const payButton = document.getElementById("payButton");

            function convertToAngka(rupiah) {
                return parseInt(rupiah.replace(/[^0-9]/g, "")) || 0;
            }

            function formatRupiah(angka) {
                let numberString = angka.toString();
                let sisa = numberString.length % 3;
                let rupiah = numberString.substr(0, sisa);
                let ribuan = numberString.substr(sisa).match(/\d{3}/g);
                if (ribuan) {
                    let separator = sisa ? "." : "";
                    rupiah += separator + ribuan.join(".");
                }
                return "Rp " + rupiah;
            }

            function updateKembalian() {
                let bayar = convertToAngka(bayarInput.value);
                let kembalian = bayar - totalSetelahDiskon;
                kembalianDisplay.value = formatRupiah(kembalian >= 0 ? kembalian : 0);

                if (bayar >= totalSetelahDiskon) {
                    payButton.disabled = false;
                    bayarError.style.display = "none";
                } else {
                    payButton.disabled = true;
                    bayarError.style.display = "block";
                    bayarError.textContent = "Jumlah bayar kurang dari total setelah diskon.";
                }
            }

            bayarInput.addEventListener("input", function() {
                let angka = convertToAngka(this.value);
                this.value = formatRupiah(angka);
                updateKembalian();
            });

            // Inisialisasi
            updateKembalian();

            // Loading states untuk form submissions
            document.getElementById('paymentForm').addEventListener('submit', function() {
                showLoading('payButton', 'payButtonText', 'payButtonLoading');
            });

            document.querySelector('form[action*="bayar.nanti"]').addEventListener('submit', function() {
                showLoading('bayarNantiBtn', 'bayarNantiText', 'bayarNantiLoading');
            });

            document.querySelector('form[action*="cancel"]').addEventListener('submit', function() {
                showLoading('cancelBtn', 'cancelText', 'cancelLoading');
            });

            function showLoading(buttonId, textId, loadingId) {
                const button = document.getElementById(buttonId);
                const text = document.getElementById(textId);
                const loading = document.getElementById(loadingId);

                button.disabled = true;
                text.style.display = 'none';
                loading.style.display = 'inline';
            }
        });

        function confirmCancel() {
            return confirm('Apakah Anda yakin ingin membatalkan transaksi ini?');
        }
    </script>

    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            // const totalHarga = parseFloat("{{ $transaksi->total_harga }}");
            const diskonInput = document.getElementById("diskon");
            const diskonValue = document.getElementById("diskon_value");
            const diskonError = document.getElementById("diskon-error");
            const totalSetelahDiskonDisplay = document.getElementById("total_setelah_diskon");
            // const bayarInput = document.getElementById("bayar");
            const bayarValue = document.getElementById("bayar_value");
            // const bayarError = document.getElementById("bayar-error");
            // const kembalianDisplay = document.getElementById("kembalian");
            // const payButton = document.getElementById("payButton");

            const totalSetelahDiskon = parseInt("{{ $transaksi->total_harga }}");
            const bayarInput = document.getElementById("bayar");
            const bayarError = document.getElementById("bayar-error");
            const kembalianDisplay = document.getElementById("kembalian");
            const payButton = document.getElementById("payButton");

            function formatRupiah(angka) {
                if (isNaN(angka) || angka < 0) angka = 0;
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
                const cleaned = rupiah.replace(/[^0-9]/g, "");
                return cleaned === "" ? 0 : parseFloat(cleaned);
            }

            function validateDiskon(diskon) {
                diskonError.style.display = "none";
                diskonError.textContent = "";

                if (diskon > totalHarga) {
                    diskonError.textContent = "Diskon tidak boleh lebih besar dari total harga";
                    diskonError.style.display = "block";
                    return false;
                }
                return true;
            }

            function validateBayar(bayar, totalSetelahDiskon) {
                bayarError.style.display = "none";
                bayarError.textContent = "";

                if (bayar <= 0) {
                    bayarError.textContent = "Jumlah bayar harus lebih dari 0";
                    bayarError.style.display = "block";
                    return false;
                }

                if (bayar < totalSetelahDiskon) {
                    bayarError.textContent = "Jumlah bayar kurang dari total yang harus dibayar";
                    bayarError.style.display = "block";
                    return false;
                }

                return true;
            }

            function validatePayment() {
                const diskon = convertToAngka(diskonInput.value);
                const totalSetelahDiskon = totalHarga - diskon;
                const bayar = convertToAngka(bayarInput.value);

                const diskonValid = validateDiskon(diskon);
                const bayarValid = validateBayar(bayar, totalSetelahDiskon);

                payButton.disabled = !(diskonValid && bayarValid);
            }

            function updatePembayaran() {
                const diskon = convertToAngka(diskonInput.value);
                const totalSetelahDiskon = Math.max(0, totalHarga - diskon);
                const bayar = convertToAngka(bayarInput.value);
                const kembalian = Math.max(0, bayar - totalSetelahDiskon);

                // Update display
                totalSetelahDiskonDisplay.value = formatRupiah(totalSetelahDiskon);
                kembalianDisplay.value = formatRupiah(kembalian);

                // Update hidden inputs
                diskonValue.value = diskon;
                bayarValue.value = bayar;

                // Validate
                validatePayment();
            }

            // Event listeners untuk input
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

            // Loading states untuk form submissions
            document.getElementById('paymentForm').addEventListener('submit', function() {
                showLoading('payButton', 'payButtonText', 'payButtonLoading');
            });

            document.querySelector('form[action*="bayar.nanti"]').addEventListener('submit', function() {
                showLoading('bayarNantiBtn', 'bayarNantiText', 'bayarNantiLoading');
            });

            document.querySelector('form[action*="cancel"]').addEventListener('submit', function() {
                showLoading('cancelBtn', 'cancelText', 'cancelLoading');
            });

            function showLoading(buttonId, textId, loadingId) {
                const button = document.getElementById(buttonId);
                const text = document.getElementById(textId);
                const loading = document.getElementById(loadingId);

                button.disabled = true;
                text.style.display = 'none';
                loading.style.display = 'inline';
            }

            // Initialize
            updatePembayaran();
        });

        function confirmCancel() {
            return confirm('Apakah Anda yakin ingin membatalkan transaksi ini?');
        }
    </script> --}}

    <style>
        .btn-action-group .btn {
            min-height: 45px;
        }

        .form-control:focus {
            border-color: #633B48;
            box-shadow: 0 0 0 0.2rem rgba(99, 59, 72, 0.25);
        }

        .text-danger {
            font-size: 0.875rem;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
    </style>
@endsection
