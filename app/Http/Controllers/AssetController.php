<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset; // Jangan lupa panggil Model Asset
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'name' => 'required|max:50',
            'type' => 'required', // saham, crypto, emas, dll
            'amount' => 'required|numeric|min:0',
            'buy_price' => 'required|numeric|min:0',
            'buy_date' => 'required|date',
        ]);

        // 2. Simpan ke Database
        // Kita pakai cara 'Relasi' biar otomatis terhubung ke User yang login
        // Artinya: "User yang sedang login -> tolong buatkan aset baru"
        Asset::create([
            'user_id' => Auth::id(), // Ambil ID user yang sedang login
            'name' => $validated['name'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'buy_price' => $validated['buy_price'],
            'buy_date' => $validated['buy_date'],
        ]);

        // 3. Kembali ke Dashboard
        return redirect('/dashboard')->with('success', 'Aset berhasil ditambahkan!');
    }
}