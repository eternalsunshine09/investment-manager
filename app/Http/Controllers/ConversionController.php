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
        $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id'   => 'required|exists:accounts,id|different:from_account_id',
            'amount_idr'      => 'required|numeric|min:1',
            'exchange_rate'   => 'required|numeric|min:1',
        ]);

        // Hitung jumlah USD yang didapat
        $amountUsd = $request->amount_idr / $request->exchange_rate;

        DB::transaction(function () use ($request, $amountUsd) {
            $userId = Auth::id();
            $date   = now(); // Atau ambil dari request jika ada input tanggal

            // --- A. SISI RUPIAH (Uang Keluar) ---
            // Catat di CashFlow sebagai Pengeluaran
            CashFlow::create([
                'user_id'     => $userId,
                'account_id'  => $request->from_account_id,
                'type'        => 'expense', // Uang keluar
                'category'    => 'Konversi Valas',
                'amount'      => $request->amount_idr,
                'date'        => $date,
                'description' => 'Beli Valas ke Akun ID #' . $request->to_account_id . ' (Rate: ' . number_format($request->exchange_rate) . ')',
            ]);

            // Update Saldo Akun Rupiah
            $fromAcc = Account::find($request->from_account_id);
            $fromAcc->updateBalance(); // Pastikan pakai method updateBalance() yang sudah kita buat sebelumnya

            // --- B. SISI VALAS (Uang Masuk / Aset Bertambah) ---
            // Catat di Transaction (Investasi) sebagai Topup
            Transaction::create([
                'user_id'          => $userId,
                'account_id'       => $request->to_account_id,
                'type'             => 'topup', // Tipe Topup menambah aset
                'amount'           => $amountUsd, // Masukkan nominal USD-nya
                'price'            => $request->exchange_rate, // Harga beli per 1 USD
                'quantity'         => $amountUsd, // Anggap 1 USD = 1 unit
                'transaction_date' => $date,
                'notes'            => 'Topup dari IDR (Total Rp ' . number_format($request->amount_idr) . ')',
            ]);

            // Update Saldo Akun Valas
            $toAcc = Account::find($request->to_account_id);
            $toAcc->updateBalance();
        });

        return redirect()->route('foreign-accounts.index')->with('success', 'Konversi berhasil! Saldo Rupiah terpotong & Valas bertambah.');
    }
}