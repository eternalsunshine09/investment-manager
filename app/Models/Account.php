<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'bank_name',
        'balance',
        'account_type',
        'account_number',
        'initial_balance',
        'description',
        'is_active'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'initial_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function cashFlows(): HasMany
    {
        return $this->hasMany(CashFlow::class);
    }

    // --- FITUR UTAMA: Sinkronisasi Saldo Otomatis ---
    public function updateBalance(): void
    {
        // 1. Hitung Total dari CashFlow (Pemasukan/Pengeluaran Manual)
        $income = (float) $this->cashFlows()->where('type', 'income')->sum('amount');
        $expense = (float) $this->cashFlows()->where('type', 'expense')->sum('amount');

        // 2. Hitung Total dari Transaksi Investasi (Deposit/Withdrawal RDN)
        // Asumsi: table transactions punya kolom 'type' (deposit/withdrawal)
        $deposit = (float) $this->transactions()->where('type', 'deposit')->sum('amount');
        $withdrawal = (float) $this->transactions()->where('type', 'withdrawal')->sum('amount');
        
        // 3. (Opsional) Jika ada logic 'Buy' saham mengurangi cash, tambahkan di sini.
        // Misal: $buyStocks = $this->transactions()->where('type', 'buy')->sum('amount');
        $buyStocks = 0; 

        // 4. Hitung Saldo Akhir
        $initial = (float) ($this->initial_balance ?? 0);

        $this->balance = $initial 
                        + $income 
                        - $expense 
                        + $deposit 
                        - $withdrawal
                        - $buyStocks;

        // 5. Simpan tanpa memicu event observer (mencegah looping)
        if (method_exists($this, 'saveQuietly')) {
            $this->saveQuietly();
        } else {
            $this->save();
        }
    }
}