<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    // Sesuaikan fillable dengan kolom baru di migration
    protected $fillable = [
        'user_id', 
        'account_id', 
        'type', 
        'category', 
        'currency', 
        'amount', 
        'date', 
        'note'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Relasi ke Akun
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}