<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Setup Filter Tanggal (Default: Bulan Ini)
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');
        $type = $request->type ?? 'all';

        // 2. Query Dasar (Base Query)
        // Kita gunakan query builder ini sebagai dasar untuk perhitungan lainnya
        $query = Transaction::with(['product', 'account'])
            ->where('user_id', Auth::id())
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        // 3. Filter Tipe (Jika user memilih dropdown)
        if ($type != 'all') {
            $query->where('type', $type);
        }

        // 4. Ambil Data untuk Tabel (Clone agar query dasar tidak berubah)
        $transactions = (clone $query)->orderBy('transaction_date', 'desc')->get();

        // 5. --- PERHITUNGAN SUMMARY (KARTU ATAS) ---
        
        $totalTransaksi = $transactions->count();
        
        // TOTAL FEE: Menjumlahkan kolom 'fee' dari transaksi yang difilter
        $totalFee = (clone $query)->sum('fee');

        // Total Pembelian: Sum total_value khusus tipe 'beli'
        $totalPembelian = (clone $query)->where('type', 'beli')->sum('total_value'); 
        
        // Total Penjualan: Sum total_value khusus tipe 'jual'
        $totalPenjualan = (clone $query)->where('type', 'jual')->sum('total_value');

        // Realized Profit (Estimasi Cashflow: Uang Masuk Jual - Uang Keluar Beli)
        $totalRealizedProfit = $totalPenjualan - $totalPembelian;

        // 6. --- PERHITUNGAN GRAFIK (CHART) ---
        // Data Grafik Line Chart (12 Bulan di tahun ini)
        $buyChart = [];
        $sellChart = [];
        
        // Loop bulan 1 s/d 12
        for ($i = 1; $i <= 12; $i++) {
            $buyChart[] = Transaction::where('user_id', Auth::id())
                ->whereYear('transaction_date', Carbon::now()->year)
                ->whereMonth('transaction_date', $i)
                ->where('type', 'beli')
                ->sum('total_value');

            $sellChart[] = Transaction::where('user_id', Auth::id())
                ->whereYear('transaction_date', Carbon::now()->year)
                ->whereMonth('transaction_date', $i)
                ->where('type', 'jual')
                ->sum('total_value');
        }

        // Data Donut Chart (Alokasi Aset berdasarkan Kategori Produk)
        // Pastikan model Product punya kolom 'category' atau ganti logic grouping sesuai kebutuhan
        $allocation = Transaction::where('user_id', Auth::id())
            ->where('type', 'beli') // Kita lihat alokasi dari sisi pembelian aset
            ->with('product')
            ->get()
            ->groupBy(function($item) {
                // Group by Product Name atau Category jika ada
                return $item->product->code ?? 'Other'; 
            })
            ->map(function ($row) {
                return $row->sum('total_value');
            });

        return view('reports.index', compact(
            'transactions', 
            'totalTransaksi', 
            'totalPembelian', 
            'totalPenjualan', 
            'totalFee',           // Variabel Penting: Fee
            'totalRealizedProfit',
            'startDate', 
            'endDate', 
            'type',
            'buyChart',
            'sellChart',
            'allocation'
        ));
    }

    // Fungsi Import (Jika diperlukan)
    public function import(Request $request) 
    {
        // Logika import CSV Anda di sini (jika ada)
        return back()->with('success', 'Fitur Import belum diaktifkan di controller ini.');
    }
    
    // Fungsi Export (Jika diperlukan)
    public function export()
    {
        return back()->with('success', 'Fitur Export belum diaktifkan.');
    }
}