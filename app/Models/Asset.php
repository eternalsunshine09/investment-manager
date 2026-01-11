<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    // DAFTAR KOLOM YANG BOLEH DIISI (Mass Assignment)
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'amount',
        'buy_price',
        'buy_date',
    ];
}