<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class ForeignAccountController extends Controller
{
    public function index()
    {
        // 1. Ambil Akun yang BUKAN IDR
        $accounts = Account::where('user_id', Auth::id())
            ->where('currency', '!=', 'IDR')
            ->get();

        // 2. Hitung Total Saldo Asing (Asumsi mayoritas USD)
        $totalForeignBalance = $accounts->sum('balance');

        // 3. Ambil Transaksi khusus akun-akun tersebut (History Valas)
        $transactions = Transaction::where('user_id', Auth::id())
            ->whereIn('account_id', $accounts->pluck('id'))
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);

        return view('foreign_accounts.index', compact('accounts', 'totalForeignBalance', 'transactions'));
    }
}