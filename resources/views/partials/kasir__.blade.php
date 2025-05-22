<div class="">


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
                                @foreach ($transaksisKasir as $key => $item)
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
</div>
