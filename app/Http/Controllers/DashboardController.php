<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Toko;
use App\Models\TambahSaldo;
use App\Models\TransaksiDetail;
use App\Models\DataTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = session('user');

        if (!$user) {
            return redirect()->route('login');
        }

        $toko = null;
        $idToko = session('id_toko');

        if ($idToko) {
            $toko = Toko::find($idToko);
        }

        $periode = $request->input('periode', 'harian');
        $filterBulan = $request->input('bulan', date('m'));
        $filterTahun = $request->input('tahun', date('Y'));
        $filterTanggal = $request->input('tanggal', date('Y-m-d'));

        $tambahSaldos = TambahSaldo::with('toko', 'user')->where('id_toko', $idToko);
        $omsetBarangQuery = TransaksiDetail::whereHas('transaksi', function ($query) use ($idToko) {
            $query->where('id_toko', $idToko);
        });

        $omsetTransaksiQuery = DataTransaksi::where('id_toko', $idToko);

        $profitBarangQuery = TransaksiDetail::join('produks', 'transaksi_details.produk_id', '=', 'produks.id')
            ->whereHas('transaksi', function ($query) use ($idToko) {
                $query->where('id_toko', $idToko);
            });

        $profitTransaksiQuery = DataTransaksi::where('id_toko', $idToko);

        $totalTransferQuery = DB::table('data_transaksi')->where('id_toko', $idToko)->where('jenis_transaksi', 'transfer');
        $totalTarikTunaiQuery = DB::table('data_transaksi')->where('id_toko', $idToko)->where('jenis_transaksi', 'tarik_tunai');

        $dataTransaksi = DataTransaksi::where('id_toko', $idToko);
        $transaksi = Transaksi::where('id_toko', $idToko);

        switch ($periode) {
            case 'harian':
                $tambahSaldos->whereDate('created_at', $filterTanggal);
                $omsetBarangQuery->whereDate('transaksi_details.created_at', $filterTanggal);
                $omsetTransaksiQuery->whereDate('created_at', $filterTanggal);
                $profitBarangQuery->whereDate('transaksi_details.created_at', $filterTanggal);
                $profitTransaksiQuery->whereDate('created_at', $filterTanggal);
                $totalTransferQuery->whereDate('created_at', $filterTanggal);
                $totalTarikTunaiQuery->whereDate('created_at', $filterTanggal);
                $dataTransaksi->whereDate('created_at', $filterTanggal);
                $transaksi->whereDate('created_at', $filterTanggal);
                break;

            case 'mingguan':
                $startOfWeek = Carbon::now()->startOfWeek();
                $endOfWeek = Carbon::now()->endOfWeek();
                $tambahSaldos->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                $omsetBarangQuery->whereBetween('transaksi_details.created_at', [$startOfWeek, $endOfWeek]);
                $omsetTransaksiQuery->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                $profitBarangQuery->whereBetween('transaksi_details.created_at', [$startOfWeek, $endOfWeek]);
                $profitTransaksiQuery->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                $totalTransferQuery->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                $totalTarikTunaiQuery->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                $dataTransaksi->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                $transaksi->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                break;

            case 'bulanan':
                $tambahSaldos->whereMonth('created_at', $filterBulan)->whereYear('created_at', $filterTahun);
                $omsetBarangQuery->whereMonth('transaksi_details.created_at', $filterBulan)->whereYear('transaksi_details.created_at', $filterTahun);
                $omsetTransaksiQuery->whereMonth('created_at', $filterBulan)->whereYear('created_at', $filterTahun);
                $profitBarangQuery->whereMonth('transaksi_details.created_at', $filterBulan)->whereYear('transaksi_details.created_at', $filterTahun);
                $profitTransaksiQuery->whereMonth('created_at', $filterBulan)->whereYear('created_at', $filterTahun);
                $totalTransferQuery->whereMonth('created_at', $filterBulan)->whereYear('created_at', $filterTahun);
                $totalTarikTunaiQuery->whereMonth('created_at', $filterBulan)->whereYear('created_at', $filterTahun);
                $dataTransaksi->whereMonth('created_at', $filterBulan)->whereYear('created_at', $filterTahun);
                $transaksi->whereMonth('created_at', $filterBulan)->whereYear('created_at', $filterTahun);
                break;

            case 'tahunan':
                $tambahSaldos->whereYear('created_at', $filterTahun);
                $omsetBarangQuery->whereYear('transaksi_details.created_at', $filterTahun);
                $omsetTransaksiQuery->whereYear('created_at', $filterTahun);
                $profitBarangQuery->whereYear('transaksi_details.created_at', $filterTahun);
                $profitTransaksiQuery->whereYear('created_at', $filterTahun);
                $totalTransferQuery->whereYear('created_at', $filterTahun);
                $totalTarikTunaiQuery->whereYear('created_at', $filterTahun);
                $dataTransaksi->whereYear('created_at', $filterTahun);
                $transaksi->whereYear('created_at', $filterTahun);
                break;
        }

        $tambahSaldos = $tambahSaldos->get();
        $totalSaldo = $tambahSaldos->sum('saldo');
        $omsetBarang = $omsetBarangQuery->sum('subtotal');
        $omsetTransaksi = $omsetTransaksiQuery->sum('nominal_transaksi');
        $totalOmset = $omsetBarang + $omsetTransaksi;

        $profitBarang = $profitBarangQuery->sum(DB::raw('(transaksi_details.subtotal - (produks.harga_beli * transaksi_details.jumlah))'));
        $profitTransaksi = $profitTransaksiQuery->sum(DB::raw('(admin_dalam + admin_luar)'));
        $totalProfit = $profitBarang + $profitTransaksi;

        $totalTransfer = $totalTransferQuery->sum('nominal_transaksi');
        $totalTarikTunai = $totalTarikTunaiQuery->sum('nominal_transaksi');

        $dataTransaksi = $dataTransaksi->get();
        $transaksi = $transaksi->with('details')->get();

        $chartData = [];

        switch ($periode) {
            case 'harian':
                $chartData = Transaksi::where('id_toko', $idToko)
                    ->whereDate('created_at', $filterTanggal)
                    ->selectRaw('HOUR(created_at) as hour, SUM(total_harga) as total')
                    ->groupBy('hour')
                    ->orderBy('hour')
                    ->pluck('total', 'hour');
                break;

            case 'mingguan':
                $chartData = Transaksi::where('id_toko', $idToko)
                    ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                    ->selectRaw('WEEKDAY(created_at)+1 as day, SUM(total_harga) as total')
                    ->groupBy('day')
                    ->orderBy('day')
                    ->pluck('total', 'day');
                break;

            case 'bulanan':
                $chartData = Transaksi::where('id_toko', $idToko)
                    ->whereMonth('created_at', $filterBulan)
                    ->whereYear('created_at', $filterTahun)
                    ->selectRaw('DAY(created_at) as day, SUM(total_harga) as total')
                    ->groupBy('day')
                    ->orderBy('day')
                    ->pluck('total', 'day');
                break;

            case 'tahunan':
                $chartData = Transaksi::where('id_toko', $idToko)
                    ->whereYear('created_at', $filterTahun)
                    ->selectRaw('MONTH(created_at) as month, SUM(total_harga) as total')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total', 'month');
                break;
        }

        return view('dashboard.index', compact(
            'user',
            'toko',
            'tambahSaldos',
            'totalSaldo',
            'omsetBarang',
            'omsetTransaksi',
            'totalOmset',
            'profitBarang',
            'profitTransaksi',
            'totalProfit',
            'totalTransfer',
            'totalTarikTunai',
            'dataTransaksi',
            'transaksi',
            'periode',
            'filterTanggal',
            'filterBulan',
            'filterTahun',
            'chartData'
        ));
    }
}
