@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
<div class="py-12 px-4">
    <div class="max-w-2xl mx-auto">

        <a href="{{ route('transactions.index') }}"
            class="inline-flex items-center gap-2 text-sm font-bold text-slate-400 hover:text-indigo-600 mb-6 transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
        </a>

        <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden"
            x-data="{ type: '{{ $transaction->type }}' }">

            <div class="px-8 pt-8 pb-4 border-b border-slate-100 bg-slate-50/50">
                <h1 class="text-2xl font-black text-slate-800">Edit Transaksi</h1>
                <p class="text-slate-500 text-sm mt-1">Ubah data transaksi tanggal
                    {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</p>
            </div>

            <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" class="p-8 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Jenis
                        Aktivitas</label>
                    <div class="relative">
                        <select name="type" x-model="type"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-4 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer">
                            <option value="beli" {{ $transaction->type == 'beli' ? 'selected' : '' }}>📉 Beli (Buy)
                            </option>
                            <option value="jual" {{ $transaction->type == 'jual' ? 'selected' : '' }}>📈 Jual (Sell)
                            </option>
                            <option value="topup" {{ $transaction->type == 'topup' ? 'selected' : '' }}>💰 Top Up RDN
                            </option>
                            <option value="tarik" {{ $transaction->type == 'tarik' ? 'selected' : '' }}>💸 Tarik Dana
                            </option>
                            <option value="dividen_cash" {{ $transaction->type == 'dividen_cash' ? 'selected' : '' }}>🎁
                                Dividen Tunai</option>
                            <option value="dividen_unit" {{ $transaction->type == 'dividen_unit' ? 'selected' : '' }}>📃
                                Dividen Unit</option>
                            <option value="stock_split" {{ $transaction->type == 'stock_split' ? 'selected' : '' }}>✂️
                                Stock Split</option>
                            <option value="right_issue" {{ $transaction->type == 'right_issue' ? 'selected' : '' }}>🎟️
                                Right Issue</option>
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Akun RDN</label>
                    <div class="relative">
                        <select name="account_id"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-4 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer">
                            @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ $transaction->account_id == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }}
                            </option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                            <i class="fas fa-wallet text-xs"></i>
                        </div>
                    </div>
                </div>

                <div x-show="!['topup', 'tarik'].includes(type)">
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Produk
                        Investasi</label>
                    <div class="relative">
                        <select name="product_id"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-4 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer">
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ $transaction->product_id == $p->id ? 'selected' : '' }}>
                                {{ $p->code }} - {{ $p->name }}
                            </option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                            <i class="fas fa-chart-line text-xs"></i>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Tanggal</label>
                        <input type="date" name="transaction_date" value="{{ $transaction->transaction_date }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>

                    <div x-show="['beli', 'jual', 'dividen_unit', 'stock_split', 'right_issue'].includes(type)">
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Unit</label>
                        <input type="number" step="any" name="amount" value="{{ $transaction->amount + 0 }}"
                            placeholder="0"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5"
                    x-show="['beli', 'jual', 'topup', 'tarik', 'dividen_cash', 'right_issue'].includes(type)">
                    <div :class="{'col-span-2': ['topup', 'tarik', 'dividen_cash'].includes(type)}">
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">
                            <span
                                x-text="['topup', 'tarik', 'dividen_cash'].includes(type) ? 'Nominal Total (Rp)' : 'Harga / Unit (Rp)'"></span>
                        </label>
                        <input type="number" step="any" name="price"
                            value="{{ $transaction->price_per_unit > 0 ? $transaction->price_per_unit : $transaction->total_value }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>

                    <div x-show="['beli', 'jual', 'right_issue'].includes(type)">
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Biaya
                            (Fee)</label>
                        <input type="number" name="fee" value="{{ $transaction->fee }}" placeholder="0"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white font-bold text-lg py-4 rounded-2xl shadow-xl shadow-indigo-500/30 hover:bg-indigo-700 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('transactions.index') }}"
                        class="px-6 py-4 rounded-2xl font-bold text-slate-500 hover:bg-slate-100 transition flex items-center justify-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection