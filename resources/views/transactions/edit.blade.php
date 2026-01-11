@extends('layouts.app')

@section('title', 'Edit Transaksi - Porto Tracking')

@section('content')
<div class="h-full overflow-y-auto pt-20 md:pt-8 pb-10 px-4">
    <div class="max-w-2xl mx-auto">

        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('transactions.index') }}"
                class="bg-white p-3 rounded-xl border border-slate-200 text-slate-500 hover:text-indigo-600 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Edit Transaksi</h1>
                <p class="text-slate-500 text-sm">Perubahan akan mengoreksi saldo RDN otomatis.</p>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100"
            x-data="{ type: '{{ $transaction->type }}' }">
            <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" class="space-y-5">
                @csrf @method('PUT')

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Jenis</label>
                        <select name="type" x-model="type"
                            class="w-full border border-slate-200 p-3 rounded-xl bg-slate-50 font-bold outline-none">
                            <option value="beli">Beli</option>
                            <option value="jual">Jual</option>
                            <option value="topup">Top Up</option>
                            <option value="tarik">Tarik Dana</option>
                            <option value="dividen_cash">Dividen</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Tanggal</label>
                        <input type="date" name="transaction_date" value="{{ $transaction->transaction_date }}"
                            class="w-full border border-slate-200 p-3 rounded-xl outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Akun RDN</label>
                    <select name="account_id" class="w-full border border-slate-200 p-3 rounded-xl outline-none">
                        @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ $transaction->account_id == $acc->id ? 'selected' : '' }}>
                            {{ $acc->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="['beli', 'jual', 'dividen_cash'].includes(type)">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Produk</label>
                    <select name="product_id" class="w-full border border-slate-200 p-3 rounded-xl outline-none">
                        @foreach($products as $product)
                        <option value="{{ $product->id }}"
                            {{ $transaction->product_id == $product->id ? 'selected' : '' }}>
                            {{ $product->code }} - {{ $product->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="['beli', 'jual'].includes(type)"
                    class="bg-slate-50 p-5 rounded-2xl border border-slate-200 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jumlah Unit</label>
                            <input type="number" step="any" name="amount" value="{{ $transaction->amount + 0 }}"
                                class="w-full border border-slate-200 p-3 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Harga / Unit</label>
                            <input type="number" step="any" name="price_per_unit"
                                value="{{ $transaction->price_per_unit + 0 }}"
                                class="w-full border border-slate-200 p-3 rounded-xl outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Fee (Biaya)</label>
                        <input type="number" name="fee" value="{{ $transaction->fee + 0 }}"
                            class="w-full border border-slate-200 p-3 rounded-xl outline-none">
                    </div>
                </div>

                <div x-show="['topup', 'tarik', 'dividen_cash'].includes(type)">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Total Nominal (Rp)</label>
                    <input type="number" name="total_value" value="{{ $transaction->total_value + 0 }}"
                        class="w-full border border-slate-200 p-3 rounded-xl font-bold text-slate-800 outline-none">
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white font-bold py-4 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition transform hover:-translate-y-1">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection