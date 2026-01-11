<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\PortfolioSnapshot;
use App\Models\Goal;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $year = $request->get('year', date('Y'));

        // --- 1. DATA DASAR ---
        $totalCash = Account::where('user_id', $userId)->sum('balance');
        $products = Product::where('user_id', $userId)->with('transactions')->get();
        
        $totalAssetValue = 0;
        $totalModalHoldings = 0; 
        $composition = ['KAS/RDN' => (float)$totalCash];
        $productPerformance = [];

        foreach ($products as $product) {
            $beliUnits = $product->transactions->whereIn('type', ['beli', 'dividen_unit'])->sum('amount');
            $jualUnits = $product->transactions->where('type', 'jual')->sum('amount');
            $sisaUnit = $beliUnits - $jualUnits;

            $totalModalBeli = $product->transactions->where('type', 'beli')->sum('total_value');
            $avgPrice = ($beliUnits > 0) ? ($totalModalBeli / $beliUnits) : 0;

            if ($sisaUnit > 0) {
                $marketValue = $sisaUnit * $product->current_price;
                $totalAssetValue += $marketValue;
                
                $modalTertahan = $sisaUnit * $avgPrice;
                $totalModalHoldings += $modalTertahan;

                $pnlAmt = $marketValue - $modalTertahan;
                $pnlPct = ($modalTertahan > 0) ? ($pnlAmt / $modalTertahan) * 100 : 0;

                $cat = strtoupper($product->type ?? 'LAINNYA');
                $composition[$cat] = ($composition[$cat] ?? 0) + $marketValue;

                $productPerformance[] = [
                    'code' => $product->code ?? $product->name,
                    'price' => $product->current_price,
                    'pnl_pct' => round($pnlPct, 2),
                    'pnl_amt' => $pnlAmt
                ];
            }
        }

        // --- 2. HITUNG NET WORTH & PROFIT ---
        $netWorth = $totalCash + $totalAssetValue;
        $profit = $totalAssetValue - $totalModalHoldings; 
        $isProfit = $profit >= 0;

        // --- 3. HITUNG PERTUMBUHAN BULANAN ---
        $startOfMonthNetWorth = PortfolioSnapshot::where('user_id', $userId)
            ->where('snapshot_date', '<=', Carbon::now()->startOfMonth())
            ->orderBy('snapshot_date', 'desc')
            ->value('net_worth');

        $growthPercentage = 0;
        if ($startOfMonthNetWorth > 0) {
            $growthPercentage = (($netWorth - $startOfMonthNetWorth) / $startOfMonthNetWorth) * 100;
        }

        // --- 4. TOP & WORST PERFORMERS ---
        $coll = collect($productPerformance);
        $topGainers = $coll->sortByDesc('pnl_pct')->take(2);
        $worstPerformers = $coll->sortBy('pnl_pct')->take(2);

        // --- 5. LOGIKA INSIGHT ---
        $watchlists = Watchlist::where('user_id', $userId)
            ->limit(3)
            ->get()
            ->map(function($item) {
                // Hitung seberapa dekat harga pasar dengan target beli
                if ($item->current_price > 0 && $item->target_price > 0) {
                    $item->gap = (($item->current_price - $item->target_price) / $item->target_price) * 100;
                } else {
                    $item->gap = 0;
                }
                return $item;
            })
            ->sortBy('gap');

        arsort($composition);
        $topAsset = array_key_first($composition);
        $topAssetVal = reset($composition);
        $topAssetPct = ($netWorth > 0) ? ($topAssetVal / $netWorth) * 100 : 0;
        
        if ($topAssetPct > 50 && $topAsset !== 'KAS/RDN') {
            $insights[] = ['type' => 'warning', 'icon' => '📊', 'text' => "$topAsset mendominasi " . round($topAssetPct) . "% portofolio."];
        } else {
            $insights[] = ['type' => 'success', 'icon' => '✅', 'text' => "Diversifikasi portofolio seimbang."];
        }

        // --- 6. TARGET KEUANGAN (GOALS) ---
        $goals = Goal::where('user_id', $userId)->with('products')->get()->map(function($goal) {
            $currentVal = 0;
            foreach ($goal->products as $p) {
                $u = $p->transactions->whereIn('type', ['beli', 'dividen_unit'])->sum('amount') - $p->transactions->where('type', 'jual')->sum('amount');
                $currentVal += ($u * $p->current_price);
            }
            $goal->percentage = ($goal->target_amount > 0) ? min(round(($currentVal / $goal->target_amount) * 100, 1), 100) : 0;
            return $goal;
        });

        // HANYA DEKLARASIKAN SEKALI SAJA
        $goal = $goals->where('percentage', '<', 100)->sortByDesc('percentage')->first() ?? $goals->first();

        // --- 7. DATA CHART ---
        $snapshots = PortfolioSnapshot::where('user_id', $userId)
            ->whereYear('snapshot_date', $year)
            ->orderBy('snapshot_date', 'asc')
            ->get();
            
        $growthLabels = $snapshots->map(fn($s) => Carbon::parse($s->snapshot_date)->format('d M'))->toArray();
        $growthData = $snapshots->pluck('net_worth')->toArray();

        if (empty($growthData)) { 
            $growthLabels = [date('d M')]; 
            $growthData = [$netWorth]; 
        }

        $totalInvested = $totalAssetValue; 

        // --- 8. RETURN VIEW (PERBAIKAN COMPACT) ---
        return view('dashboard', compact(
            'netWorth',
            'totalCash',
            'watchlists',
            'totalInvested',
            'totalAssetValue',
            'profit',
            'isProfit',
            'growthPercentage',
            'composition',
            'topGainers',
            'worstPerformers',
            'insights',
            'goal',          // <--- HANYA 'goal', JANGAN ADA 'primaryGoal'
            'growthLabels',
            'growthData'
        ));
    }
}