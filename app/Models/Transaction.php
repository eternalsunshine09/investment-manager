<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'product_id', 
        'account_id', 
        'transaction_date', 
        'type', 
        'amount', 
        'price_per_unit', 
        'total_value',
        'exchange_rate', 
        'notes' 
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // PASTIKAN FUNGSI INI ADA
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}