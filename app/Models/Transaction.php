<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // PASTIKAN 'fee' ADA DI SINI
    protected $fillable = [
        'user_id',
        'product_id',
        'account_id',
        'transaction_date',
        'type',
        'amount',           // Jumlah Unit
        'price_per_unit',   // Harga Satuan
        'fee',              // <--- WAJIB DITAMBAHKAN
        'total_value',      // Total Bersih
        'notes'
    ];

    // Relasi (Biarkan seperti semula)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}