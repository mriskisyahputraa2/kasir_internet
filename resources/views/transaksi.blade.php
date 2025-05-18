@extends('layouts.app')

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

    <div class="container">
        <h2>Transaksi Baru</h2>
        <div class="row">
            <!-- Pilih Produk -->
            <div class="col-md-7">
                <h4>Pilih Produk</h4>
                <div class="row">
                    @foreach ($produks as $produk)
                        <div class="col-md-4">
                            <div class="card produk-card text-center p-2" data-id="{{ $produk->id }}"
                                data-nama="{{ $produk->nama }}" data-harga="{{ $produk->harga_jual }}"
                                data-diskon="{{ $produk->diskon_global }}">
                                <img src="{{ asset('storage/' . $produk->foto) }}" class="card-img-top"
                                    alt="{{ $produk->nama }}" style="cursor: pointer" />
                                <div class="card-body">
                                    <h5 class="card-title">{{ $produk->nama }}</h5>
                                    <p class="card-text">
                                        Harga: Rp{{ number_format($produk->harga_jual, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Bagian Pembayaran -->
            <div class="col-md-5">
                <h4>Daftar Pembayaran</h4>

                <form action="{{ route('transaksi.store') }}" method="POST" id="form-transaksi">
                    @csrf
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="daftar-pembayaran"></tbody>
                    </table>
                    <div class="form-group">
                        <label for="tujuan_dana">Pilih Tujuan Dana</label>
                        <select name="tujuan_dana" class="form-control" required>
                            @foreach ($tambahSaldos as $saldo)
                                <option value="{{ $saldo->id }}">
                                    {{ $saldo->nama_platform }} - Rp{{ number_format($saldo->saldo, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="diskon_transaksi">Diskon Keseluruhan:</label>
                        <input type="number" name="diskon_transaksi" id="diskon_transaksi" class="form-control"
                            value="0" />
                    </div>

                    <div class="form-group">
                        <label for="total_harga">Total Harga:</label>
                        <input type="text" id="total_harga" class="form-control" readonly />
                        <input type="hidden" name="total_harga" id="total_harga_input" />
                    </div>

                    <div class="form-group">
                        <label for="bayar">Jumlah Bayar:</label>
                        <input type="number" name="bayar" id="bayar" class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label for="status_pembayaran">Status Pembayaran:</label>
                        <select name="status_pembayaran" class="form-control" required id="status_pembayaran">
                            <option value="Lunas">Lunas</option>
                            <option value="Belum Lunas">Belum Lunas</option>
                            <option value="Bayar Nanti">Bayar Nanti</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kembalian">Kembalian:</label>
                        <input type="text" id="kembalian" class="form-control" readonly />
                    </div>

                    <button type="submit" class="btn btn-success" id="submit-transaksi">
                        Proses Transaksi
                    </button>
                    <button type="button" class="btn btn-primary" id="skip-pelanggan">
                        Skip ke Pelanggan Selanjutnya
                    </button>
                </form>
            </div>
        </div>

        <!-- Daftar Transaksi Belum Lunas -->
        <div class="row mt-5">
            <div class="col-md-12">
                <h4>Daftar Transaksi Belum Lunas</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaksis as $transaksi)
                            <tr>
                                <td>{{ $transaksi->id }}</td>
                                <td>Rp{{ number_format($transaksi->total_harga, 2) }}</td>
                                <td>{{ $transaksi->status_pembayaran }}</td>
                                <td>
                                    <button class="btn btn-primary" onclick="lanjutkanPembayaran({{ $transaksi->id }})">
                                        Lanjutkan Pembayaran
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Lanjutkan Pembayaran -->
    <div class="modal fade" id="modalLanjutkanPembayaran" tabindex="-1" aria-labelledby="modalLanjutkanPembayaranLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLanjutkanPembayaranLabel">Lanjutkan Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formLanjutkanPembayaran" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="total_harga_modal">Total Harga:</label>
                            <input type="text" id="total_harga_modal" class="form-control" readonly />
                        </div>

                        <div class="form-group">
                            <label for="bayar_modal">Jumlah Bayar:</label>
                            <input type="number" name="bayar" id="bayar_modal" class="form-control" required />
                        </div>

                        <div class="form-group">
                            <label for="kembalian_modal">Kembalian:</label>
                            <input type="text" id="kembalian_modal" class="form-control" readonly />
                        </div>

                        <button type="submit" class="btn btn-success">
                            Selesaikan Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const daftarPembayaran = document.getElementById("daftar-pembayaran");
            const totalHargaInput = document.getElementById("total_harga_input");
            const totalHargaDisplay = document.getElementById("total_harga");
            const bayarInput = document.getElementById("bayar");
            const kembalianDisplay = document.getElementById("kembalian");
            const diskonTransaksiInput = document.getElementById("diskon_transaksi");
            const statusPembayaranSelect = document.getElementById("status_pembayaran");
            const skipPelangganButton = document.getElementById("skip-pelanggan");

            function updateTotal() {
                let total = 0;
                document.querySelectorAll(".subtotal").forEach(subtotal => {
                    total += parseFloat(subtotal.textContent);
                });
                let diskonTransaksi = parseFloat(diskonTransaksiInput.value) || 0;
                total -= diskonTransaksi;
                totalHargaDisplay.value = "Rp " + total.toFixed(2);
                totalHargaInput.value = total.toFixed(2);
                updateKembalian();
            }

            function updateKembalian() {
                let totalHarga = parseFloat(totalHargaInput.value) || 0;
                let bayar = parseFloat(bayarInput.value) || 0;
                let kembalian = bayar - totalHarga;
                kembalianDisplay.value = kembalian >= 0 ? "Rp " + kembalian.toFixed(2) : "Rp 0.00";
            }

            function togglePaymentFields() {
                if (statusPembayaranSelect.value === "Bayar Nanti") {
                    bayarInput.disabled = true;
                    kembalianDisplay.disabled = true;
                    bayarInput.value = 0;
                    kembalianDisplay.value = "Rp 0.00";
                } else {
                    bayarInput.disabled = false;
                    kembalianDisplay.disabled = false;
                }
            }

            statusPembayaranSelect.addEventListener("change", togglePaymentFields);

            // Skip ke pelanggan selanjutnya
            skipPelangganButton.addEventListener("click", function() {
                // Reset form dan daftar pembayaran
                daftarPembayaran.innerHTML = "";
                totalHargaDisplay.value = "Rp 0.00";
                totalHargaInput.value = 0;
                bayarInput.value = 0;
                kembalianDisplay.value = "Rp 0.00";
                diskonTransaksiInput.value = 0;
                alert("Transaksi dilanjutkan ke pelanggan selanjutnya.");
            });

            // Tambahkan produk ke daftar pembayaran
            document.querySelectorAll(".produk-card").forEach(card => {
                card.addEventListener("click", function() {
                    let id = this.getAttribute("data-id");
                    let nama = this.getAttribute("data-nama");
                    let harga = parseFloat(this.getAttribute("data-harga"));
                    let diskon = parseFloat(this.getAttribute("data-diskon"));
                    let jumlah = 1;
                    let subtotal = harga * jumlah - diskon;

                    if (!document.querySelector(`#row-${id}`)) {
                        let row = document.createElement("tr");
                        row.setAttribute("id", `row-${id}`);
                        row.innerHTML = `
                    <td>${nama}</td>
                    <td>
                        <button class="btn btn-sm btn-danger kurang">-</button>
                        <span class="jumlah">${jumlah}</span>
                        <button class="btn btn-sm btn-success tambah">+</button>
                        <input type="hidden" name="produk_id[]" value="${id}">
                        <input type="hidden" name="harga_satuan[]" value="${harga}">
                        <input type="hidden" name="diskon[]" value="${diskon}">
                        <input type="hidden" name="jumlah[]" value="${jumlah}" class="jumlah-input">
                    </td>
                    <td>Rp <span class="subtotal">${subtotal.toFixed(2)}</span></td>
                    <td><button class="btn btn-danger btn-hapus">X</button></td>
                `;
                        daftarPembayaran.appendChild(row);

                        // Tambahkan event listener untuk tombol tambah dan kurang
                        const tambahButton = row.querySelector(".tambah");
                        const kurangButton = row.querySelector(".kurang");
                        const jumlahSpan = row.querySelector(".jumlah");
                        const subtotalSpan = row.querySelector(".subtotal");
                        const jumlahInput = row.querySelector(".jumlah-input");

                        tambahButton.addEventListener("click", function() {
                            let jumlah = parseInt(jumlahSpan.textContent) + 1;
                            jumlahSpan.textContent = jumlah;
                            jumlahInput.value = jumlah;
                            let subtotal = harga * jumlah - diskon;
                            subtotalSpan.textContent = subtotal.toFixed(2);
                            updateTotal();
                        });

                        kurangButton.addEventListener("click", function() {
                            let jumlah = parseInt(jumlahSpan.textContent) - 1;
                            if (jumlah >= 1) {
                                jumlahSpan.textContent = jumlah;
                                jumlahInput.value = jumlah;
                                let subtotal = harga * jumlah - diskon;
                                subtotalSpan.textContent = subtotal.toFixed(2);
                                updateTotal();
                            }
                        });

                        // Tambahkan event listener untuk tombol hapus
                        const hapusButton = row.querySelector(".btn-hapus");
                        hapusButton.addEventListener("click", function() {
                            row.remove();
                            updateTotal();
                        });
                    }
                    updateTotal();
                });
            });

            diskonTransaksiInput.addEventListener("input", updateTotal);
            bayarInput.addEventListener("input", updateKembalian);
            togglePaymentFields(); // Panggil fungsi ini saat halaman dimuat untuk mengatur status awal
        });

        // Fungsi untuk melanjutkan pembayaran
        function lanjutkanPembayaran(id) {
            fetch(`/transaksi/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('total_harga_modal').value = "Rp " + data.total_harga.toFixed(2);
                    document.getElementById('formLanjutkanPembayaran').action = `/transaksi/complete/${id}`;

                    const bayarModal = document.getElementById('bayar_modal');
                    const kembalianModal = document.getElementById('kembalian_modal');

                    bayarModal.addEventListener('input', function() {
                        let bayar = parseFloat(bayarModal.value) || 0;
                        let kembalian = bayar - data.total_harga;
                        kembalianModal.value = kembalian >= 0 ? "Rp " + kembalian.toFixed(2) : "Rp 0.00";
                    });

                    // Tampilkan modal
                    new bootstrap.Modal(document.getElementById('modalLanjutkanPembayaran')).show();
                });
        }
    </script>
@endsection
