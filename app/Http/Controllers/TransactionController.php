<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // 1. TAMPILKAN HALAMAN TRANSAKSI
    public function index(Request $request)
    {
        $query = Transaction::with(['product', 'account'])
                    ->where('user_id', Auth::id());

        // Filter Kategori
        if ($request->has('category') && $request->category != '') {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        // Filter Tipe
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Filter Tanggal
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('transaction_date', $request->date);
        }

        // Filter Akun (Untuk Fitur Riwayat per Akun)
        if ($request->has('account_id') && $request->account_id != '') {
            $query->where('account_id', $request->account_id);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(10);
        
        // Data untuk dropdown
        $products = Product::where('user_id', Auth::id())->get();
        $accounts = Account::where('user_id', Auth::id())->get();

        return view('transactions.index', compact('transactions', 'products', 'accounts'));
    }

    // 2. SIMPAN TRANSAKSI BARU
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'transaction_date' => 'required|date',
            'account_id' => 'required'
        ]);

        DB::transaction(function() use ($request) {
            $type = $request->type;
            $amount = $request->amount ?? 0;
            $price = $request->price ?? 0; // Bisa harga/unit atau nominal total
            $fee = $request->fee ?? 0;
            
            // --- LOGIKA HITUNG TOTAL VALUE (Uang Riil yang mempengaruh RDN) ---
            $totalValue = 0;

            // 1. Kelompok Uang Keluar (Mengurangi RDN)
            if (in_array($type, ['beli', 'right_issue'])) {
                $totalValue = ($amount * $price) + $fee; 
            } 
            // 2. Kelompok Uang Masuk (Menambah RDN)
            elseif ($type == 'jual') {
                $totalValue = ($amount * $price) - $fee; 
            } 
            // 3. Kelompok Nominal Langsung (Topup/Tarik/Div Tunai)
            elseif (in_array($type, ['topup', 'tarik', 'dividen_cash'])) {
                $totalValue = $price; // Di input form, 'price' kita anggap nominal total
            }
            // 4. Kelompok Non-Tunai (Hanya Ubah Unit, RDN Tetap)
            elseif (in_array($type, ['stock_split', 'reverse_split', 'dividen_unit'])) {
                $totalValue = 0; 
            }

            // --- SIMPAN DATA ---
            Transaction::create([
                'user_id' => Auth::id(),
                'account_id' => $request->account_id,
                'product_id' => $request->product_id, // Nullable
                'transaction_date' => $request->transaction_date,
                'type' => $type,
                'amount' => $amount,
                'price_per_unit' => $price,
                'fee' => $fee,
                'total_value' => $totalValue,
                'notes' => $request->notes
            ]);

            // --- UPDATE SALDO AKUN ---
            $this->updateAccountBalance($request->account_id, $type, $totalValue, 'add');
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dicatat!');
    }

    // 3. HALAMAN EDIT (INI YANG TADI HILANG/ERROR)
    public function edit($id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);
        $accounts = Account::where('user_id', Auth::id())->get();
        $products = Product::where('user_id', Auth::id())->get();
        
        return view('transactions.edit', compact('transaction', 'accounts', 'products'));
    }

    // 4. UPDATE TRANSAKSI
    public function update(Request $request, $id)
    {
        $trx = Transaction::where('user_id', Auth::id())->findOrFail($id);

        DB::transaction(function() use ($request, $trx) {
            // 1. Revert Saldo Lama (Batalkan efek transaksi lama)
            $this->updateAccountBalance($trx->account_id, $trx->type, $trx->total_value, 'revert');

            // 2. Hitung Ulang Data Baru
            $type = $request->type;
            $amount = $request->amount ?? 0;
            $price = $request->price_per_unit ?? ($request->price ?? 0); // Handle beda nama field di form edit/create
            $fee = $request->fee ?? 0;
            $totalValue = 0;

            // Logika hitung ulang sama dengan Store
            if (in_array($type, ['beli', 'right_issue'])) {
                $totalValue = ($amount * $price) + $fee;
            } elseif ($type == 'jual') {
                $totalValue = ($amount * $price) - $fee;
            } elseif (in_array($type, ['topup', 'tarik', 'dividen_cash'])) {
                // Di form edit view kamu pakai name="total_value" untuk topup/tarik, 
                // tapi logic di store pakai 'price'. Kita sesuaikan ambil dari request total_value jika ada.
                $totalValue = $request->total_value ?? $price; 
            } 
            
            // 3. Update Transaksi
            $trx->update([
                'account_id' => $request->account_id,
                'product_id' => $request->product_id,
                'type' => $type,
                'transaction_date' => $request->transaction_date,
                'amount' => $amount,
                'price_per_unit' => $price,
                'fee' => $fee,
                'total_value' => $totalValue,
            ]);

            // 4. Apply Saldo Baru
            $this->updateAccountBalance($request->account_id, $type, $totalValue, 'add');
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi diperbarui!');
    }

    // 5. HAPUS TRANSAKSI
    public function destroy($id)
    {
        $trx = Transaction::where('user_id', Auth::id())->findOrFail($id);

        DB::transaction(function() use ($trx) {
            // Kembalikan saldo sebelum hapus record
            $this->updateAccountBalance($trx->account_id, $trx->type, $trx->total_value, 'revert');
            $trx->delete();
        });

        return back()->with('success', 'Transaksi dihapus & saldo dikembalikan!');
    }

    // HELPER: Logic Update Saldo
    private function updateAccountBalance($accountId, $type, $amount, $operation)
    {
        $account = Account::find($accountId);
        if (!$account) return;

        // Tentukan apakah transaksi ini aslinya Menambah (+) atau Mengurangi (-) saldo
        $isMoneyIn = in_array($type, ['topup', 'jual', 'dividen_cash']);
        $isMoneyOut = in_array($type, ['tarik', 'beli', 'right_issue']);

        if ($operation == 'add') {
            // Transaksi Baru: Jalankan sesuai logika
            if ($isMoneyIn) $account->increment('balance', $amount);
            if ($isMoneyOut) $account->decrement('balance', $amount);
        } elseif ($operation == 'revert') {
            // Pembatalan: Lakukan kebalikannya
            if ($isMoneyIn) $account->decrement('balance', $amount);
            if ($isMoneyOut) $account->increment('balance', $amount);
        }
    }
}