<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashFlow;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashFlowController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Ambil Data Cashflow
        $flows = CashFlow::with('account')
                    ->where('user_id', $userId)
                    ->latest()
                    ->get();
        
        // Ambil Data Akun
        $accounts = Account::where('user_id', $userId)->get();

        $income = $flows->where('type', 'income')->sum('amount');
        $expense = $flows->where('type', 'expense')->sum('amount');
        $savingsRate = $income > 0 ? (($income - $expense) / $income * 100) : 0;
        
        return view('cashflow.index', compact('flows', 'income', 'expense', 'savingsRate', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:income,expense',
            'date' => 'required|date',
            'category' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Simpan Transaksi Cashflow
            CashFlow::create([
                'user_id' => Auth::id(),
                'account_id' => $request->account_id,
                'type' => $request->type,
                'category' => $request->category,
                'amount' => $request->amount,
                'date' => $request->date,
                'description' => $request->description
            ]);

            // 2. Update Saldo Akun Otomatis
            $account = Account::find($request->account_id);
            if ($request->type == 'income') {
                $account->increment('balance', $request->amount);
            } else {
                $account->decrement('balance', $request->amount);
            }
        });

        return back()->with('success', 'Data berhasil dicatat & saldo diperbarui!');
    }

    // Fungsi Baru untuk Import CSV
    // Fungsi Import CSV
    public function import(Request $request)
    {
        // Validasi file: Harus CSV dan maksimal 2MB (2048 KB)
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');

        // Buka file dengan mode Read ('r')
        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            // Lewati baris pertama (Header)
            fgetcsv($handle, 1000, ';');

            DB::beginTransaction();
            try {
                // Baca baris per baris
                while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                    // Mapping sesuai urutan kolom di CSV kamu
                    $accountName = $row[0];  // account
                    $category    = $row[1];  // category
                    $amountRaw   = $row[3];  // amount (format: 40000,00)
                    $typeRaw     = $row[5];  // type (Income/Expenses)
                    $note        = ($row[8] == 'nan') ? '' : $row[8]; // note
                    $dateRaw     = $row[9];  // date

                    // 1. Bersihkan Format Angka (Rp)
                    // Hapus titik ribuan (jika ada), ubah koma desimal jadi titik
                    $amountClean = str_replace('.', '', $amountRaw);
                    $amountClean = str_replace(',', '.', $amountClean);
                    $amount      = abs((float)$amountClean);

                    // 2. Normalisasi Tipe Transaksi
                    // CSV pakai 'Income'/'Expenses', Database pakai 'income'/'expense'
                    $type = ($typeRaw == 'Income') ? 'income' : 'expense';

                    // 3. Cari ID Akun
                    $account = Account::where('user_id', Auth::id())
                                ->where('name', $accountName)
                                ->first();

                    // Jika akun tidak ada di database, lewati baris ini
                    if (!$account) continue;

                    // 4. Simpan ke Database
                    CashFlow::create([
                        'user_id'     => Auth::id(),
                        'account_id'  => $account->id,
                        'type'        => $type,
                        'category'    => $category,
                        'amount'      => $amount,
                        'date'        => date('Y-m-d', strtotime($dateRaw)),
                        'description' => $note,
                    ]);

                    // 5. Update Saldo Akun Otomatis
                    if ($type == 'income') {
                        $account->increment('balance', $amount);
                    } else {
                        $account->decrement('balance', $amount);
                    }
                }

                DB::commit();
                return back()->with('success', 'Import berhasil! Data cashflow bulan ini sudah masuk.');

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Gagal import: ' . $e->getMessage());
            } finally {
                fclose($handle);
            }
        }

        return back()->with('error', 'File tidak bisa dibaca.');
    }

    public function destroy($id)
    {
        $flow = CashFlow::where('user_id', Auth::id())->findOrFail($id);

        DB::transaction(function () use ($flow) {
            // Kembalikan Saldo sebelum hapus (Revert)
            $account = Account::find($flow->account_id);
            if ($account) {
                if ($flow->type == 'income') {
                    $account->decrement('balance', $flow->amount);
                } else {
                    $account->increment('balance', $flow->amount);
                }
            }
            $flow->delete();
        });

        return back()->with('success', 'Data dihapus & saldo dikembalikan.');
    }
}