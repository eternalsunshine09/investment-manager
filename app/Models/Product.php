<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Pastikan 'current_price' ada di sini
    protected $fillable = [
    'user_id', 
    'category', 
    'sub_category', // <--- Tambahkan ini
    'code', 
    'name', 
    'current_price'
];

    // Relasi ke transaksi
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}