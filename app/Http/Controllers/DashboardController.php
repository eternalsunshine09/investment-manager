<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\PortfolioSnapshot;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $year = $request->get('year', date('Y'));

        // 1. Hitung Saldo Tunai (RDN)
        $totalCash = Account::where('user_id', $userId)->sum('balance');

        // 2. Ambil Semua Produk untuk Perhitungan Aset & PnL
        $products = Product::where('user_id', $userId)->with('transactions')->get();
        
        $totalAssetValue = 0;
        $totalRealizedPnL = 0;   // <--- PASTIKAN INI DIINISIALISASI
        $totalUnrealizedPnL = 0; // <--- PASTIKAN INI DIINISIALISASI
        $composition = ['KAS/RDN' => (float)$totalCash];

        foreach ($products as $product) {
            $beliUnits = $product->transactions->whereIn('type', ['beli', 'dividen_unit'])->sum('amount');
            $jualUnits = $product->transactions->where('type', 'jual')->sum('amount');
            $sisaUnit = $beliUnits - $jualUnits;

            // Hitung Harga Beli Rata-rata (Avg Price)
            $totalModalUang = $product->transactions->where('type', 'beli')->sum('total_value');
            $avgPrice = ($beliUnits > 0) ? ($totalModalUang / $beliUnits) : 0;

            // Hitung Market Value
            $marketValue = $sisaUnit * $product->current_price;
            $totalAssetValue += $marketValue;

            // Hitung Realized PnL (Cuan yang sudah cair)
            $totalUangJual = $product->transactions->where('type', 'jual')->sum('total_value');
            $totalRealizedPnL += ($totalUangJual - ($jualUnits * $avgPrice));

            // Hitung Unrealized PnL (Cuan melayang)
            $totalUnrealizedPnL += ($marketValue - ($sisaUnit * $avgPrice));

            if ($sisaUnit > 0) {
                $cat = strtoupper($product->category);
                $composition[$cat] = ($composition[$cat] ?? 0) + $marketValue;
            }
        }

        $netWorth = $totalCash + $totalAssetValue;

        // 3. Logika Target Keuangan (Goals) Terhubung Produk
        $goals = Goal::where('user_id', $userId)->with('products.transactions')->get()->map(function($goal) {
            $currentVal = 0;
            foreach ($goal->products as $p) {
                $u = $p->transactions->whereIn('type', ['beli', 'dividen_unit'])->sum('amount') - $p->transactions->where('type', 'jual')->sum('amount');
                $currentVal += ($u * $p->current_price);
            }
            $goal->percentage = ($goal->target_amount > 0) ? min(round(($currentVal / $goal->target_amount) * 100, 1), 100) : 0;
            return $goal;
        });

        // 4. Ambil Data Tren (Snapshot)
        $snapshots = PortfolioSnapshot::where('user_id', $userId)->whereYear('snapshot_date', $year)->orderBy('snapshot_date', 'asc')->get();
        $growthLabels = $snapshots->pluck('snapshot_date')->toArray();
        $growthData = $snapshots->pluck('net_worth')->toArray();
        if (empty($growthData)) { $growthLabels = [date('d M')]; $growthData = [$netWorth]; }

        $recentTransactions = Transaction::where('user_id', $userId)->with(['product'])->latest('transaction_date')->limit(5)->get();

        // 5. Kirim Semua Variabel ke View
        return view('dashboard', compact(
            'totalCash', 
            'totalAssetValue', 
            'netWorth', 
            'totalRealizedPnL',    // <--- SUDAH TERKIRIM
            'totalUnrealizedPnL',  // <--- SUDAH TERKIRIM
            'recentTransactions',
            'composition', 
            'growthLabels', 
            'growthData', 
            'goals'
        ));
    }
}