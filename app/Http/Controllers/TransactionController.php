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
    public function index(Request $request)
    {
        $query = Transaction::with(['product', 'account'])
                    ->where('user_id', Auth::id());

        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('transaction_date', $request->date);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(15);
        $products = Product::where('user_id', Auth::id())->get();
        $accounts = Account::where('user_id', Auth::id())->get();

        return view('transactions.index', compact('transactions', 'products', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'transaction_date' => 'required|date',
            'account_id' => 'required',
            'fee' => 'nullable|numeric|min:0', // Validasi Fee
            'amount' => 'nullable|numeric',
            'price' => 'nullable|numeric'
        ]);

        DB::transaction(function() use ($request) {
            $data = $this->calculateTransactionValues($request);

            Transaction::create(array_merge($data, [
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'account_id' => $request->account_id,
                'transaction_date' => $request->transaction_date,
                'type' => $request->type,
                'notes' => $request->notes
            ]));

            // Update Saldo
            $this->updateAccountBalance($request->account_id, $request->type, $data['total_value'], 'add');
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dicatat!');
    }

    public function edit($id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);
        $accounts = Account::where('user_id', Auth::id())->get();
        $products = Product::where('user_id', Auth::id())->get();
        
        return view('transactions.edit', compact('transaction', 'accounts', 'products'));
    }

    public function update(Request $request, $id)
    {
        $trx = Transaction::where('user_id', Auth::id())->findOrFail($id);

        DB::transaction(function() use ($request, $trx) {
            // A. Revert Saldo Lama
            $this->updateAccountBalance($trx->account_id, $trx->type, $trx->total_value, 'revert');

            // B. Hitung Data Baru
            $data = $this->calculateTransactionValues($request);

            // C. Update Database
            $trx->update(array_merge($data, [
                'account_id' => $request->account_id,
                'product_id' => $request->product_id,
                'transaction_date' => $request->transaction_date,
                'type' => $request->type,
                'notes' => $request->notes
            ]));

            // D. Terapkan Saldo Baru
            $this->updateAccountBalance($request->account_id, $request->type, $data['total_value'], 'add');
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $trx = Transaction::where('user_id', Auth::id())->findOrFail($id);

        DB::transaction(function() use ($trx) {
            $this->updateAccountBalance($trx->account_id, $trx->type, $trx->total_value, 'revert');
            $trx->delete();
        });

        return back()->with('success', 'Transaksi dihapus & saldo dikembalikan!');
    }

    // --- HELPER FUNCTIONS ---

    private function calculateTransactionValues($request) {
        $type = $request->type;
        // Gunakan floatval untuk memastikan angka desimal terhitung benar
        $amount = floatval($request->amount ?? 0);
        $price = floatval($request->price ?? 0); 
        $fee = floatval($request->fee ?? 0);
        
        $totalValue = 0;

        if (in_array($type, ['beli', 'right_issue'])) {
            // Beli = (Jml x Harga) DITAMBAH Fee
            $totalValue = ($amount * $price) + $fee; 
        } elseif ($type == 'jual') {
            // Jual = (Jml x Harga) DIKURANGI Fee
            $totalValue = ($amount * $price) - $fee; 
        } elseif (in_array($type, ['topup', 'tarik', 'dividen_cash'])) {
            $totalValue = $price; 
        }

        return [
            'amount' => $amount,
            'price_per_unit' => $price,
            'fee' => $fee,
            'total_value' => $totalValue
        ];
    }

    private function updateAccountBalance($accountId, $type, $amount, $operation)
    {
        $account = Account::find($accountId);
        if (!$account) return;

        $isMoneyIn = in_array($type, ['topup', 'jual', 'dividen_cash']);
        $isMoneyOut = in_array($type, ['tarik', 'beli', 'right_issue']);

        if ($operation == 'add') {
            if ($isMoneyIn) $account->increment('balance', $amount);
            if ($isMoneyOut) $account->decrement('balance', $amount);
        } elseif ($operation == 'revert') {
            if ($isMoneyIn) $account->decrement('balance', $amount);
            if ($isMoneyOut) $account->increment('balance', $amount);
        }
    }
}