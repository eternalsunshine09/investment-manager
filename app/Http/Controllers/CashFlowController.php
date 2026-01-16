<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashFlow;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashFlowController extends Controller
{
    // Menyimpan Data Cashflow (Pemasukan/Pengeluaran/Transfer)
    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // --- LOGIKA TRANSFER ---
            if ($request->type == 'transfer') {
                $request->validate(['to_account_id' => 'required|different:account_id']);
                
                $fromAcc = Account::find($request->account_id);
                $toAcc = Account::find($request->to_account_id);

                // 1. Catat Pengeluaran
                CashFlow::create([
                    'user_id' => Auth::id(),
                    'account_id' => $fromAcc->id,
                    'type' => 'expense',
                    'amount' => $request->amount,
                    'date' => $request->date,
                    'category' => 'Transfer Keluar',
                    'description' => 'Transfer ke ' . $toAcc->name . '. ' . $request->description,
                ]);
                
                // 2. Catat Pemasukan
                CashFlow::create([
                    'user_id' => Auth::id(),
                    'account_id' => $toAcc->id,
                    'type' => 'income',
                    'amount' => $request->amount,
                    'date' => $request->date,
                    'category' => 'Transfer Masuk',
                    'description' => 'Transfer dari ' . $fromAcc->name . '. ' . $request->description,
                ]);

                // 3. Update Saldo via Model (PENTING)
                $fromAcc->updateBalance();
                $toAcc->updateBalance();

            } else {
                // --- LOGIKA BIASA (Income/Expense) ---
                CashFlow::create([
                    'user_id' => Auth::id(),
                    'account_id' => $request->account_id,
                    'type' => $request->type,
                    'category' => $request->category,
                    'amount' => $request->amount,
                    'date' => $request->date,
                    'description' => $request->description,
                ]);

                // Update Saldo via Model
                $account = Account::find($request->account_id);
                $account->updateBalance(); 
            }
        });

        return back()->with('success', 'Transaksi berhasil disimpan!');
    }

    // Update Data Cashflow
    public function update(Request $request, $id)
    {
        $flow = CashFlow::where('user_id', Auth::id())->findOrFail($id);
        $oldAccountId = $flow->account_id; // Simpan ID akun lama
        
        DB::transaction(function () use ($request, $flow, $oldAccountId) {
            
            // Update Data Flow
            $flow->update([
                'account_id' => $request->account_id,
                'type' => $request->type,
                'category' => $request->category,
                'amount' => $request->amount,
                'date' => $request->date,
                'description' => $request->description,
            ]);

            // Recalculate Account Lama (jika user memindahkan akun)
            if ($oldAccountId != $request->account_id) {
                $oldAcc = Account::find($oldAccountId);
                if($oldAcc) $oldAcc->updateBalance();
            }

            // Recalculate Account Baru/Saat ini
            $currentAcc = Account::find($request->account_id);
            if($currentAcc) $currentAcc->updateBalance();
        });

        return back()->with('success', 'Transaksi berhasil diperbarui!');
    }

    // Hapus Data
    public function destroy($id)
    {
        $flow = CashFlow::where('user_id', Auth::id())->findOrFail($id);
        $accountId = $flow->account_id;

        DB::transaction(function () use ($flow, $accountId) {
            $flow->delete();

            // Hitung ulang saldo setelah dihapus
            $account = Account::find($accountId);
            if ($account) {
                $account->updateBalance();
            }
        });

        return back()->with('success', 'Data dihapus & saldo dikembalikan.');
    }
}