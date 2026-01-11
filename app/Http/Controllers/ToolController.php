<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Allocation;
use App\Models\CalendarEvent;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ToolController extends Controller
{
    // --- FITUR 1: REBALANCING ---
    public function rebalance()
    {
        $userId = Auth::id();

        // 1. Ambil Nilai Portofolio Saat Ini (Real)
        $products = Product::with(['transactions' => function($q) use ($userId) {
            $q->where('user_id', $userId);
        }])->whereHas('transactions', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->get();

        $portfolio = [];
        $totalPortfolioValue = 0;

        // Grouping data per kategori
        $grouped = $products->groupBy('category');

        foreach ($grouped as $category => $items) {
            $catValue = 0;
            foreach ($items as $item) {
                // Hitung Unit
                $buyUnits = $item->transactions->whereIn('type', ['beli', 'dividen_unit', 'stock_split', 'right_issue'])->sum('amount');
                $sellUnits = $item->transactions->whereIn('type', ['jual', 'reverse_split'])->sum('amount');
                $units = $buyUnits - $sellUnits;

                if ($units > 0) {
                    $catValue += $units * $item->current_price;
                }
            }
            $portfolio[$category] = $catValue;
            $totalPortfolioValue += $catValue;
        }

        // Tambahkan Cash (Optional, kalau mau hitung cash sebagai aset)
        // $totalCash = ... (Ambil dari Account::sum('balance'))
        // $portfolio['cash'] = $totalCash;
        // $totalPortfolioValue += $totalCash;

        // 2. Ambil Target Alokasi User
        $targets = Allocation::where('user_id', $userId)->pluck('percentage', 'category')->toArray();

        // 3. Hitung Selisih (Rebalancing Logic)
        $analysis = [];
        foreach ($portfolio as $cat => $currentVal) {
            $targetPct = $targets[$cat] ?? 0;
            $currentPct = $totalPortfolioValue > 0 ? ($currentVal / $totalPortfolioValue) * 100 : 0;
            
            $targetVal = $totalPortfolioValue * ($targetPct / 100);
            $actionValue = $targetVal - $currentVal; // Positif = Beli, Negatif = Jual

            $analysis[] = [
                'category' => $cat,
                'current_value' => $currentVal,
                'current_pct' => $currentPct,
                'target_pct' => $targetPct,
                'action_value' => $actionValue,
                'action' => $actionValue > 0 ? 'BELI' : ($actionValue < 0 ? 'JUAL' : 'TEPAT')
            ];
        }

        return view('tools.rebalance', compact('analysis', 'targets', 'totalPortfolioValue'));
    }

    public function saveAllocation(Request $request)
    {
        // Validasi total harus 100% (Opsional, tapi disarankan)
        // $total = array_sum($request->allocation);

        foreach ($request->allocation as $category => $pct) {
            Allocation::updateOrCreate(
                ['user_id' => Auth::id(), 'category' => $category],
                ['percentage' => $pct]
            );
        }

        return redirect()->back()->with('success', 'Target alokasi berhasil disimpan!');
    }

    // --- FITUR 2: KALENDER KEUANGAN ---
    public function calendar()
    {
        $events = CalendarEvent::where('user_id', Auth::id())
            ->orderBy('event_date', 'asc')
            ->whereDate('event_date', '>=', Carbon::today()) // Tampilkan yg akan datang
            ->get();
            
        return view('tools.calendar', compact('events'));
    }

    public function storeEvent(Request $request)
    {
        CalendarEvent::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'event_date' => $request->event_date,
            'type' => $request->type,
            'description' => $request->description
        ]);

        return redirect()->back()->with('success', 'Jadwal berhasil dicatat!');
    }
    
    public function destroyEvent($id)
    {
        CalendarEvent::where('user_id', Auth::id())->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Jadwal dihapus.');
    }
}