@extends('layouts.app')

@section('title', 'Detail Rekening - ' . $account->name)

@section('content')
<div class="min-h-screen bg-slate-50 pt-24 pb-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-8">

        <a href="{{ route('accounts.index') }}"
            class="inline-flex items-center gap-2 text-slate-500 hover:text-teal-600 font-bold transition-colors group mb-4">
            <div
                class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center group-hover:border-teal-400 shadow-sm">
                <i class="fas fa-arrow-left text-xs group-hover:-translate-x-0.5 transition-transform"></i>
            </div>
            <span>Kembali ke Daftar Rekening</span>
        </a>

        <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-200 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-48 h-48 bg-teal-50 rounded-full opacity-50 blur-3xl">
            </div>

            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex items-center gap-5">
                    <div
                        class="w-20 h-20 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center text-3xl shadow-inner border border-slate-100">
                        🏦
                    </div>
                    <div>
                        <span
                            class="inline-block px-3 py-1 rounded-lg bg-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wider mb-2 border border-slate-200">
                            {{ $account->bank_name }}
                        </span>
                        <h1 class="text-3xl font-black text-slate-800 tracking-tight">{{ $account->name }}</h1>
                        <p class="text-slate-400 text-sm mt-1">ID Akun: #{{ $account->id }}</p>
                    </div>
                </div>

                <div
                    class="text-left md:text-right w-full md:w-auto bg-slate-50 md:bg-transparent p-5 md:p-0 rounded-xl">
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Saldo Saat Ini</p>
                    <h2 class="text-4xl font-black text-teal-600 tracking-tight">
                        Rp {{ number_format($account->balance, 0, ',', '.') }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-bold text-slate-700 text-lg flex items-center gap-2">
                    <i class="fas fa-history text-teal-500"></i> Riwayat Transaksi
                </h3>

            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 font-bold text-slate-500 text-xs uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 font-bold text-slate-500 text-xs uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-4 font-bold text-slate-500 text-xs uppercase tracking-wider">Detail /
                                Produk</th>
                            <th class="px-6 py-4 font-bold text-slate-500 text-xs uppercase tracking-wider text-right">
                                Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/80 transition-colors duration-150">
                            <td class="px-6 py-4 text-slate-600 font-medium">
                                {{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}
                            </td>

                            <td class="px-6 py-4">
                                @php
                                $isIncome = in_array($trx->type, ['topup', 'jual', 'dividen_cash']);
                                $badgeColor = $isIncome ? 'bg-emerald-100 text-emerald-700 border-emerald-200' :
                                'bg-rose-100 text-rose-700 border-rose-200';
                                $icon = $isIncome ? 'fa-arrow-down' : 'fa-arrow-up'; // Panah masuk/keluar
                                @endphp
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold border {{ $badgeColor }}">
                                    <i class="fas {{ $icon }} text-[10px]"></i>
                                    {{ strtoupper(str_replace('_', ' ', $trx->type)) }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                @if($trx->product)
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-700">{{ $trx->product->code }}</span>
                                    <span class="text-xs text-slate-400">({{ $trx->product->name ?? '' }})</span>
                                </div>
                                @else
                                <span class="text-slate-500 italic">{{ $trx->notes ?? 'Tidak ada catatan' }}</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if(in_array($trx->type, ['topup', 'jual', 'dividen_cash']))
                                <span class="font-bold text-emerald-600 text-base">
                                    + {{ number_format($trx->total_value, 0, ',', '.') }}
                                </span>
                                @else
                                <span class="font-bold text-rose-600 text-base">
                                    - {{ number_format($trx->total_value, 0, ',', '.') }}
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-receipt text-2xl text-slate-300"></i>
                                    </div>
                                    <p class="text-slate-500 font-bold">Belum ada transaksi</p>
                                    <p class="text-xs text-slate-400">Riwayat transaksi akun ini akan muncul di sini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $transactions->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection