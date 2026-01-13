@extends('layouts.app')

@section('title', 'Arus Kas Bulanan')

@section('content')
<div class="h-full overflow-y-auto pt-20 md:pt-8 pb-10 px-4" x-data="{ showModal: false }">
    <div class="max-w-[1600px] mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                    <span class="text-indigo-600">💸</span> Arus Kas Bulanan
                </h1>
                <p class="text-slate-500 font-medium mt-1">Monitor pemasukan & pengeluaran bulananmu.</p>
            </div>

            <button @click="showModal = true"
                class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 hover:scale-105 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Catat Cashflow
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 items-stretch">

            <div
                class="bg-gradient-to-br from-emerald-500 to-emerald-600 text-white p-6 rounded-[1.5rem] shadow-lg shadow-emerald-200 relative overflow-hidden h-full flex flex-col justify-between">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <div>
                    <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">Pemasukan (Bulan Ini)
                    </p>
                    <h3 class="text-2xl lg:text-3xl font-black tracking-tight">+ Rp
                        {{ number_format($income ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>

            <div
                class="bg-white text-slate-800 p-6 rounded-[1.5rem] shadow-sm border border-slate-100 h-full flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Pengeluaran</p>
                    <div class="h-8 w-8 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                </div>
                <h3 class="text-2xl lg:text-3xl font-black text-rose-500 tracking-tight">- Rp
                    {{ number_format($expense ?? 0, 0, ',', '.') }}</h3>
            </div>

            <div
                class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-100 h-full flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Savings Rate</p>
                        <span class="text-[10px] font-bold px-2 py-1 rounded bg-slate-100 text-slate-500">Target
                            >20%</span>
                    </div>
                    @php
                    $savingsRate = $savingsRate ?? 0;
                    $widthPercentage = min($savingsRate, 100);
                    @endphp
                    <h3 class="text-4xl font-black {{ $savingsRate >= 20 ? 'text-indigo-600' : 'text-orange-500' }}">
                        {{ number_format($savingsRate, 1) }}<span class="text-xl text-slate-400">%</span>
                    </h3>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full mt-3 overflow-hidden">
                    <div class="h-full {{ $savingsRate >= 20 ? 'bg-indigo-600' : 'bg-orange-500' }}"
                        style="width: {{ $widthPercentage }}%"></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-lg text-slate-800">Riwayat Cashflow</h3>
                <span
                    class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">{{ count($flows ?? []) }}
                    Data</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead
                        class="bg-slate-50 text-slate-500 text-xs uppercase font-extrabold tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4 text-right">Nominal</th>
                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($flows ?? [] as $flow)
                        <tr class="hover:bg-slate-50 transition duration-150 group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="text-sm font-bold text-slate-700 block">{{ $flow->date->format('d') }}</span>
                                <span
                                    class="text-xs text-slate-400 font-bold uppercase">{{ $flow->date->format('M Y') }}</span>
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold inline-flex items-center gap-1.5
                                    {{ $flow->type == 'income' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                                    {{ $flow->type == 'income' ? '💰' : '💸' }} {{ $flow->category }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm font-medium text-slate-500 max-w-xs truncate">
                                {{ $flow->description ?? '-' }}
                            </td>

                            <td
                                class="px-6 py-4 text-right font-black whitespace-nowrap {{ $flow->type == 'income' ? 'text-emerald-600' : 'text-slate-800' }}">
                                {{ $flow->type == 'income' ? '+' : '-' }} Rp
                                {{ number_format($flow->amount, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('cashflow.destroy', $flow->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus transaksi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-slate-300 hover:text-rose-500 p-2 rounded-full hover:bg-rose-50 transition"
                                        title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-wallet text-4xl mb-3 text-slate-200"></i>
                                    <p>Belum ada data cashflow bulan ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div x-show="showModal" x-cloak
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4"
        x-transition.opacity>
        <div @click.away="showModal = false"
            class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden relative">

            <div class="px-8 pt-8 pb-4 flex justify-between items-center bg-white sticky top-0 z-10">
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Catat Cashflow</h3>
                <button @click="showModal = false"
                    class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition flex items-center justify-center">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form action="{{ route('cashflow.store') }}" method="POST" class="px-8 pb-8 space-y-5">
                @csrf
                <div class="grid grid-cols-2 gap-4 p-1 bg-slate-100 rounded-2xl" x-data="{ type: 'expense' }">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="income" class="peer sr-only" x-model="type">
                        <div
                            class="text-center py-3 rounded-xl font-bold text-sm text-slate-500 peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm transition-all">
                            💰 Pemasukan
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="expense" class="peer sr-only" x-model="type">
                        <div
                            class="text-center py-3 rounded-xl font-bold text-sm text-slate-500 peer-checked:bg-white peer-checked:text-rose-500 peer-checked:shadow-sm transition-all">
                            💸 Pengeluaran
                        </div>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Tanggal</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Kategori</label>
                        <input type="text" name="category" placeholder="Contoh: Makan" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Nominal</label>
                        <input type="number" name="amount" placeholder="Rp 0" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Keterangan
                        (Opsional)</label>
                    <input type="text" name="description" placeholder="Catatan tambahan..."
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 text-white font-bold text-lg py-4 rounded-2xl shadow-xl shadow-indigo-500/30 hover:bg-indigo-700 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                    Simpan Cashflow
                </button>
            </form>
        </div>
    </div>
</div>
@endsection