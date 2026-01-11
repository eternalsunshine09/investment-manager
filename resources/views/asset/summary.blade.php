@extends('layouts.app')

@section('title', 'Rekapan Aset - Porto Tracking')

@section('content')
<div class="h-full overflow-y-auto pt-20 md:pt-8 pb-10 px-4">
    <div class="max-w-[1600px] mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                    <span class="text-indigo-500">📝</span> Rekapan & Update Aset
                </h1>
                <p class="text-slate-500 font-medium mt-1">Perbarui harga pasar secara berkala agar nilai portofolio
                    akurat.</p>
            </div>

            <button type="submit" form="priceForm"
                class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition flex items-center gap-2 transform hover:-translate-y-1">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>

        @if(session('success'))
        <div
            class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-xl"></i>
            <p class="font-bold">{{ session('success') }}</p>
        </div>
        @endif

        <form id="priceForm" action="{{ route('portfolio.update') }}" method="POST">
            @csrf

            @if(isset($portfolio) && $portfolio->isEmpty())
            <div class="bg-white p-10 rounded-[2rem] shadow-sm border border-slate-100 text-center py-24">
                <div class="bg-slate-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl">
                    📭</div>
                <h3 class="text-xl font-bold text-slate-700">Aset Kosong</h3>
                <p class="text-slate-400 mt-2">Kamu belum memiliki aset aktif untuk diupdate.</p>
                <a href="{{ route('transactions.index') }}"
                    class="text-indigo-600 font-bold text-sm mt-4 inline-block hover:underline">+ Beli Aset Dulu</a>
            </div>
            @elseif(isset($portfolio))

            @foreach($portfolio as $category => $items)
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden mb-8">

                <div
                    class="bg-slate-50/50 px-8 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h3 class="text-xl font-black text-slate-700 uppercase tracking-tight flex items-center gap-3">
                        <span
                            class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-lg shadow-sm">
                            @if($category == 'saham') 🏢
                            @elseif($category == 'reksadana') 📃
                            @elseif($category == 'crypto') 🪙
                            @elseif($category == 'emas') 🥇
                            @else 📦 @endif
                        </span>
                        {{ $category }}
                    </h3>
                    <div class="flex gap-3 text-xs font-bold">
                        <div class="bg-white border border-slate-200 text-slate-500 px-4 py-2 rounded-xl shadow-sm">
                            Modal: <span class="text-slate-800">Rp
                                {{ number_format($items->sum('total_cost'), 0, ',', '.') }}</span>
                        </div>
                        <div
                            class="bg-indigo-50 border border-indigo-100 text-indigo-500 px-4 py-2 rounded-xl shadow-sm">
                            Nilai: <span class="text-indigo-700">Rp
                                {{ number_format($items->sum('market_value'), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-white text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="p-5 pl-8 text-xs uppercase font-extrabold tracking-wider w-1/4">Produk</th>
                                <th class="p-5 text-xs uppercase font-extrabold tracking-wider text-center">Unit</th>
                                <th class="p-5 text-xs uppercase font-extrabold tracking-wider w-1/4">Harga Pasar
                                    (Update)</th>
                                <th class="p-5 text-xs uppercase font-extrabold tracking-wider text-right">Total Modal
                                </th>
                                <th class="p-5 pr-8 text-xs uppercase font-extrabold tracking-wider text-right">Nilai
                                    Aset</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach($items as $item)
                            <tr class="hover:bg-slate-50/80 transition group">
                                <td class="p-5 pl-8">
                                    <div class="font-black text-slate-800 text-base">{{ $item['code'] }}</div>
                                    <div class="text-xs font-bold text-slate-400">{{ $item['name'] }}</div>
                                </td>

                                <td class="p-5 font-mono font-bold text-slate-600 text-center bg-slate-50/30">
                                    {{ number_format($item['quantity'] + 0, 4) }}
                                </td>

                                <td class="p-5">
                                    <div class="relative group/input">
                                        <span
                                            class="absolute left-3 top-3 text-slate-400 font-bold text-xs group-focus-within/input:text-indigo-500">Rp</span>
                                        <input type="number" name="prices[{{ $item['id'] }}]"
                                            value="{{ $item['current_price'] }}"
                                            class="w-full pl-8 pr-4 py-2.5 bg-yellow-50/50 border border-yellow-200 rounded-xl font-bold text-slate-700 focus:ring-2 focus:ring-yellow-400 focus:bg-white outline-none transition shadow-sm text-right">
                                    </div>

                                    <div
                                        class="mt-1.5 flex justify-end items-center gap-1.5 opacity-60 group-hover:opacity-100 transition">
                                        <i class="fas fa-clock text-[10px] text-slate-400"></i>
                                        <span class="text-[10px] font-bold text-slate-400">
                                            Update: {{ \Carbon\Carbon::parse($item['updated_at'])->diffForHumans() }}
                                        </span>
                                    </div>
                                </td>

                                <td class="p-5 text-right font-bold text-slate-400">
                                    Rp {{ number_format($item['total_cost'], 0, ',', '.') }}
                                </td>

                                <td class="p-5 pr-8 text-right">
                                    <div class="font-black text-slate-800 text-lg">
                                        Rp {{ number_format($item['market_value'], 0, ',', '.') }}
                                    </div>

                                    @php
                                    $pnl = $item['market_value'] - $item['total_cost'];
                                    $isProfit = $pnl >= 0;
                                    $pnlPct = ($item['total_cost'] > 0) ? ($pnl / $item['total_cost'] * 100) : 0;
                                    @endphp

                                    <div
                                        class="text-xs font-bold mt-1 {{ $isProfit ? 'text-emerald-600' : 'text-rose-500' }}">
                                        {{ $isProfit ? '+' : '' }}{{ number_format($pnlPct, 2) }}%
                                        <span class="opacity-50">({{ number_format($pnl, 0, ',', '.') }})</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach

            @endif
        </form>
    </div>
</div>
@endsection