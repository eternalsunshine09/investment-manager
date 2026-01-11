<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Product; // Tambahkan ini
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

    public function update(Request $request, Goal $goal)
    {
        // Validasi dan update goal
        $validated = $request->validate([
            // aturan validasi
        ]);

        $goal->update($validated);

        return redirect()->back()->with('success', 'Goal updated successfully.');
    }
    

    public function destroy($id)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($id);
        
        // Hapus relasi pivot dulu (opsional, biasanya otomatis jika cascade)
        $goal->products()->detach();
        
        $goal->delete();

        return back()->with('success', 'Target berhasil dihapus! 🗑️');
    }
}