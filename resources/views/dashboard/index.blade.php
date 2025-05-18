@extends('layouts.app')

@section('title', 'Kasir Internet')

<style>
    body {
        background-color: rgba(99, 59, 72, 0.15);
        font-family: 'Poppins', sans-serif;
    }
</style>

@section('content')
    <div class="d-flex justify-content-between align-items-center">
        <div class="pagetitle">
            <h1 style="color: #633B48; font-weight: 600;">Dashboard</h1>
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
        @if ($user['role'] === 'superadmin')
            <button type="button" class="btn mb-3 text-white" style="background-color: #633B48" data-bs-toggle="modal"
                data-bs-target="#filterModal">
                <i class="bi bi-filter"></i> Filter Data
            </button>
        @endif
    </div>
    <!-- End Page Title -->

    <!-- Form Filter -->

    <!-- Modal Filter -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true"
        style="color: black">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('dashboard') }}" method="GET">
                        <div class="mb-3">
                            <label for="periode" class="form-label">Pilih Periode</label>
                            <select name="periode" id="periode" class="form-select" onchange="toggleFilterInputs()">
                                <option value="harian" {{ $periode == 'harian' ? 'selected' : '' }}>Harian</option>
                                <option value="mingguan" {{ $periode == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                                <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                <option value="tahunan" {{ $periode == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                            </select>
                        </div>

                        <div class="mb-3" id="tanggalInput">
                            <label for="tanggal" class="form-label">Pilih Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ $filterTanggal }}">
                        </div>

                        <div class="mb-3" id="bulanInput">
                            <label for="bulan" class="form-label">Pilih Bulan</label>
                            <select name="bulan" class="form-select">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $filterBulan == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="mb-3" id="tahunInput">
                            <label for="tahun" class="form-label">Pilih Tahun</label>
                            <select name="tahun" class="form-select">
                                @for ($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}" {{ $filterTahun == $i ? 'selected' : '' }}>
                                        {{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn text-white" style="background-color: #633B48">Terapkan
                                Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <section class="section dashboard">
        <div class="">
            <!-- Left side columns -->
            <div class="">
                <div class="row">
                    <!-- Sales Card -->


                    {{-- Omset --}}
                    <div class="col-md-6">
                        <div class="card info-card revenue-card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    Omset <span>| {{ ucfirst($periode) }}
                                        {{ $filterTanggal ?? date('F Y', mktime(0, 0, 0, $filterBulan, 1, $filterTahun)) }}</span>

                                </h5>


                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-cash-coin"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>
                                            Rp {{ number_format($totalOmset, 0, ',', '.') }}
                                        </h6>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end Omset --}}

                    {{-- Profit --}}
                    <div class="col-md-6">
                        <div class="card info-card customers-card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    Profit <span>| {{ ucfirst($periode) }}
                                        {{ $filterTanggal ?? date('F Y', mktime(0, 0, 0, $filterBulan, 1, $filterTahun)) }}</span>
                                </h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-graph-up"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>
                                            Rp {{ number_format($totalProfit, 0, ',', '.') }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end Profit --}}

                    <!-- End dana -->

                    <!-- Rekap Transaksi -->
                    <div class="col-12">
                        <div class="card recent-sales overflow-auto">
                            <div class="card-body">
                                <h5 class="card-title">
                                    Rekap Transaksi <span>| {{ ucfirst($periode) }}
                                        {{ $filterTanggal ?? date('F Y', mktime(0, 0, 0, $filterBulan, 1, $filterTahun)) }}</span>
                                </h5>

                                <table class="table table-borderless datatable">
                                    <table id="saldoAwalTable" class="table table-bordered display nowrap"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Tanggal</th>
                                                <th scope="col">Jenis Transaksi</th>
                                                <th scope="col">Nominal</th>
                                                <th scope="col">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dataTransaksi as $index => $dt)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $dt->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>{{ $dt->jenis_transaksi }}</td>
                                                    <td>Rp {{ number_format($dt->nominal_transaksi, 0, ',', '.') }}</td>
                                                    <td>{{ $dt->keterangan }}</td>
                                                </tr>
                                            @endforeach

                                            @foreach ($transaksi as $index => $tr)
                                                <tr>
                                                    <td>{{ $index + 1 + $dataTransaksi->count() }}</td>
                                                    <td>{{ $tr->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>Transaksi Barang</td>
                                                    <td>Rp {{ number_format($tr->total_harga, 0, ',', '.') }}</td>
                                                    <td>Transaksi ID: {{ $tr->id }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>
                    <!-- End Rekap Transaksi -->
                    <!-- Sales Report -->
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <!-- Title & Periode -->
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                                    <h5 class="card-title mb-2 mb-md-0" style="color: #633B48; font-weight: 700;">
                                        Sales Report
                                    </h5>
                                    <span class="text-muted small">
                                        / {{ ucfirst($periode) }}
                                        @if ($periode == 'harian')
                                            {{ \Carbon\Carbon::parse($filterTanggal)->format('d M Y') }}
                                        @elseif ($periode == 'mingguan')
                                            {{ \Carbon\Carbon::now()->startOfWeek()->format('d M') }} -
                                            {{ \Carbon\Carbon::now()->endOfWeek()->format('d M Y') }}
                                        @elseif ($periode == 'bulanan')
                                            {{ date('F Y', mktime(0, 0, 0, $filterBulan, 1, $filterTahun)) }}
                                        @else
                                            {{ $filterTahun }}
                                        @endif
                                    </span>
                                </div>

                                <!-- Chart -->
                                <div id="salesChart" class="position-relative"></div>

                                <script>
                                    document.addEventListener("DOMContentLoaded", () => {
                                        const chartData = @json($chartData);
                                        let labels = [],
                                            seriesData = [];

                                        if (typeof chartData === 'object') {
                                            for (const key in chartData) {
                                                labels.push(key);
                                                seriesData.push(chartData[key]);
                                            }
                                        }

                                        // Label Formatter
                                        const getLabel = (key) => {
                                            @if ($periode == 'harian')
                                                return `${key}:00`;
                                            @elseif ($periode == 'mingguan')
                                                return ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'][key - 1] || `Hari ${key}`;
                                            @elseif ($periode == 'bulanan')
                                                return key;
                                            @elseif ($periode == 'tahunan')
                                                return new Date(2024, key - 1, 1).toLocaleString('default', {
                                                    month: 'short'
                                                });
                                            @else
                                                return key;
                                            @endif
                                        };

                                        const formattedLabels = labels.map(getLabel);

                                        if (seriesData.length > 0) {
                                            new ApexCharts(document.querySelector("#salesChart"), {
                                                series: [{
                                                    name: 'Penjualan',
                                                    data: seriesData
                                                }],
                                                chart: {
                                                    height: 350,
                                                    type: 'area',
                                                    toolbar: {
                                                        show: true,
                                                        tools: {
                                                            download: true,
                                                            zoom: true,
                                                            pan: true,
                                                            reset: true
                                                        }
                                                    },
                                                    animations: {
                                                        enabled: true,
                                                        easing: 'easeinout',
                                                        speed: 800,
                                                    },
                                                    zoom: {
                                                        enabled: true
                                                    }
                                                },
                                                colors: ['#633B48'],
                                                fill: {
                                                    type: "gradient",
                                                    gradient: {
                                                        shade: "light",
                                                        type: "vertical",
                                                        shadeIntensity: 0.5,
                                                        gradientToColors: ["#9D6B7E"],
                                                        opacityFrom: 0.7,
                                                        opacityTo: 0.3,
                                                        stops: [0, 80, 100]
                                                    }
                                                },
                                                stroke: {
                                                    curve: 'smooth',
                                                    width: 3
                                                },
                                                markers: {
                                                    size: 6,
                                                    colors: ['#fff'],
                                                    strokeColors: '#633B48',
                                                    strokeWidth: 2,
                                                    hover: {
                                                        size: 8
                                                    }
                                                },
                                                dataLabels: {
                                                    enabled: false
                                                },
                                                xaxis: {
                                                    categories: formattedLabels,
                                                    title: {
                                                        text: '{{ $periode == 'harian' ? 'Jam' : ($periode == 'bulanan' ? 'Tanggal' : ($periode == 'tahunan' ? 'Bulan' : 'Hari')) }}',
                                                        style: {
                                                            fontSize: '12px',
                                                            fontWeight: 'bold',
                                                            color: '#633B48'
                                                        }
                                                    },
                                                    labels: {
                                                        rotate: -45,
                                                        style: {
                                                            colors: '#6c757d',
                                                            fontSize: '12px'
                                                        }
                                                    },
                                                    axisBorder: {
                                                        show: true,
                                                        color: '#f1f1f1'
                                                    },
                                                    axisTicks: {
                                                        show: true,
                                                        color: '#f1f1f1'
                                                    }
                                                },
                                                yaxis: {
                                                    title: {
                                                        text: 'Total Penjualan (Rp)',
                                                        style: {
                                                            fontSize: '12px',
                                                            fontWeight: 'bold',
                                                            color: '#633B48'
                                                        }
                                                    },
                                                    labels: {
                                                        style: {
                                                            colors: '#6c757d',
                                                            fontSize: '12px'
                                                        },
                                                        formatter: value => {
                                                            if (value >= 1_000_000) return "Rp " + (value / 1_000_000).toFixed(1) +
                                                                "Jt";
                                                            if (value >= 1_000) return "Rp " + (value / 1_000).toFixed(1) + "Rb";
                                                            return "Rp " + value;
                                                        }
                                                    },
                                                    axisBorder: {
                                                        show: true,
                                                        color: '#f1f1f1'
                                                    },
                                                    axisTicks: {
                                                        show: true,
                                                        color: '#f1f1f1'
                                                    }
                                                },
                                                tooltip: {
                                                    shared: true,
                                                    intersect: false,
                                                    y: {
                                                        formatter: val => "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                                                    }
                                                },
                                                legend: {
                                                    position: 'top',
                                                    horizontalAlign: 'right',
                                                    offsetY: -10,
                                                    markers: {
                                                        width: 10,
                                                        height: 10,
                                                        radius: 4
                                                    }
                                                },
                                                grid: {
                                                    borderColor: '#f1f1f1',
                                                    strokeDashArray: 5,
                                                    xaxis: {
                                                        lines: {
                                                            show: true
                                                        }
                                                    },
                                                    yaxis: {
                                                        lines: {
                                                            show: true
                                                        }
                                                    }
                                                },
                                                responsive: [{
                                                    breakpoint: 768,
                                                    options: {
                                                        chart: {
                                                            height: 300
                                                        },
                                                        xaxis: {
                                                            labels: {
                                                                rotate: -45
                                                            }
                                                        }
                                                    }
                                                }]
                                            }).render();
                                        } else {
                                            document.getElementById("salesChart").innerHTML = `
                            <div class="text-center py-5">
                                <i class="bi bi-bar-chart-fill text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">Belum ada data transaksi</p>
                            </div>`;
                                        }
                                    });
                                </script>
                                <!-- End Chart -->
                            </div>
                        </div>
                    </div>

                    <!-- End Sales Report -->


                    <!-- Reports -->
                    {{-- <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    Reports <span>/Today</span>
                                </h5>

                                <!-- Line Chart -->
                                <div id="reportsChart"></div>
                            </div>
                        </div>
                    </div> --}}
                    <!-- End Reports -->

                    <!-- Reports -->
                    {{-- <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    Reports <span>/Today</span>
                                </h5>

                                <!-- Line Chart -->
                                <div id="tes"></div>
                            </div>
                        </div>
                    </div> --}}
                    <!-- End Reports -->
                </div>
            </div>
        </div>
    </section>

    <script>
        function toggleFilterInputs() {
            const periode = document.getElementById('periode').value;
            const tanggalInput = document.getElementById('tanggalInput');
            const bulanInput = document.getElementById('bulanInput');
            const tahunInput = document.getElementById('tahunInput');

            if (periode === 'harian') {
                tanggalInput.style.display = 'block';
                bulanInput.style.display = 'none';
            } else if (periode === 'mingguan') {
                tanggalInput.style.display = 'none';
                bulanInput.style.display = 'none';
            } else if (periode === 'bulanan') {
                tanggalInput.style.display = 'none';
                bulanInput.style.display = 'block';
            } else if (periode === 'tahunan') {
                tanggalInput.style.display = 'none';
                bulanInput.style.display = 'none';
            }

            tahunInput.style.display = 'block'; // Tahun selalu ditampilkan
        }


        // Panggil fungsi saat modal dibuka
        document.getElementById('filterModal').addEventListener('shown.bs.modal', toggleFilterInputs);

        $(document).ready(function() {
            $('#saldoAwalTable').DataTable({
                responsive: true,
                language: {
                    "lengthMenu": "Tampilkan _MENU_ entri per halaman",
                    "zeroRecords": "Tidak ditemukan data yang cocok",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Tidak ada data tersedia",
                    "infoFiltered": "(disaring dari total _MAX_ entri)",
                    "search": "Cari:",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });
        });
    </script>
@endsection
