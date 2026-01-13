<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
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

        return back()->with('success', 'Rekening berhasil ditambahkan!');
    }

    // --- TAMBAHKAN FUNGSI INI ---
    public function update(Request $request, $id)
    {
        $account = Account::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
        ]);

        $account->update([
            'name' => $request->name,
            'bank_name' => $request->bank_name,
        ]);

        return back()->with('success', 'Rekening berhasil diperbarui!');
    }
    // -----------------------------

    public function destroy($id)
    {
        $account = Account::where('user_id', Auth::id())->findOrFail($id);
        $account->delete();

        return back()->with('success', 'Rekening berhasil dihapus.');
    }
}