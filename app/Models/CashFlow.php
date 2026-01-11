<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    protected $fillable = ['user_id', 'type', 'category', 'amount', 'date', 'description'];
    protected $casts = ['date' => 'date'];
}