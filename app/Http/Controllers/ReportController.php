<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        
        // --- FILTER TANGGAL ---
        $startDate = $request->input('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate   = $request->input('end_date', Carbon::now()->endOfYear()->format('Y-m-d'));
        $type      = $request->input('type', 'all');

        // --- QUERY DASAR ---
        $query = Transaction::where('user_id', $userId)
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($type != 'all') {
            $query->where('type', $type);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();

        // --- 1. RINGKASAN CARD ---
        $totalTransaksi = $transactions->count();
        $totalPembelian = $transactions->where('type', 'beli')->sum('total_value');
        $totalPenjualan = $transactions->where('type', 'jual')->sum('total_value');
        $totalFee       = $transactions->where('type', 'jual')->sum('fee') + $transactions->where('type', 'beli')->sum('fee');

        // --- 2. HITUNG REALIZED PROFIT (Global) ---
        // Kita hitung dari semua produk yang pernah dijual
        $allProducts = Product::whereHas('transactions', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->get();

        $totalRealizedProfit = 0;

        foreach ($allProducts as $product) {
            $txs = $product->transactions->where('user_id', $userId);
            
            $buyUnits  = $txs->whereIn('type', ['beli', 'dividen_unit', 'stock_split', 'right_issue'])->sum('amount');
            $buyMoney  = $txs->whereIn('type', ['beli', 'right_issue'])->sum('total_value');
            $sellUnits = $txs->whereIn('type', ['jual', 'reverse_split'])->sum('amount');
            $sellMoney = $txs->where('type', 'jual')->sum('total_value');

            // Hitung Avg Price
            $avgPrice = $buyUnits > 0 ? ($buyMoney / $buyUnits) : 0;

            // Realized PnL = Total Jual - (Unit yg Dijual * Avg Price)
            $pnl = $sellMoney - ($sellUnits * $avgPrice);
            $totalRealizedProfit += $pnl;
        }

        // --- 3. DATA CHART BULANAN (Line Chart) ---
        $buyChart = [];
        $sellChart = [];
        
        for ($m = 1; $m <= 12; $m++) {
            $monthData = $transactions->filter(function($t) use ($m) {
                return Carbon::parse($t->transaction_date)->month == $m;
            });
            $buyChart[] = $monthData->where('type', 'beli')->sum('total_value');
            $sellChart[] = $monthData->where('type', 'jual')->sum('total_value');
        }

        // --- 4. DATA CHART PER JENIS (Doughnut Chart) ---
        // Kelompokkan transaksi berdasarkan kategori produk
        $allocation = $transactions->where('type', 'beli')->groupBy(function($item) {
            return $item->product->category ?? 'Lainnya';
        })->map(function($row) {
            return $row->sum('total_value');
        });

        $chartYear = Carbon::parse($startDate)->year;

        return view('reports.index', compact(
            'transactions', 'startDate', 'endDate', 'type',
            'totalTransaksi', 'totalPembelian', 'totalPenjualan', 'totalFee', 'totalRealizedProfit',
            'buyChart', 'sellChart', 'chartYear', 'allocation'
        ));
    }

    /**
     * 📥 EXPORT DATA KE CSV
     */
    public function export()
    {
        $fileName = 'transaksi_investasi_' . date('Y-m-d_H-i') . '.csv';
        $transactions = Transaction::with('product', 'account')->where('user_id', Auth::id())->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Tanggal', 'Tipe', 'Kode Produk', 'Nama Produk', 'Kategori', 'Akun', 'Jumlah Unit', 'Harga/Unit', 'Fee', 'Total Nilai', 'Catatan'];

        $callback = function() use($transactions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($transactions as $t) {
                $row['Tanggal']     = $t->transaction_date;
                $row['Tipe']        = $t->type;
                $row['Kode Produk'] = $t->product->code ?? '-';
                $row['Nama Produk'] = $t->product->name ?? '-';
                $row['Kategori']    = $t->product->category ?? '-';
                $row['Akun']        = $t->account->name ?? '-';
                $row['Jumlah Unit'] = $t->amount;
                $row['Harga/Unit']  = $t->price_per_unit;
                $row['Fee']         = $t->fee;
                $row['Total Nilai'] = $t->total_value;
                $row['Catatan']     = $t->notes;

                fputcsv($file, array_values($row));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * 📤 IMPORT DATA DARI CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $csvData = array_map('str_getcsv', file($file));
        
        // Hapus header (baris pertama)
        $header = array_shift($csvData);

        // Loop setiap baris
        $count = 0;
        foreach ($csvData as $row) {
            // Asumsi urutan kolom CSV sesuai dengan urutan Export di atas
            // [0]Tanggal, [1]Tipe, [2]Kode, [3]Nama, [4]Kategori, [5]Akun, [6]Unit, [7]Harga, [8]Fee, [9]Total, [10]Catatan
            
            // 1. Cari/Buat Product
            // Kita cari berdasarkan Kode. Jika tidak ada, skip atau buat baru (disini kita skip saja biar aman)
            $product = Product::where('user_id', Auth::id())->where('code', $row[2])->first();
            if (!$product && $row[2] != '-') {
                // Opsional: Buat produk otomatis jika tidak ada
                $product = Product::create([
                    'user_id' => Auth::id(), 'code' => $row[2], 'name' => $row[3], 'category' => strtolower($row[4] ?? 'saham')
                ]);
            }

            // 2. Cari/Buat Akun
            $account = Account::where('user_id', Auth::id())->where('name', $row[5])->first();
            
            // 3. Simpan Transaksi
            Transaction::create([
                'user_id'          => Auth::id(),
                'transaction_date' => $row[0],
                'type'             => $row[1],
                'product_id'       => $product ? $product->id : null,
                'account_id'       => $account ? $account->id : null,
                'amount'           => floatval($row[6]),
                'price_per_unit'   => floatval($row[7]),
                'fee'              => floatval($row[8]),
                'total_value'      => floatval($row[9]),
                'notes'            => $row[10] ?? null,
            ]);
            $count++;
        }

        return redirect()->back()->with('success', "Berhasil mengimport $count transaksi!");
    }
}