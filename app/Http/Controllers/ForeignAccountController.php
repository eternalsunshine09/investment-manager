<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class ForeignAccountController extends Controller
{
    public function index()
    {
        // 1. Ambil Akun yang BUKAN IDR
        $accounts = Account::where('user_id', Auth::id())
            ->where('currency', '!=', 'IDR')
            ->orderBy('balance', 'desc') // Urutkan dari saldo terbesar
            ->get();

        // 2. Hitung Total Saldo Asing (Estimasi)
        $totalForeignBalance = $accounts->sum('balance');

        // Di halaman Index tidak perlu load transaksi lagi (sudah dipindah ke Show)
        return view('foreign_accounts.index', compact('accounts', 'totalForeignBalance'));
    }

    public function show($id)
    {
        // 1. Ambil Detail Akun (Pastikan milik user & Bukan IDR)
        $account = Account::where('user_id', Auth::id())
            ->where('currency', '!=', 'IDR')
            ->findOrFail($id);

        // 2. Ambil Transaksi KHUSUS akun ini
        $transactions = $account->transactions()
            ->orderBy('transaction_date', 'desc')
            ->paginate(15);

        return view('foreign_accounts.show', compact('account', 'transactions'));
    }
}