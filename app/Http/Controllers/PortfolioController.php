<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    /**
     * HALAMAN 1: PORTOFOLIO (Route: /portfolio)
     * Target: Menampilkan Tabel Untung/Rugi (PnL)
     * View: resources/views/portfolio/index.blade.php
     */
    public function index()
    {
        $products = Product::whereHas('transactions', function($q) {
            $q->where('user_id', Auth::id());
        })->get();

        $summary = [];

        foreach ($products as $product) {
            $txs = Transaction::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->get();

            // --- UPDATE LOGIKA PERHITUNGAN (Support Corporate Action) ---
            
            // 1. Total Unit Masuk (Beli + Bonus Saham + Stock Split + Right Issue)
            $totalBuyUnits = $txs->whereIn('type', ['beli', 'dividen_unit', 'stock_split', 'right_issue'])->sum('amount');
            
            // 2. Total Uang Keluar / Modal (Hanya Beli & Tebus Right Issue)
            $totalBuyMoney = $txs->whereIn('type', ['beli', 'right_issue'])->sum('total_value');
            
            // 3. Total Unit Keluar (Jual + Reverse Split)
            $totalSellUnits = $txs->whereIn('type', ['jual', 'reverse_split'])->sum('amount');
            
            // 4. Total Uang Masuk / Realized (Hanya Jual)
            $totalSellMoney = $txs->where('type', 'jual')->sum('total_value');

            // Hitung Sisa Unit
            $currentUnits = $totalBuyUnits - $totalSellUnits;
            
            if ($currentUnits <= 0) continue;

            // Hitung Rata-rata Pembelian (Average Price)
            $avgPrice = $totalBuyUnits > 0 ? ($totalBuyMoney / $totalBuyUnits) : 0;
            $currentPrice = $product->current_price ?? 0;
            
            // Hitung Value & Modal untuk Tabel
            $marketValue = $currentUnits * $currentPrice;
            $costBasis = $currentUnits * $avgPrice; // Ini MODAL
            
            $unrealizedPnL = $marketValue - $costBasis;
            $pnlPercentage = $costBasis > 0 ? ($unrealizedPnL / $costBasis) * 100 : 0;
            $realizedPnL = $totalSellMoney - ($totalSellUnits * $avgPrice);

            $summary[] = [
                'code'          => $product->code ?? $product->name,
                'category'      => $product->category,
                'units'         => $currentUnits,
                'avg_price'     => $avgPrice,
                'current_price' => $currentPrice,
                'total_cost'    => $costBasis,   // Data Modal
                'market_value'  => $marketValue, // Data Nilai Aset
                'realized_pnl'  => $realizedPnL,
                'unrealized_pnl'=> $unrealizedPnL,
                'pnl_pct'       => $pnlPercentage
            ];
        }

        return view('portfolio.index', compact('summary'));
    }

    /**
     * HALAMAN 2: REKAPAN ASET (Route: /rekapan-aset)
     * Target: Menampilkan Form Update Harga
     */
    public function assetSummary()
    {
        $products = Product::with('transactions')->whereHas('transactions', function($q) {
            $q->where('user_id', Auth::id());
        })->get();

        $data = $products->map(function ($product) {
            $txs = $product->transactions->where('user_id', Auth::id());

            // Gunakan logika yang sama untuk menghitung unit & modal
            $buyUnits = $txs->whereIn('type', ['beli', 'dividen_unit', 'stock_split', 'right_issue'])->sum('amount');
            $buyMoney = $txs->whereIn('type', ['beli', 'right_issue'])->sum('total_value');
            $sellUnits = $txs->whereIn('type', ['jual', 'reverse_split'])->sum('amount');
            
            $units = $buyUnits - $sellUnits;

            if ($units <= 0) return null;

            $avgPrice = $buyUnits > 0 ? ($buyMoney / $buyUnits) : 0;
            $totalCost = $units * $avgPrice;

            return [
                'id'            => $product->id,
                'code'          => $product->code ?? $product->name,
                'name'          => $product->name,
                'category'      => $product->category,
                'quantity'      => $units,
                'current_price' => $product->current_price ?? 0,
                'total_cost'    => $totalCost,
                'market_value'  => $units * ($product->current_price ?? 0),
            ];
        })->filter();

        $portfolio = $data->groupBy('category');

        return view('asset.summary', compact('portfolio'));
    }

    /**
     * ACTION: UPDATE HARGA
     */
    public function updatePrices(Request $request)
    {
        $request->validate([
            'prices' => 'required|array',
            'prices.*' => 'numeric|min:0',
        ]);

        foreach ($request->prices as $productId => $newPrice) {
            $product = Product::find($productId);
            if ($product) {
                $product->current_price = $newPrice;
                $product->save();
            }
        }

        return redirect()->route('asset.summary')->with('success', 'Harga pasar berhasil diperbarui!');
    }
}