<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    protected $fillable = [
    'user_id', 'account_id', 'type', 'category', 'amount', 'date', 'description'
];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}