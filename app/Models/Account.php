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
        'is_active',
        'currency' // <--- WAJIB DITAMBAHKAN DISINI
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'initial_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    // ... sisa method relasi dan updateBalance biarkan sama ...
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function transactions(): HasMany { return $this->hasMany(Transaction::class); }
    public function cashFlows(): HasMany { return $this->hasMany(CashFlow::class); }
    
    public function updateBalance(): void
    {
        // ... kode updateBalance yang sudah ada sebelumnya ...
        $income = (float) $this->cashFlows()->where('type', 'income')->sum('amount');
        $expense = (float) $this->cashFlows()->where('type', 'expense')->sum('amount');
        $deposit = (float) $this->transactions()->where('type', 'deposit')->sum('amount');
        $withdrawal = (float) $this->transactions()->where('type', 'withdrawal')->sum('amount');
        $topup = (float) $this->transactions()->where('type', 'topup')->sum('amount'); // Handle Topup Valas

        $initial = (float) ($this->initial_balance ?? 0);

        $this->balance = $initial + $income - $expense + $deposit - $withdrawal + $topup;

        if (method_exists($this, 'saveQuietly')) {
            $this->saveQuietly();
        } else {
            $this->save();
        }
    }

    /**
     * Menghitung Harga Rata-Rata Pembelian (Average Rate)
     */
    public function getAverageRateAttribute()
    {
        // 1. Ambil transaksi masuk (Topup/Buy/Deposit) yang punya rate
        $transactions = $this->transactions()
            ->whereIn('type', ['topup', 'buy', 'deposit']) // Sesuaikan dengan tipe transaksi masukmu
            ->where('exchange_rate', '>', 0) // Hanya yang punya kurs
            ->get();

        // 2. Jika tidak ada data, return 0
        if ($transactions->isEmpty()) {
            return 0;
        }

        // 3. Hitung Total Modal (Rupiah) & Total Unit (Valas)
        $totalModalIdr = 0;
        $totalUnitValas = 0;

        foreach ($transactions as $trx) {
            // Modal = Jumlah Valas * Kurs saat itu
            $totalModalIdr += ($trx->amount * $trx->exchange_rate);
            $totalUnitValas += $trx->amount;
        }

        // 4. Hindari pembagian dengan nol
        if ($totalUnitValas <= 0) return 0;

        // 5. Return Rata-rata
        return $totalModalIdr / $totalUnitValas;
    }
}