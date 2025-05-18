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

    <div class="container">
        <h2>Pilih Barang</h2>
        <button type="button" class="btn btn-info mb-3" data-bs-toggle="modal" data-bs-target="#modalBayarNanti">
            Lihat Transaksi Bayar Nanti
        </button>
        <div class="row">
            <!-- Daftar Produk -->
            <div class="col-md-8">
                <div class="row">
                    @foreach ($produks as $produk)
                        <div class="col-md-4 mb-3">
                            <div class="card produk-card" data-id="{{ $produk->id }}" data-nama="{{ $produk->nama }}"
                                data-harga="{{ $produk->harga_jual }}" data-diskon="{{ $produk->diskon_global ?? 0 }}">
                                <img src="{{ asset('storage/' . $produk->foto) }}" class="card-img-top"
                                    alt="{{ $produk->nama }}" style="cursor: pointer">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $produk->nama }}</h5>
                                    <p class="card-text">
                                        Harga: {{ formatRupiah($produk->harga_jual) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Daftar Pembelian -->
            <div class="col-md-4">
                <h4>Daftar Pembelian</h4>
                <form action="{{ route('transaksi.store') }}" method="POST" id="form-transaksi">
                    @csrf
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="daftar-pembelian"></tbody>
                    </table>
                    <div class="form-group">
                        <label for="total_harga">Total Harga:</label>
                        <input type="text" id="total_harga" class="form-control" readonly>
                        <input type="hidden" name="total_harga" id="total_harga_input">
                    </div>
                    <div class="mt-3">
                        <button type="submit" name="status_pembayaran" value="Belum Lunas"
                            class="btn btn-success">Proses</button>
                        <button type="submit" name="status_pembayaran" value="Bayar Nanti" class="btn btn-secondary">Bayar
                            Nanti</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal untuk Daftar Transaksi Bayar Nanti -->
    <div class="modal fade" id="modalBayarNanti" tabindex="-1" aria-labelledby="modalBayarNantiLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBayarNantiLabel">Daftar Transaksi Bayar Nanti</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Total Harga</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaksis as $transaksi)
                                <tr>
                                    <td>{{ $transaksi->id }}</td>
                                    <td>{{ formatRupiah($transaksi->total_harga) }}</td>
                                    <td>{{ $transaksi->created_at->format('d-m-Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('transaksi.lanjutkan', $transaksi->id) }}"
                                            class="btn btn-primary">
                                            Lanjutkan Pembayaran
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const daftarPembelian = document.getElementById("daftar-pembelian");
            const totalHargaInput = document.getElementById("total_harga_input");
            const totalHargaDisplay = document.getElementById("total_harga");

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

            function updateTotal() {
                let total = 0;
                document.querySelectorAll(".subtotal").forEach(subtotal => {
                    total += parseFloat(subtotal.textContent.replace(/[^0-9]/g, ''));
                });
                totalHargaDisplay.value = formatRupiah(total);
                totalHargaInput.value = total;
            }

            document.querySelectorAll(".produk-card").forEach(card => {
                card.addEventListener("click", function() {
                    const id = this.getAttribute("data-id");
                    const nama = this.getAttribute("data-nama");
                    const harga = parseFloat(this.getAttribute("data-harga"));
                    const diskon = parseFloat(this.getAttribute("data-diskon"));
                    const jumlah = 1;

                    const existingRow = document.querySelector(`#row-${id}`);
                    if (existingRow) {
                        const jumlahSpan = existingRow.querySelector(".jumlah");
                        const jumlahInput = existingRow.querySelector(".jumlah-input");
                        const subtotalSpan = existingRow.querySelector(".subtotal");

                        let jumlahBaru = parseInt(jumlahSpan.textContent) + 1;
                        jumlahSpan.textContent = jumlahBaru;
                        jumlahInput.value = jumlahBaru;
                        subtotalSpan.textContent = formatRupiah((harga - diskon) * jumlahBaru);
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
                    <td><span class="subtotal">${formatRupiah(subtotal)}</span></td>
                    <td><button type="button" class="btn btn-danger btn-hapus">X</button></td>
                `;
                        daftarPembelian.appendChild(row);

                        const tambahButton = row.querySelector(".tambah");
                        const kurangButton = row.querySelector(".kurang");
                        const jumlahSpan = row.querySelector(".jumlah");
                        const subtotalSpan = row.querySelector(".subtotal");
                        const jumlahInput = row.querySelector(".jumlah-input");

                        tambahButton.addEventListener("click", function() {
                            let jumlah = parseInt(jumlahSpan.textContent) + 1;
                            jumlahSpan.textContent = jumlah;
                            jumlahInput.value = jumlah;
                            subtotalSpan.textContent = formatRupiah((harga - diskon) *
                                jumlah);
                            updateTotal();
                        });

                        kurangButton.addEventListener("click", function() {
                            let jumlah = parseInt(jumlahSpan.textContent) - 1;
                            if (jumlah >= 1) {
                                jumlahSpan.textContent = jumlah;
                                jumlahInput.value = jumlah;
                                subtotalSpan.textContent = formatRupiah((harga - diskon) *
                                    jumlah);
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
@endsection
