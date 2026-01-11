<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    // TAMPILKAN DAFTAR AKUN
    public function index()
    {
        // Ambil semua akun milik user login
        $accounts = Account::where('user_id', Auth::id())->get();

        // Kirim variabel '$accounts' (JAMAK) ke view
        return view('accounts.index', compact('accounts'));
    }

    // SIMPAN AKUN BARU
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
            'balance' => 0 // Saldo awal selalu 0, harus Top Up via Transaksi
        ]);

        return back()->with('success', 'Rekening berhasil ditambahkan!');
    }

    // HAPUS AKUN
    public function destroy($id)
    {
        $account = Account::where('user_id', Auth::id())->findOrFail($id);
        
        // Cek apakah akun ini punya transaksi?
        // Jika ingin aman, bisa dicek dulu. Tapi karena di database kita pakai cascadeOnDelete,
        // maka transaksi terkait akan ikut terhapus otomatis.
        
        $account->delete();

        return back()->with('success', 'Rekening berhasil dihapus.');
    }
}