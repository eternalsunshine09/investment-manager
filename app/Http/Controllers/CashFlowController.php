<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashFlow;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CashFlowController extends Controller
{
    public function index()
{
    $flows = CashFlow::where('user_id', auth()->id())
                     ->latest()
                     ->get();
    
    $income = $flows->where('type', 'income')->sum('amount');
    $expense = $flows->where('type', 'expense')->sum('amount');
    $savingsRate = $income > 0 ? (($income - $expense) / $income * 100) : 0;
    
    return view('cashflow.index', compact('flows', 'income', 'expense', 'savingsRate'));
}

    public function store(Request $request)
    {
        CashFlow::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'category' => $request->category,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description
        ]);

        return back()->with('success', 'Data berhasil dicatat!');
    }

    public function destroy($id)
    {
        CashFlow::where('user_id', Auth::id())->where('id', $id)->delete();
        return back()->with('success', 'Data dihapus.');
    }
}