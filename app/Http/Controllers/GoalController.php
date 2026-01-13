<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'target_amount' => 'required|numeric',
        ]);

        $goal = Goal::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'target_amount' => $request->target_amount,
        ]);

        if ($request->has('product_ids')) {
            $goal->products()->sync($request->product_ids);
        }

        return back()->with('success', 'Target baru berhasil dibuat! 🎯');
    }

    // --- PERBAIKAN DI SINI ---
    public function update(Request $request, $id)
    {
        // 1. Cari Goal milik user yang sedang login
        $goal = Goal::where('user_id', Auth::id())->findOrFail($id);

        // 2. Validasi input (Sama seperti store)
        $request->validate([
            'name' => 'required',
            'target_amount' => 'required|numeric',
        ]);

        // 3. Update data utama (Nama & Nominal)
        $goal->update([
            'name' => $request->name,
            'target_amount' => $request->target_amount,
        ]);

        // 4. Update Hubungan Aset (Produk)
        // Jika ada produk yang dicentang, sinkronkan.
        // Jika tidak ada yang dicentang, hapus semua hubungan (detach).
        if ($request->has('product_ids')) {
            $goal->products()->sync($request->product_ids);
        } else {
            $goal->products()->detach();
        }

        return back()->with('success', 'Target berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($id);
        
        // Hapus relasi pivot (otomatis aman jika database cascade, tapi ini buat jaga-jaga)
        $goal->products()->detach();
        
        $goal->delete();

        return back()->with('success', 'Target berhasil dihapus! 🗑️');
    }
}