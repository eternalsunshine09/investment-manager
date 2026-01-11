<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        // Ambil akun milik user
        $accounts = Account::where('user_id', Auth::id())->get();
        return view('accounts.index', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
        ]);

        Account::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'bank_name' => $request->bank_name,
            'balance' => 0 // Saldo awal 0
        ]);

        return redirect()->back()->with('success', 'Rekening berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $account = Account::where('user_id', Auth::id())->findOrFail($id);
        
        // Opsional: Cek jika ada transaksi
        // if($account->transactions()->exists()) { return back()->with('error', 'Gagal hapus, akun ini ada transaksinya.'); }

        $account->delete();
        return redirect()->back()->with('success', 'Rekening berhasil dihapus.');
    }
}