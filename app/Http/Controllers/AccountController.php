<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    // Halaman Daftar Rekening
    public function index()
    {
        $userId = Auth::id();
        $accounts = Account::where('user_id', $userId)
                    ->orderBy('is_active', 'desc')
                    ->get();

        return view('accounts.index', compact('accounts'));
    }

    // Proses Tambah Rekening Baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'initial_balance' => 'nullable|numeric|min:0',
        ]);

        Account::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'bank_name' => $request->bank_name,
            'initial_balance' => $request->initial_balance ?? 0,
            'balance' => $request->initial_balance ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Rekening berhasil ditambahkan!');
    }

    // Halaman Detail (Menggabungkan Cashflow & Investment History)
    public function show($id)
    {
        $account = Account::where('user_id', Auth::id())->findOrFail($id);

        // 1. Query CashFlow (Tabel ini punya kolom description, jadi aman)
        $cashflows = DB::table('cash_flows')
            ->where('account_id', $id)
            ->select(
                'id', 
                'amount', 
                'type', 
                'category', 
                'description', 
                'date as transaction_date', 
                DB::raw("'cashflow' as source"),
                DB::raw("NULL as product_name")
            );

        // 2. Query Transaksi Investasi (FIX: Gunakan NULL untuk description)
        $investments = DB::table('transactions')
            ->where('account_id', $id)
            ->select(
                'id', 
                'amount', 
                'type', 
                DB::raw("'Investasi' as category"), 
                
                // --- BAGIAN YANG DIPERBAIKI ---
                // Kita isi NULL karena tabel transactions tidak punya kolom description
                DB::raw("NULL as description"), 
                // ------------------------------
                
                'transaction_date', 
                DB::raw("'investment' as source"),
                DB::raw("NULL as product_name") 
            );

        // 3. Gabungkan dan Paginate
        $transactions = $cashflows->union($investments)
                        ->orderBy('transaction_date', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate(15);

        return view('accounts.show', compact('account', 'transactions'));
    }

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

        return back()->with('success', 'Data rekening diperbarui.');
    }

    public function destroy($id)
    {
        $account = Account::where('user_id', Auth::id())->findOrFail($id);
        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil dihapus.');
    }
}