<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashFlow;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashFlowController extends Controller
{
    /**
     * Menampilkan halaman daftar Cashflow.
     */
    public function index()
    {
        $userId = Auth::id();

        // Ambil data cashflow urut dari yang terbaru
        $flows = CashFlow::with('account')
                    ->where('user_id', $userId)
                    ->latest('date')
                    ->get();
        
        // Ambil daftar akun untuk dropdown di modal tambah manual
        $accounts = Account::where('user_id', $userId)->get();

        // Hitung total pemasukan dan pengeluaran
        $income = $flows->where('type', 'income')->sum('amount');
        $expense = $flows->where('type', 'expense')->sum('amount');
        
        // Hitung Savings Rate (Persentase uang yang disimpan)
        $savingsRate = $income > 0 ? (($income - $expense) / $income * 100) : 0;
        
        return view('cashflow.index', compact('flows', 'income', 'expense', 'savingsRate', 'accounts'));
    }

    /**
     * Menyimpan data dari Input Manual (Form).
     */
    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount'     => 'required|numeric|min:1',
            'type'       => 'required|in:income,expense',
            'date'       => 'required|date',
            'category'   => 'required|string|max:255',
            'description'=> 'nullable|string|max:255', // Input manual pakai nama 'description'
        ]);

        DB::transaction(function () use ($request) {
            // 1. Simpan ke database
            CashFlow::create([
                'user_id'    => Auth::id(),
                'account_id' => $request->account_id,
                'type'       => $request->type,
                'category'   => $request->category,
                'currency'   => 'IDR', // Default IDR untuk input manual
                'amount'     => $request->amount,
                'date'       => $request->date,
                'note'       => $request->description // Simpan ke kolom 'note'
            ]);

            // 2. Update Saldo Akun
            $account = Account::find($request->account_id);
            if ($request->type == 'income') {
                $account->increment('balance', $request->amount);
            } else {
                $account->decrement('balance', $request->amount);
            }
        });

        return back()->with('success', 'Transaksi berhasil dicatat & saldo diperbarui!');
    }

    /**
     * Memproses Import file CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048', // Maks 2MB
        ]);

        $file = $request->file('file');
        
        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            // Lewati baris pertama (Header Judul Kolom)
            fgetcsv($handle, 1000, ';'); 

            DB::beginTransaction();
            try {
                while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                    // --- MAPPING DATA DARI CSV ---
                    // Sesuaikan index array dengan urutan kolom di file CSV kamu
                    $accountName = $row[0]; // account
                    $category    = $row[1]; // category
                    $currency    = $row[2]; // currency
                    $amountRaw   = $row[3]; // amount (misal: 40000,00)
                    $typeRaw     = $row[5]; // type (Income/Expenses)
                    $note        = ($row[8] == 'nan') ? '' : $row[8]; // note
                    $dateRaw     = $row[9]; // date

                    // 1. Bersihkan Format Angka (Hapus titik ribuan, ganti koma desimal)
                    $amountClean = str_replace('.', '', $amountRaw); 
                    $amountClean = str_replace(',', '.', $amountClean);
                    $amount      = abs((float)$amountClean);

                    // 2. Normalisasi Tipe (CSV: Income/Expenses -> DB: income/expense)
                    $type = ($typeRaw == 'Income') ? 'income' : 'expense';

                    // 3. Cari ID Akun berdasarkan Nama
                    $account = Account::where('user_id', Auth::id())
                                ->where('name', $accountName)
                                ->first();

                    // Jika akun tidak ditemukan di database, lewati baris ini
                    if (!$account) continue; 

                    // 4. Simpan Transaksi
                    CashFlow::create([
                        'user_id'    => Auth::id(),
                        'account_id' => $account->id,
                        'type'       => $type,
                        'category'   => $category, 
                        'currency'   => $currency,
                        'amount'     => $amount,
                        'date'       => date('Y-m-d', strtotime($dateRaw)),
                        'note'       => $note
                    ]);

                    // 5. Update Saldo Akun Otomatis
                    if ($type == 'income') {
                        $account->increment('balance', $amount);
                    } else {
                        $account->decrement('balance', $amount);
                    }
                }
                
                DB::commit();
                return back()->with('success', 'Import CSV berhasil! Data masuk & saldo diperbarui.');
                
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Gagal impor: ' . $e->getMessage());
            } finally {
                fclose($handle);
            }
        }
        
        return back()->with('error', 'File tidak dapat dibaca.');
    }

    /**
     * Menghapus data Cashflow.
     */
    public function destroy($id)
    {
        $flow = CashFlow::where('user_id', Auth::id())->findOrFail($id);

        DB::transaction(function () use ($flow) {
            // Kembalikan Saldo sebelum menghapus data (Revert)
            $account = Account::find($flow->account_id);
            
            if ($account) {
                // Jika yang dihapus adalah Pemasukan, saldo harus dikurangi kembali
                if ($flow->type == 'income') {
                    $account->decrement('balance', $flow->amount);
                } 
                // Jika yang dihapus adalah Pengeluaran, uangnya "balik" ke saldo
                else {
                    $account->increment('balance', $flow->amount);
                }
            }
            
            $flow->delete();
        });

        return back()->with('success', 'Data dihapus & saldo dikembalikan.');
    }
}