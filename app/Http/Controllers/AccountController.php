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
    
    // Menampilkan Detail RDN + Riwayat Transaksinya
    public function show($id)
    {
        // 1. Ambil data Akun berdasarkan ID dan User yang login
        $account = Account::where('user_id', Auth::id())->findOrFail($id);

        // 2. Ambil Transaksi KHUSUS milik akun ini saja
        // Kita urutkan dari tanggal terbaru
        $transactions = $account->transactions()
                                ->with('product') // Eager load product biar ringan
                                ->orderBy('transaction_date', 'desc')
                                ->paginate(10); // Pakai pagination biar rapi

        // 3. Lempar ke view baru (nanti kita buat viewnya)
        return view('accounts.show', compact('account', 'transactions'));
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