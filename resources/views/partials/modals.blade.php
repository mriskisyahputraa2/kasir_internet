<style>
    body {
        font-family: 'Poppins', sans-serif;
    }
</style>

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
                        <input list="tipe_transaksi_list" name="tipe_transaksi" id="tipe_transaksi" class="form-control"
                            placeholder="Pilih atau ketik tipe transaksi" />
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
                                    <a href="{{ route('transaksi.lanjutkan', $transaksi->id) }}" class="btn text-white"
                                        style="background-color: #633B48">
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

<!-- sesi -->
<!-- Cashier Modal -->
<div class="modal fade" id="kasirModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    @if ($sesiAktif)
                        Tutup Kasir
                    @else
                        Buka Kasir
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ $sesiAktif ? route('tutup.kasir') : route('buka.kasir') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if ($sesiAktif)
                        <div class="alert alert-info">
                            <h5>Informasi Sesi Kasir</h5>
                            <p>Shift: {{ $sesiAktif->shift->nama_shift }}</p>
                            <p>Dibuka oleh: {{ $sesiAktif->user->nama }}</p>
                            <p>Saldo Awal: Rp {{ number_format($sesiAktif->saldo_awal, 0, ',', '.') }}</p>
                            <p>Dana Laci: Rp {{ number_format($sesiAktif->dana_laci, 0, ',', '.') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Saldo Akhir</label>
                            <input type="text" class="form-control"
                                value="Rp {{ number_format($totalSaldoTambahSaldo, 0, ',', '.') }}" readonly>
                        </div>
                    @else
                        <div class="mb-3">
                            <label class="form-label">Shift</label>
                            <select name="shift_id" class="form-select" required>
                                <option value="">Pilih Shift</option>
                                @foreach ($shifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->nama_shift }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dana Laci</label>
                            <input type="number" name="dana_laci" class="form-control" required>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white" style="background-color: #633B48">

                        @if ($sesiAktif)
                            Tutup Kasir
                        @else
                            Buka Kasir
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
