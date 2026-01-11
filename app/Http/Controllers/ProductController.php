<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Tampilkan Daftar Produk
     */
    public function index()
    {
        // Ambil produk milik user yang sedang login, urutkan dari yang terbaru
        $products = Product::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('products.index', compact('products'));
    }

    /**
     * Simpan Produk Baru
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'category'      => 'required|string',
            'sub_category'  => 'nullable|string', // Boleh kosong jika bukan reksadana
            'code'          => 'required|string|max:10',
            'name'          => 'required|string|max:255',
        ]);

        // 2. Simpan ke Database
        Product::create([
            'user_id'       => Auth::id(),
            'category'      => $request->category,
            'sub_category'  => $request->category == 'reksadana' ? $request->sub_category : null,
            'code'          => strtoupper($request->code),
            'name'          => $request->name,
            'current_price' => 0 // Default harga 0 dulu
        ]);

        // 3. Kembali ke halaman index dengan pesan sukses
        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    /**
     * Update Produk yang Diedit
     */
    public function update(Request $request, $id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'category'      => 'required|string',
            'sub_category'  => 'nullable|string',
            'code'          => 'required|string|max:10',
            'name'          => 'required|string|max:255',
        ]);

        $product->update([
            'category'      => $request->category,
            // Jika kategori diubah jadi saham/crypto, sub_category harus dikosongkan
            'sub_category'  => $request->category == 'reksadana' ? $request->sub_category : null,
            'code'          => strtoupper($request->code),
            'name'          => $request->name,
        ]);

        return redirect()->route('products.index')->with('success', 'Data produk berhasil diperbarui!');
    }

    /**
     * Hapus Produk
     */
    public function destroy($id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);
        
        // Opsional: Cek dulu apakah produk sudah punya transaksi
        // if($product->transactions()->exists()) {
        //    return back()->with('error', 'Gagal hapus! Produk ini sudah memiliki riwayat transaksi.');
        // }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}