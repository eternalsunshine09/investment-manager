<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Goal;
use App\Models\Watchlist;
use App\Models\PortfolioSnapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // ==========================================
        // 1. HITUNG DATA SAAT INI (REALTIME)
        // ==========================================
        $totalCash = Account::where('user_id', $userId)->sum('balance');
        $products = Product::where('user_id', $userId)->with('transactions')->get();
        
        $totalAssetValue = 0;
        $totalModalHoldings = 0; 
        $composition = ['KAS/RDN' => (float)$totalCash];
        $productPerformance = [];

        foreach ($products as $product) {
            // Hitung Unit & Modal
            $beliUnits = $product->transactions->whereIn('type', ['beli', 'dividen_unit', 'right_issue'])->sum('amount');
            $jualUnits = $product->transactions->where('type', 'jual')->sum('amount');
            $sisaUnit = $beliUnits - $jualUnits;

            $totalModalBeli = $product->transactions->whereIn('type', ['beli', 'right_issue'])->sum('total_value');
            $avgPrice = ($beliUnits > 0) ? ($totalModalBeli / $beliUnits) : 0;

            if ($sisaUnit > 0) {
                $marketValue = $sisaUnit * $product->current_price;
                $totalAssetValue += $marketValue;
                
                $modalTertahan = $sisaUnit * $avgPrice;
                $totalModalHoldings += $modalTertahan;

                $pnlAmt = $marketValue - $modalTertahan;
                $pnlPct = ($modalTertahan > 0) ? ($pnlAmt / $modalTertahan) * 100 : 0;

                $cat = strtoupper($product->category ?? 'LAINNYA');
                $composition[$cat] = ($composition[$cat] ?? 0) + $marketValue;

                $productPerformance[] = [
                    'code' => $product->code ?? $product->name,
                    'price' => $product->current_price,
                    'pnl_pct' => round($pnlPct, 2),
                    'pnl_amt' => $pnlAmt
                ];
            }
        }

        $netWorth = $totalCash + $totalAssetValue;
        $profit = $totalAssetValue - $totalModalHoldings; 
        $isProfit = $profit >= 0;
        $totalInvested = $totalAssetValue;

        // ==========================================
        // 2. LOGIC CHART (REVERSE CALCULATION)
        // ==========================================
        
        // Cari tanggal transaksi paling PERTAMA (Untuk filter "Semua")
        $firstTrx = Transaction::where('user_id', $userId)->orderBy('transaction_date', 'asc')->first();
        
        $endDate = Carbon::now();
        // Jika belum ada transaksi, default mundur 30 hari
        $startDate = $firstTrx ? Carbon::parse($firstTrx->transaction_date) : Carbon::now()->subDays(30);

        // Ambil SEMUA transaksi dari awal sampai sekarang
        $transactions = Transaction::where('user_id', $userId)
            ->whereDate('transaction_date', '>=', $startDate)
            ->orderBy('transaction_date', 'desc')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->transaction_date)->format('Y-m-d');
            });

        $chartData = [];
        $runningBalance = $netWorth; // Mulai dari saldo HARI INI
        
        // Loop mundur
        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = array_reverse(iterator_to_array($period)); 

        foreach ($dates as $date) {
            $dateStr = $date->format('Y-m-d');
            
            $chartData[] = [
                'x' => $dateStr,
                'y' => $runningBalance
            ];

            if (isset($transactions[$dateStr])) {
                foreach ($transactions[$dateStr] as $trx) {
                    // Balikkan efek transaksi
                    if (in_array($trx->type, ['topup', 'dividen_cash'])) {
                        $runningBalance -= $trx->total_value; 
                    } elseif ($trx->type == 'tarik') {
                        $runningBalance += $trx->total_value; 
                    }
                    $runningBalance += ($trx->fee ?? 0);
                }
            }
        }

        // Balik urutan jadi: Lama -> Baru
        $allChartData = array_reverse($chartData);

        // ==========================================
        // 3. LOGIC LAINNYA
        // ==========================================
        // Hitung growth bulanan manual dari data chart
        $lastMonthDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
        $lastMonthValue = collect($allChartData)->firstWhere('x', $lastMonthDate)['y'] ?? 0;
        
        if ($lastMonthValue == 0) {
            $lastMonthValue = PortfolioSnapshot::where('user_id', $userId)
                ->where('snapshot_date', '<=', Carbon::now()->startOfMonth())
                ->orderBy('snapshot_date', 'desc')
                ->value('net_worth') ?? 0;
        }

        $growthPercentage = ($lastMonthValue > 0) ? (($netWorth - $lastMonthValue) / $lastMonthValue) * 100 : 0;

        $coll = collect($productPerformance);
        $topGainers = $coll->sortByDesc('pnl_pct')->take(2);
        $worstPerformers = $coll->sortBy('pnl_pct')->take(2);
        
        $watchlists = Watchlist::where('user_id', $userId)->limit(5)->get();
        
        $goals = Goal::where('user_id', $userId)->with('products')->get()->map(function($goal) {
            $currentVal = 0;
            foreach ($goal->products as $p) {
                $u = $p->transactions->whereIn('type', ['beli', 'dividen_unit'])->sum('amount') - 
                     $p->transactions->where('type', 'jual')->sum('amount');
                $currentVal += ($u * $p->current_price);
            }
            $goal->percentage = ($goal->target_amount > 0) ? min(round(($currentVal / $goal->target_amount) * 100, 1), 100) : 0;
            return $goal;
        })->sortByDesc('percentage');

        $allProducts = Product::where('user_id', $userId)->orderBy('code')->get();
        $insights = [];

        return view('dashboard', compact(
            'netWorth', 'totalCash', 'totalInvested', 'totalAssetValue', 
            'profit', 'isProfit', 'growthPercentage', 'composition', 
            'topGainers', 'worstPerformers', 'insights', 'goals', 
            'watchlists', 'allChartData', 'allProducts'
        ));
    }

    // ... method storeGoal, updateGoal, destroyGoal biarkan saja ...
}