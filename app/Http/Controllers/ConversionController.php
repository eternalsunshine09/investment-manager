<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\CashFlow;
use App\Models\Transaction; // Asumsi Valas menggunakan tabel transaksi investasi
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConversionController extends Controller
{
    // 1. Menampilkan Form Konversi
    public function index()
    {
        $userId = Auth::id();
        
        // Ambil akun Rupiah (Sumber Dana)
        $idrAccounts = Account::where('user_id', $userId)
                        ->where(function($q) {
                            $q->where('currency', 'IDR')
                              ->orWhereNull('currency'); // Handle jika kolom currency belum diisi
                        })->get();

        // Ambil akun Asing (Tujuan)
        $usdAccounts = Account::where('user_id', $userId)
                        ->where('currency', '!=', 'IDR')
                        ->get();

        return view('conversion.index', compact('idrAccounts', 'usdAccounts'));
    }

    // 2. Memproses Transaksi (Logic Utama)
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id'   => 'required|exists:accounts,id|different:from_account_id',
            'amount_idr'      => 'required|numeric|min:1',
            'exchange_rate'   => 'required|numeric|min:1',
        ]);

        $amountForeign = $request->amount_idr / $request->exchange_rate;

        DB::transaction(function () use ($request, $amountForeign) {
            $userId = Auth::id();
            $date   = now();
            
            $fromAcc = Account::find($request->from_account_id);
            $toAcc   = Account::find($request->to_account_id);

            // A. UANG KELUAR (IDR) -> CASHFLOW
            CashFlow::create([
                'user_id'     => $userId,
                'account_id'  => $fromAcc->id,
                'type'        => 'expense', 
                'category'    => 'Konversi Valas',
                'amount'      => $request->amount_idr,
                'date'        => $date,
                // Kita simpan info rate di deskripsi juga untuk kemudahan baca
                'description' => "Beli {$toAcc->currency} ({$toAcc->name}). Rate: " . number_format($request->exchange_rate),
            ]);
            $fromAcc->updateBalance();

            // B. UANG MASUK (VALAS) -> TRANSACTIONS
            Transaction::create([
                'user_id'          => $userId,
                'account_id'       => $toAcc->id,
                'type'             => 'topup', 
                'amount'           => $amountForeign, // Jumlah USD yang didapat
                'transaction_date' => $date,
                'exchange_rate'    => $request->exchange_rate, // <--- SIMPAN RATE DISINI
                'notes'            => "Topup dari IDR (Total Rp " . number_format($request->amount_idr) . ")",
            ]);
            $toAcc->updateBalance();
        });

        return redirect()->route('foreign-accounts.index')->with('success', 'Konversi berhasil disimpan!');
    }
}