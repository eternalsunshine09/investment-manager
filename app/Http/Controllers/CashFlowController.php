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

<<<<<<< Updated upstream
    public function store(Request $request)
    {
        CashFlow::create([
            'user_id' => Auth::id(),
=======
   public function store(Request $request)
{
    $request->validate([
        'account_id' => 'required|exists:accounts,id',
        'type' => 'required|in:income,expense',
        'amount' => 'required|numeric|min:0',
        'date' => 'required|date',
    ]);

    DB::transaction(function () use ($request) {
        // 1. Simpan Transaksi ke Riwayat Cashflow
        CashFlow::create([
            'user_id' => Auth::id(),
            'account_id' => $request->account_id,
>>>>>>> Stashed changes
            'type' => $request->type,
            'category' => $request->category,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description
        ]);

<<<<<<< Updated upstream
        return back()->with('success', 'Data berhasil dicatat!');
    }
=======
        // 2. Update Saldo Akun yang dipilih
        $account = Account::find($request->account_id);
        if ($request->type == 'income') {
            $account->increment('balance', $request->amount); // Saldo +
        } else {
            $account->decrement('balance', $request->amount); // Saldo -
        }
    });

    return back()->with('success', 'Transaksi berhasil dicatat & saldo diperbarui!');
}
>>>>>>> Stashed changes

    public function destroy($id)
    {
        CashFlow::where('user_id', Auth::id())->where('id', $id)->delete();
        return back()->with('success', 'Data dihapus.');
    }
}