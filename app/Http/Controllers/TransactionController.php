<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    // 1. TAMPILKAN HALAMAN TRANSAKSI
    public function index(Request $request)
    {
        // 1. Mulai Query Dasar (Ambil transaksi user ini)
        // Kita gunakan 'with' agar query lebih efisien (Eager Loading)
        $query = Transaction::with(['product', 'account'])
                    ->where('user_id', Auth::id());

        // 2. Filter Berdasarkan Kategori Produk (Saham/Reksadana/dll)
        // Karena kategori ada di tabel 'products', kita pakai whereHas
        if ($request->has('category') && $request->category != '') {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        // 3. Filter Berdasarkan Jenis Transaksi (Beli/Jual/Dividen)
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // 4. Filter Berdasarkan Tanggal
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('transaction_date', $request->date);
        }

        // 5. Eksekusi Query
        $transactions = $query->orderBy('transaction_date', 'desc')->get();

        // Data untuk dropdown filter (agar tidak hardcode)
        $products = Product::where('user_id', Auth::id())->get();
        $accounts = Account::where('user_id', Auth::id())->get();

        return view('transactions.index', compact('transactions', 'products', 'accounts'));
    }

    // 2. SIMPAN TRANSAKSI BARU
    public function store(Request $request)
    {
        // Validasi simpel
        $request->validate([
            'type' => 'required',
            'transaction_date' => 'required|date',
        ]);

        $type = $request->type;
        $totalValue = 0;
        $amount = $request->amount ?? 0; // Jumlah Unit
        $price = $request->price ?? 0;   // Harga/Nominal
        $fee = $request->fee ?? 0;

        // --- LOGIKA HITUNG TOTAL VALUE (Uang yang keluar/masuk) ---
        
        if ($type == 'beli' || $type == 'right_issue') {
            // Uang Keluar: (Unit * Harga) + Fee
            $totalValue = ($amount * $price) + $fee;
        } 
        elseif ($type == 'jual') {
            // Uang Masuk: (Unit * Harga) - Fee
            $totalValue = ($amount * $price) - $fee;
        } 
        elseif ($type == 'topup' || $type == 'dividen_cash') {
            // Uang Masuk langsung
            $totalValue = $price; 
            $amount = 0; // Tidak ada unit saham yang berubah
        } 
        elseif ($type == 'tarik') {
            // Uang Keluar langsung
            $totalValue = $price;
            $amount = 0;
        }
        elseif ($type == 'dividen_unit' || $type == 'stock_split') {
            // Unit Nambah, Tidak ada uang berubah (Harga dianggap 0 untuk Average Down otomatis)
            $totalValue = 0;
            $price = 0; // Harga perolehan 0
        }
        elseif ($type == 'reverse_split') {
            // Unit Berkurang, Tidak ada uang berubah
            $totalValue = 0;
            $price = 0;
            // Penting: Unit harus negatif agar mengurangi saldo di Portfolio
            // Tapi user input positif di form, jadi kita negatifkan di sini?
            // Nanti di PortfolioController logic-nya: Total Beli - Total Jual.
            // Reverse Split lebih baik dianggap "Jual dengan harga 0" atau kita simpan sebagai tipe khusus.
            // Oja sarankan simpan sebagai 'jual' tapi harga 0 agar unit berkurang.
            // ATAU biarkan type 'reverse_split' tapi di PortfolioController nanti unitnya dikurang.
            // Kita pakai opsi type 'reverse_split' saja biar datanya jelas.
        }

        // --- SIMPAN TRANSAKSI ---
        $trx = Transaction::create([
            'user_id' => Auth::id(),
            'account_id' => $request->account_id, // Bisa null kalau stock split
            'product_id' => $request->product_id, // Bisa null kalau topup/tarik
            'transaction_date' => $request->transaction_date,
            'type' => $type,
            'amount' => $amount,       // Unit
            'price_per_unit' => $price, // Harga
            'fee' => $fee,
            'total_value' => $totalValue, // Total Uang
            'notes' => $request->notes
        ]);

        // --- UPDATE SALDO AKUN (RDN) ---
        if ($request->account_id) {
            $account = \App\Models\Account::find($request->account_id);
            if ($account) {
                // Transaksi yang MENAMBAH saldo RDN
                if (in_array($type, ['topup', 'jual', 'dividen_cash'])) {
                    $account->balance += $totalValue;
                } 
                // Transaksi yang MENGURANGI saldo RDN
                elseif (in_array($type, ['tarik', 'beli', 'right_issue'])) {
                    $account->balance -= $totalValue;
                }
                $account->save();
            }
        }

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dicatat!');
    }

    // 3. HALAMAN EDIT
    public function edit($id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);
        $accounts = Account::where('user_id', Auth::id())->get();
        $products = Product::where('user_id', Auth::id())->get();
        
        return view('transactions.edit', compact('transaction', 'accounts', 'products'));
    }

    // 4. UPDATE TRANSAKSI (Logika Perbaikan Saldo)
    public function update(Request $request, $id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);
        
        // Ambil Akun Lama & Akun Baru (Bisa saja akunnya diganti saat edit)
        $oldAccount = Account::find($transaction->account_id);
        $newAccount = Account::find($request->account_id);

        // A. KEMBALIKAN SALDO KE AKUN LAMA
        if (in_array($transaction->type, ['beli', 'tarik'])) {
            $oldAccount->increment('balance', $transaction->total_value);
        } else {
            $oldAccount->decrement('balance', $transaction->total_value);
        }

        // B. HITUNG NILAI TRANSAKSI BARU
        $amount = $request->amount ?? 0;
        $price = $request->price_per_unit ?? 0;
        $fee = $request->fee ?? 0;
        $totalValue = 0;

        if ($request->type == 'beli') {
            $totalValue = ($amount * $price) + $fee;
        } elseif ($request->type == 'jual') {
            $totalValue = ($amount * $price) - $fee;
        } else {
            $totalValue = $request->total_value ?? 0;
        }

        // C. POTONG/TAMBAH SALDO PADA AKUN BARU
        if (in_array($request->type, ['beli', 'tarik'])) {
            if ($newAccount->balance < $totalValue) {
                // Jika saldo tidak cukup, batalkan semua dan kembalikan ke awal
                // (Opsi: kamu bisa handle revert balik disini jika perlu)
                return back()->with('error', 'Saldo pada akun baru tidak cukup!');
            }
            $newAccount->decrement('balance', $totalValue);
        } else {
            $newAccount->increment('balance', $totalValue);
        }

        // D. SIMPAN PERUBAHAN
        $transaction->update([
            'account_id' => $request->account_id,
            'product_id' => $request->product_id,
            'type' => $request->type,
            'transaction_date' => $request->transaction_date,
            'amount' => $amount,
            'price_per_unit' => $price,
            'fee' => $fee,
            'total_value' => $totalValue,
            'notes' => $request->notes
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaksi diperbarui!');
    }

    // 5. HAPUS TRANSAKSI
    public function destroy($id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);
        $account = Account::find($transaction->account_id);

        // Kembalikan saldo
        if (in_array($transaction->type, ['beli', 'tarik'])) {
            $account->increment('balance', $transaction->total_value);
        } else {
            $account->decrement('balance', $transaction->total_value);
        }

        $transaction->delete();
        return back()->with('success', 'Transaksi dihapus & saldo dikembalikan!');
    }
}