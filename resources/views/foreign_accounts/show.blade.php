@extends('layouts.app')

@section('title', 'Detail Valas - ' . $account->name)

@section('content')
<div class="min-h-screen bg-slate-50 pt-24 pb-12 px-4 sm:px-6 lg:px-8" x-data="{ showModal: false }">
    <div class="max-w-5xl mx-auto space-y-8">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <a href="{{ route('foreign-accounts.index') }}"
                class="inline-flex items-center gap-2 text-slate-500 hover:text-sky-600 font-bold transition-colors group">
                <div
                    class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center group-hover:border-sky-400 shadow-sm transition-all">
                    <i class="fas fa-arrow-left text-sm group-hover:-translate-x-0.5 transition-transform"></i>
                </div>
                <span>Kembali ke Dompet Valas</span>
            </a>

            <div class="flex gap-3">
                <button @click="showModal = true"
                    class="bg-sky-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-sky-200 hover:bg-sky-700 hover:scale-105 transition transform duration-200 flex items-center gap-2 text-sm">
                    <i class="fas fa-plus-circle"></i> Catat Manual
                </button>
            </div>
        </div>

        <div
            class="relative overflow-hidden bg-gradient-to-br from-slate-800 to-slate-900 rounded-[2.5rem] p-8 md:p-10 text-white shadow-2xl shadow-slate-200">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-sky-500 rounded-full opacity-20 blur-3xl">
            </div>
            <div
                class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-indigo-500 rounded-full opacity-20 blur-3xl">
            </div>

            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-8">
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-3xl shadow-inner border border-white/20">
                            🏦
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span
                                    class="inline-block px-3 py-1 rounded-lg bg-white/20 backdrop-blur-md text-sky-100 text-xs font-bold uppercase tracking-wider border border-white/10">
                                    {{ $account->bank_name }}
                                </span>
                                <span
                                    class="inline-block px-3 py-1 rounded-lg bg-sky-500/20 backdrop-blur-md text-sky-200 text-xs font-bold uppercase tracking-wider border border-sky-400/30">
                                    {{ $account->currency }}
                                </span>
                            </div>
                            <h1 class="text-3xl md:text-4xl font-black tracking-tight text-white">{{ $account->name }}
                            </h1>
                        </div>
                    </div>

                    <div class="flex gap-6 text-slate-300 text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 bg-white/10 rounded text-xs border border-white/10">Avg Rate: Rp
                                {{ number_format($account->average_rate, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="text-left md:text-right">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Total Saldo</p>
                    <h2
                        class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-sky-200 to-emerald-400 tracking-tight">
                        {{ $account->currency == 'USD' ? '$' : $account->currency }}
                        {{ number_format($account->balance, 2) }}
                    </h2>
                    @if($account->average_rate > 0)
                    <p class="text-sky-200 text-sm font-bold mt-2 opacity-80">
                        ≈ Rp {{ number_format($account->balance * $account->average_rate, 0, ',', '.') }}
                    </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div
                class="p-8 border-b border-slate-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="font-black text-xl text-slate-800 flex items-center gap-2">
                        <span>📊</span> Riwayat Transaksi Valas
                    </h3>
                    <p class="text-slate-400 text-sm font-medium mt-1">Daftar mutasi masuk dan keluar pada akun ini.</p>
                </div>
                <span
                    class="text-xs font-bold text-slate-500 bg-slate-100 px-4 py-2 rounded-xl border border-slate-200">
                    {{ $transactions->total() }} Transaksi
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead
                        class="bg-slate-50/50 text-slate-500 text-xs uppercase font-extrabold tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-5">Tanggal</th>
                            <th class="px-8 py-5">Tipe</th>
                            <th class="px-8 py-5">Keterangan</th>
                            <th class="px-8 py-5 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50 transition duration-150 group">
                            <td class="px-8 py-5 whitespace-nowrap">
                                <span class="text-lg font-black text-slate-700 block leading-none">
                                    {{ \Carbon\Carbon::parse($trx->transaction_date)->format('d') }}
                                </span>
                                <span class="text-xs text-slate-400 font-bold uppercase">
                                    {{ \Carbon\Carbon::parse($trx->transaction_date)->format('M Y') }}
                                </span>
                            </td>

                            <td class="px-8 py-5">
                                <span
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold inline-flex items-center gap-2 
                                    {{ in_array($trx->type, ['topup', 'income', 'deposit']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                                    {{ strtoupper($trx->type) }}
                                </span>
                            </td>

                            <td class="px-8 py-5">
                                <div class="text-sm text-slate-600 font-medium truncate max-w-xs">
                                    {{ $trx->notes ?? $trx->description ?? '-' }}
                                </div>
                                @if($trx->exchange_rate && $trx->exchange_rate > 0)
                                <div
                                    class="text-[10px] text-slate-400 mt-1 font-mono bg-slate-100 px-2 py-0.5 rounded w-fit">
                                    Kurs: Rp {{ number_format($trx->exchange_rate, 0, ',', '.') }}
                                </div>
                                @endif
                            </td>

                            <td class="px-8 py-5 text-right">
                                @php $amount = $trx->amount ?? $trx->total_value ?? 0; @endphp
                                @if(in_array($trx->type, ['topup', 'income', 'deposit']))
                                <span class="font-black text-emerald-600 text-base tracking-tight">
                                    + {{ number_format($amount, 2) }}
                                </span>
                                @else
                                <span class="font-black text-rose-500 text-base tracking-tight">
                                    - {{ number_format($amount, 2) }}
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <p class="font-bold text-slate-500">Belum ada transaksi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
            <div class="px-8 py-6 border-t border-slate-50 bg-slate-50/30">
                {{ $transactions->links() }}
            </div>
            @endif
        </div>
    </div>

    <div x-show="showModal" style="display: none;"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4"
        x-transition.opacity>
        <div @click.away="showModal = false"
            class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden relative" x-transition.scale>

            <div class="px-8 pt-8 pb-4 flex justify-between items-center bg-white sticky top-0 z-10">
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Catat Manual</h3>
                <button @click="showModal = false"
                    class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 hover:bg-slate-100 transition flex items-center justify-center">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form action="{{ route('transactions.store') }}" method="POST" class="px-8 pb-8 space-y-5">
                @csrf
                <input type="hidden" name="account_id" value="{{ $account->id }}">

                <div class="grid grid-cols-2 gap-4 p-1 bg-slate-100 rounded-2xl" x-data="{ type: 'topup' }">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="topup" class="peer sr-only" x-model="type" checked>
                        <div
                            class="text-center py-3 rounded-xl font-bold text-sm text-slate-500 peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm transition-all">
                            Pemasukan (+)
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="withdrawal" class="peer sr-only" x-model="type">
                        <div
                            class="text-center py-3 rounded-xl font-bold text-sm text-slate-500 peer-checked:bg-white peer-checked:text-rose-500 peer-checked:shadow-sm transition-all">
                            Pengeluaran (-)
                        </div>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Jumlah
                        ({{ $account->currency }})</label>
                    <input type="number" step="0.01" name="amount" placeholder="0.00" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-sky-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Keterangan</label>
                    <input type="text" name="notes" placeholder="Contoh: Bunga bank, Biaya admin"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-sky-500 transition-all">
                </div>

                <input type="hidden" name="transaction_date" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="price" value="1">
                <input type="hidden" name="quantity" value="1">

                <button type="submit"
                    class="w-full bg-sky-600 text-white font-bold text-lg py-4 rounded-2xl shadow-xl shadow-sky-200 hover:bg-sky-700 transition-all">
                    Simpan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection