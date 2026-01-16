<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class ForeignAccountController extends Controller
{
    public function index()
    {
        // 1. Ambil Akun Valas
        $accounts = Account::where('user_id', Auth::id())
            ->where('currency', '!=', 'IDR')
            ->orderBy('balance', 'desc')
            ->get();

        // 2. Hitung Total Estimasi Aset dalam IDR
        // Menggunakan Average Rate dari masing-masing akun
        $totalForeignBalanceIDR = 0;
        
        foreach ($accounts as $acc) {
            // Jika punya saldo dan rate rata-rata > 0, kalikan
            if ($acc->balance > 0 && $acc->average_rate > 0) {
                $totalForeignBalanceIDR += ($acc->balance * $acc->average_rate);
            } 
            // Opsional: Jika tidak ada rate rata-rata, bisa pakai rate manual/default (misal 15500 untuk USD)
            // else if ($acc->currency == 'USD') { $totalForeignBalanceIDR += ($acc->balance * 16000); }
        }

        return view('foreign_accounts.index', compact('accounts', 'totalForeignBalanceIDR'));
    }

    public function show($id)
    {
        $account = Account::where('user_id', Auth::id())
            ->where('currency', '!=', 'IDR')
            ->findOrFail($id);

        $transactions = $account->transactions()
            ->orderBy('transaction_date', 'desc')
            ->paginate(15);

        return view('foreign_accounts.show', compact('account', 'transactions'));
    }
}