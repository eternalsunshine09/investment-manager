<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashFlow;
use App\Models\Account; // Load Model Account
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Untuk Transaction

class CashFlowController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Ambil Data Cashflow
        $flows = CashFlow::with('account') // Eager load account
                    ->where('user_id', $userId)
                    ->latest()
                    ->get();
        
        // Ambil Data Akun (Dipisahkan jenisnya jika ada kolom type, atau ambil semua)
        $accounts = Account::where('user_id', $userId)->get();

        $income = $flows->where('type', 'income')->sum('amount');
        $expense = $flows->where('type', 'expense')->sum('amount');
        $savingsRate = $income > 0 ? (($income - $expense) / $income * 100) : 0;
        
        return view('cashflow.index', compact('flows', 'income', 'expense', 'savingsRate', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:income,expense',
            // ... validasi lain
        ]);

        DB::transaction(function () use ($request) {
            // 1. Simpan Transaksi Cashflow
            CashFlow::create([
                'user_id' => Auth::id(),
                'account_id' => $request->account_id, // Simpan ID Akun
                'type' => $request->type,
                'category' => $request->category,
                'amount' => $request->amount,
                'date' => $request->date,
                'description' => $request->description
            ]);

            // 2. Update Saldo Akun Otomatis
            $account = Account::find($request->account_id);
            if ($request->type == 'income') {
                $account->increment('balance', $request->amount);
            } else {
                $account->decrement('balance', $request->amount);
            }
        });

        return back()->with('success', 'Data berhasil dicatat & saldo diperbarui!');
    }

    public function destroy($id)
    {
        $flow = CashFlow::where('user_id', Auth::id())->findOrFail($id);

        DB::transaction(function () use ($flow) {
            // Kembalikan Saldo sebelum hapus (Revert)
            $account = Account::find($flow->account_id);
            if ($account) {
                if ($flow->type == 'income') {
                    // Jika dulu pemasukan dihapus, saldo berkurang
                    $account->decrement('balance', $flow->amount);
                } else {
                    // Jika dulu pengeluaran dihapus, saldo bertambah (uang balik)
                    $account->increment('balance', $flow->amount);
                }
            }
            $flow->delete();
        });

        return back()->with('success', 'Data dihapus & saldo dikembalikan.');
    }
}