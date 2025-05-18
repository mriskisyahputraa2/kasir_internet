<div>
    <div class="row">
        <!-- Tambahkan dropdown filter kategori -->
        <!-- Tambahkan input pencarian -->
        <div class="col-md-8">
            <div class="row mb-3">
                <div class="col-md-4">
                    <select id="filterKategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}">
                                {{ $kategori->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" id="cariProduk" class="form-control" placeholder="Cari nama produk..." />
                </div>
            </div>

            <!-- Daftar Produk -->
            <div class="row" id="daftar-produk" style="min-height: 50vh; max-height: 65vh; overflow-y: scroll">
                @foreach ($produks as $produk)
                    <div class="col-md-3 mb-3 produk-item" data-kategori="{{ $produk->kategori }}"
                        data-nama="{{ strtolower($produk->nama) }}" style="cursor: pointer">
                        <div class="card produk-card" data-id="{{ $produk->id }}" data-nama="{{ $produk->nama }}"
                            data-harga="{{ $produk->harga_jual }}" data-diskon="{{ $produk->diskon_global ?? 0 }}">
                            <div class="">
                                <img src="{{ asset('storage/' . $produk->foto) }}" class="card-img-top"
                                    alt="{{ $produk->nama }}"
                                    style="
                                    cursor: pointer;
                                    width: 100%;
                                    height: 150px;
                                    object-fit: cover;
                                    cursor: pointer;
                                " />
                            </div>
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
        <div class="col-md-4" style="padding-left: 30px">
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
                    <input type="text" id="total_harga" class="form-control" readonly />
                    <input type="hidden" name="total_harga" id="total_harga_input" />
                </div>
                <div class="mt-3">
                    <button type="submit" name="status_pembayaran" value="Belum Lunas" class="btn btn-success">
                        Proses
                    </button>
                    <button type="submit" name="status_pembayaran" value="Bayar Nanti" class="btn btn-secondary">
                        Bayar Nanti
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
