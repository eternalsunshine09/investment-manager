<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Watchlist;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    public function index()
    {
        $watchlists = Watchlist::where('user_id', Auth::id())->get();
        return view('watchlist.index', compact('watchlists'));
    }

    public function store(Request $request)
    {
        Watchlist::create([
            'user_id' => Auth::id(),
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'target_price' => $request->target_price,
            'current_price' => $request->current_price,
            'note' => $request->note
        ]);

        return back()->with('success', 'Aset masuk pantauan!');
    }

    public function destroy($id)
    {
        Watchlist::where('user_id', Auth::id())->where('id', $id)->delete();
        return back()->with('success', 'Aset dihapus dari pantauan.');
    }
}