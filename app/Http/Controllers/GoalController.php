<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Goal; // Import Model Goal
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'product_ids' => 'nullable|array', // Untuk menghubungkan produk
        ]);

        $goal = Goal::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'target_amount' => $request->target_amount,
        ]);

        // Jika ada produk yang dipilih, hubungkan lewat tabel pivot
        if ($request->has('product_ids')) {
            $goal->products()->attach($request->product_ids);
        }

        return back()->with('success', 'Target keuangan berhasil dibuat!');
    }

    public function update(Request $request, Goal $goal)
    {
        // 1. Pastikan hanya pemilik target yang bisa mengubah
        if ($goal->user_id !== Auth::id()) {
            return back()->with('error', 'Akses ditolak!');
        }

    // 2. Validasi input
    $request->validate([
        'name' => 'required|string|max:255',
        'target_amount' => 'required|numeric|min:1',
        'product_ids' => 'nullable|array',
    ]);

    // 3. Perbarui data dasar
    $goal->update([
        'name' => $request->name,
        'target_amount' => $request->target_amount,
    ]);

    // 4. Sinkronisasi produk investasi (Many-to-Many)
    // sync() akan otomatis menghapus yang lama dan menambah yang baru dipilih
    if ($request->has('product_ids')) {
        $goal->products()->sync($request->product_ids);
    } else {
        $goal->products()->detach(); // Lepas semua jika tidak ada yang dipilih
    }

        return back()->with('success', 'Target berhasil diperbarui!');
    }

    public function destroy(Goal $goal)
    {
        // Pastikan hanya pemilik yang bisa menghapus
        if ($goal->user_id === Auth::id()) {
            $goal->delete();
            return back()->with('success', 'Target berhasil dihapus!');
        }
        
        return back()->with('error', 'Akses ilegal!');
    }
}