<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction; // Asumsi model Transaction yang sudah ada
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ConversionController extends Controller
{
    public function index()
    {
        // Ambil akun berdasarkan mata uang
        $idrAccounts = Account::where('user_id', Auth::id())->where('currency', 'IDR')->get();
        $usdAccounts = Account::where('user_id', Auth::id())->where('currency', 'USD')->get();

        return view('conversion.index', compact('idrAccounts', 'usdAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id'   => 'required|exists:accounts,id|different:from_account_id',
            'amount_idr'      => 'required|numeric|min:1',
            'exchange_rate'   => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Hitung Hasil USD
            $usdResult = $request->amount_idr / $request->exchange_rate;

            // 2. Catat Pengeluaran di Akun IDR (Type: Tarik/Transfer Out)
            Transaction::create([
                'user_id' => Auth::id(),
                'account_id' => $request->from_account_id,
                'transaction_date' => now(),
                'type' => 'tarik', // Atau buat tipe baru 'transfer_out'
                'amount' => 1, // Logika dummy unit
                'price_per_unit' => $request->amount_idr, // Total Rupiah keluar
                'total_value' => $request->amount_idr,
                'notes' => 'Konversi ke USD (Rate: ' . number_format($request->exchange_rate) . ')',
            ]);

            // 3. Catat Pemasukan di Akun USD (Type: Topup/Transfer In)
            Transaction::create([
                'user_id' => Auth::id(),
                'account_id' => $request->to_account_id,
                'transaction_date' => now(),
                'type' => 'topup', // Atau buat tipe baru 'transfer_in'
                'amount' => 1,
                // Di sini 'price' adalah nominal USD yang diterima
                'price_per_unit' => $usdResult, 
                'total_value' => $usdResult,
                'exchange_rate' => $request->exchange_rate, // Simpan rate untuk histori
                'notes' => 'Hasil Konversi dari IDR ' . number_format($request->amount_idr),
            ]);

            // 4. Update Saldo Akun (Helper function kamu sebelumnya)
            // (Pastikan kamu memanggil logic update saldo di sini atau pakai Observer)
            $fromAccount = Account::find($request->from_account_id);
            $fromAccount->decrement('balance', $request->amount_idr);

            $toAccount = Account::find($request->to_account_id);
            $toAccount->increment('balance', $usdResult);
        });

        return redirect()->route('foreign-accounts.index')
        ->with('success', 'Konversi Berhasil! Saldo USD telah bertambah.');
    }
}