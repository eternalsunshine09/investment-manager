<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = ['user_id', 'name', 'target_amount'];

    // Relasi ke Produk (Agar target bisa dihitung per produk spesifik)
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}