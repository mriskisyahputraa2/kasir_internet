{{-- filepath: resources/views/transaksi/pembayaran.blade.php --}}
@extends('layouts.app')

@section('title', 'Pembayaran')

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
                                            <td>{{ $detail->produk->nama_produk }}</td>
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
                        <form action="{{ route('transaksi.complete', $transaksi->id) }}" method="POST" id="paymentForm">
                            @csrf
                            <div class="mb-3">
                                <label for="total_harga" class="form-label">Total Harga:</label>
                                <input type="text" id="total_harga" class="form-control"
                                    value="Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="diskon" class="form-label">Diskon:</label>
                                <input type="text" name="diskon" id="diskon" class="form-control" value="Rp 0" />
                            </div>
                            <div class="mb-3">
                                <label for="total_setelah_diskon" class="form-label">Total Setelah Diskon:</label>
                                <input type="text" id="total_setelah_diskon" class="form-control" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="bayar" class="form-label">Jumlah Bayar:</label>
                                <input type="text" name="bayar" id="bayar" class="form-control" value="Rp 0"
                                    required />
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
@endsection

@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const totalHarga = parseFloat("{{ $transaksi->total_harga }}");
            const diskonInput = document.getElementById("diskon");
            const totalSetelahDiskonDisplay = document.getElementById("total_setelah_diskon");
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
                payButton.disabled = bayar <= 0;
            }

            function updatePembayaran() {
                const diskon = convertToAngka(diskonInput.value) || 0;
                const totalSetelahDiskon = totalHarga - diskon;
                const bayar = convertToAngka(bayarInput.value) || 0;
                const kembalian = bayar - totalSetelahDiskon;

                totalSetelahDiskonDisplay.value = formatRupiah(totalSetelahDiskon);
                kembalianDisplay.value = kembalian >= 0 ? formatRupiah(kembalian) : formatRupiah(0);

                validatePayment();
            }

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

            updatePembayaran();
        });
    </script>
@endsection
