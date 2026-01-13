@extends('layouts.app')

@section('title', 'Portofolio Aset - Porto Tracking')

@section('content')
<div class="h-full overflow-y-auto pt-20 md:pt-8 pb-10 px-4">
    <div class="max-w-[1600px] mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                    <span class="text-indigo-500">📈</span> Portofolio Aset
                </h1>
                <p class="text-slate-500 font-medium mt-1">Pantau performa untung/rugi asetmu secara real-time.</p>
            </div>

            <a href="{{ route('asset.summary') }}"
                class="bg-indigo-600 text-white px-5 py-3 rounded-xl font-bold hover:bg-indigo-700 transition flex items-center gap-2 shadow-lg shadow-indigo-200 transform hover:-translate-y-0.5">
                <i class="fas fa-edit"></i> Update Harga Pasar
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-5 rounded-[1.5rem] shadow-sm border border-slate-100">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-1">Total Modal (Cost)</p>
                <h3 class="text-xl font-black text-slate-800">
                    Rp {{ number_format(collect($summary)->sum('total_cost'), 0, ',', '.') }}
                </h3>
            </div>

            <div class="bg-white p-5 rounded-[1.5rem] shadow-sm border border-slate-100">
                <p class="text-indigo-500 text-[10px] font-bold uppercase tracking-wider mb-1">Total Nilai Pasar</p>
                <h3 class="text-xl font-black text-indigo-600">
                    Rp {{ number_format(collect($summary)->sum('market_value'), 0, ',', '.') }}
                </h3>
            </div>

            @php
            $totalUnrealized = collect($summary)->sum('unrealized_pnl');
            $totalRealized = collect($summary)->sum('realized_pnl');
            @endphp
            <div class="bg-white p-5 rounded-[1.5rem] shadow-sm border border-slate-100">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-1">Unrealized PnL (Floating)
                </p>
                <h3 class="text-xl font-black {{ $totalUnrealized >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                    {{ $totalUnrealized >= 0 ? '+' : '' }}Rp {{ number_format($totalUnrealized, 0, ',', '.') }}
                </h3>
            </div>

            <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-5 rounded-[1.5rem] shadow-lg text-white">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-1">Realized PnL (Sudah Jual)
                </p>
                <h3 class="text-xl font-black {{ $totalRealized >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ $totalRealized >= 0 ? '+' : '' }}Rp {{ number_format($totalRealized, 0, ',', '.') }}
                </h3>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[1200px]">
                    <thead>
                        <tr
                            class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-100">
                            <th class="p-6 font-bold w-1/4">Produk Investasi</th>
                            <th class="p-6 font-bold text-center">Unit</th>
                            <th class="p-6 font-bold text-right">Avg Price</th>
                            <th class="p-6 font-bold text-right text-indigo-500">Harga Pasar</th>
                            <th class="p-6 font-bold text-right">Total Modal</th>
                            <th class="p-6 font-bold text-right text-indigo-600">Nilai Aset</th>
                            <th class="p-6 font-bold text-right">Realized PnL</th>
                            <th class="p-6 font-bold text-right">Unrealized PnL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        @forelse($summary as $item)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="p-6">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs text-indigo-600 bg-indigo-50 border border-indigo-100">
                                        {{ Str::limit($item['code'], 4, '') }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-base">{{ $item['code'] }}</div>
                                        <div class="text-xs text-slate-500 font-medium">{{ $item['name'] }}</div>
                                        <span
                                            class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500 uppercase tracking-wide">
                                            {{ $item['category'] }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="p-6 text-center font-mono font-bold text-slate-600">
                                {{ number_format($item['units'] + 0, 4) }}
                            </td>

                            <td class="p-6 text-right font-medium text-slate-500">
                                Rp {{ number_format($item['avg_price'], 0, ',', '.') }}
                            </td>

                            <td class="p-6 text-right font-bold text-indigo-600 bg-indigo-50/30">
                                Rp {{ number_format($item['current_price'], 0, ',', '.') }}
                            </td>

                            <td class="p-6 text-right font-medium text-slate-600">
                                Rp {{ number_format($item['total_cost'], 0, ',', '.') }}
                            </td>

                            <td class="p-6 text-right font-black text-indigo-700 bg-indigo-50/30">
                                Rp {{ number_format($item['market_value'], 0, ',', '.') }}
                            </td>

                            <td class="p-6 text-right">
                                <div
                                    class="font-bold {{ $item['realized_pnl'] >= 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                                    {{ $item['realized_pnl'] >= 0 ? '+' : '' }}Rp
                                    {{ number_format($item['realized_pnl'], 0, ',', '.') }}
                                </div>
                            </td>

                            <td class="p-6 text-right">
                                <div
                                    class="font-black text-base {{ $item['unrealized_pnl'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                    {{ $item['unrealized_pnl'] >= 0 ? '+' : '' }}Rp
                                    {{ number_format($item['unrealized_pnl'], 0, ',', '.') }}
                                </div>
                                <div
                                    class="text-xs font-bold mt-1 {{ $item['pnl_pct'] >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} inline-block px-2 py-0.5 rounded-lg">
                                    {{ $item['pnl_pct'] >= 0 ? '▲' : '▼' }} {{ number_format($item['pnl_pct'], 2) }}%
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-16 text-center text-slate-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-folder-open text-4xl mb-3 text-slate-200"></i>
                                    <p class="font-medium">Portofolio masih kosong.</p>
                                    <a href="{{ route('transactions.index') }}"
                                        class="text-indigo-500 font-bold text-sm mt-2 hover:underline">+ Beli Aset
                                        Pertama</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection