@extends('layouts.app')

@section('title', 'Dompet & Aset Valas')

@section('content')
<div class="h-full overflow-y-auto pt-20 md:pt-8 pb-10 px-4" x-data="{ showModal: false }">
    <div class="max-w-[1600px] mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-sky-100 text-sky-600 p-2 rounded-2xl text-2xl">🌎</span>
                    Dompet & Aset Valas
                </h1>
                <p class="text-slate-500 font-medium mt-2 ml-1">Kelola saldo USD, SGD, dan mata uang asing lainnya.</p>
            </div>

            <div class="flex gap-3">
                <button @click="showModal = true"
                    class="bg-white text-slate-700 border border-slate-200 px-6 py-3 rounded-2xl font-bold hover:bg-slate-50 hover:border-sky-200 transition flex items-center gap-2 shadow-sm">
                    <i class="fas fa-plus text-sky-500"></i> Buat Akun Valas
                </button>

                <a href="{{ route('conversion.index') }}"
                    class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-slate-300 hover:bg-slate-800 transition flex items-center gap-2">
                    <i class="fas fa-exchange-alt"></i> Top Up / Konversi
                </a>
            </div>
        </div>

        @if(session('success'))
        <div
            class="bg-emerald-50 text-emerald-700 px-6 py-4 rounded-2xl border border-emerald-100 font-bold mb-8 flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-xl"></i> {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div
                class="lg:col-span-1 bg-gradient-to-br from-sky-500 to-indigo-600 text-white p-8 rounded-[2.5rem] shadow-2xl shadow-sky-200 relative overflow-hidden flex flex-col justify-between min-h-[240px]">
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-48 h-48 bg-white/20 rounded-full blur-3xl"></div>
                <div
                    class="absolute bottom-0 left-0 -mb-10 -ml-10 w-32 h-32 bg-indigo-400 rounded-full blur-3xl opacity-50">
                </div>

                <div class="relative z-10">
                    <div
                        class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-2xl mb-6 border border-white/10 shadow-inner">
                        💰
                    </div>
                    <p class="text-sky-100 text-sm font-bold uppercase tracking-wider mb-2">Total Aset Valas (Estimasi
                        IDR)</p>
                    <h3 class="text-4xl md:text-5xl font-black tracking-tight text-white">
                        <span
                            class="text-2xl opacity-70 font-sans mr-1">Rp</span>{{ number_format($totalForeignBalanceIDR, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="relative z-10 border-t border-white/10 pt-4 mt-4">
                    <p class="text-xs text-sky-100 opacity-80 flex items-center gap-2">
                        <i class="fas fa-info-circle"></i> Berdasarkan rata-rata kurs pembelian (Avg Rate)
                    </p>
                </div>
            </div>

            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
                @foreach($accounts as $acc)
                <a href="{{ route('foreign-accounts.show', $acc->id) }}" class="group block h-full">
                    <div
                        class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm group-hover:shadow-xl group-hover:border-sky-100 group-hover:-translate-y-1 transition-all duration-300 h-full flex flex-col justify-between relative overflow-hidden">

                        <div class="flex justify-between items-start mb-6 relative z-10">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span
                                        class="text-[10px] font-black bg-sky-50 text-sky-600 px-3 py-1 rounded-full uppercase tracking-wide border border-sky-100">
                                        {{ $acc->currency }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-slate-700 text-xl group-hover:text-sky-600 transition">
                                    {{ $acc->name }}</h4>
                                <p class="text-sm text-slate-400 font-medium mt-0.5">{{ $acc->bank_name }}</p>
                            </div>
                            <div
                                class="w-12 h-12 rounded-2xl bg-white border border-slate-100 text-slate-400 flex items-center justify-center text-xl group-hover:bg-sky-500 group-hover:text-white group-hover:border-sky-500 transition-all shadow-sm">
                                <i class="fas fa-chevron-right text-sm"></i>
                            </div>
                        </div>

                        <div class="relative z-10 pt-4 border-t border-slate-50">
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Saldo Valas</p>
                                    <h4 class="text-3xl font-black text-slate-800 group-hover:text-sky-700 transition">
                                        <span
                                            class="text-lg text-slate-400 font-medium mr-1">{{ $acc->currency == 'USD' ? '$' : $acc->currency }}</span>{{ number_format($acc->balance, 2) }}
                                    </h4>
                                </div>

                                @if($acc->average_rate > 0)
                                <div class="text-right">
                                    <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Estimasi IDR</p>
                                    <p class="text-sm font-bold text-slate-600">
                                        Rp {{ number_format($acc->balance * $acc->average_rate, 0, ',', '.') }}
                                    </p>
                                    <p class="text-[10px] text-slate-400">Rate:
                                        {{ number_format($acc->average_rate, 0) }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach

                <button @click="showModal = true"
                    class="group bg-slate-50 border-2 border-dashed border-slate-200 p-6 rounded-[2.5rem] flex flex-col items-center justify-center text-center hover:border-sky-300 hover:bg-sky-50 transition-all min-h-[200px]">
                    <div
                        class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-plus text-sky-500 text-2xl"></i>
                    </div>
                    <p class="text-slate-600 font-bold group-hover:text-sky-700">Tambah Akun Baru</p>
                    <p class="text-slate-400 text-xs mt-1">PayPal, Wise, Payoneer, atau Bank</p>
                </button>
            </div>
        </div>
    </div>

    <div x-show="showModal" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        x-transition.opacity>

        <div @click.away="showModal = false"
            class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-8 transform transition-all scale-100"
            x-transition.scale>

            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-2xl font-black text-slate-800">Buat Akun Valas</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase mt-1">Tambah Dompet Asing Baru</p>
                </div>
                <button @click="showModal = false"
                    class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form action="{{ route('accounts.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Mata Uang</label>
                    <div class="relative">
                        <select name="currency"
                            class="w-full px-5 py-4 rounded-2xl border border-slate-200 font-bold text-slate-700 bg-slate-50 focus:bg-white focus:border-sky-500 outline-none appearance-none cursor-pointer transition">
                            <option value="USD">🇺🇸 USD - US Dollar</option>
                            <option value="SGD">🇸🇬 SGD - Singapore Dollar</option>
                            <option value="JPY">🇯🇵 JPY - Japanese Yen</option>
                            <option value="EUR">🇪🇺 EUR - Euro</option>
                            <option value="AUD">🇦🇺 AUD - Australian Dollar</option>
                            <option value="CNY">🇨🇳 CNY - Chinese Yuan</option>
                        </select>
                        <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Nama Platform</label>
                    <input type="text" name="name" placeholder="Contoh: PayPal, Wise" required
                        class="w-full px-5 py-4 rounded-2xl border border-slate-200 font-bold text-slate-700 focus:border-sky-500 focus:bg-white bg-slate-50 outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Kategori /
                        Bank</label>
                    <input type="text" name="bank_name" placeholder="Contoh: E-Wallet, DBS" required
                        class="w-full px-5 py-4 rounded-2xl border border-slate-200 font-bold text-slate-700 focus:border-sky-500 focus:bg-white bg-slate-50 outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Saldo Awal
                        (Opsional)</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                        <input type="number" step="0.01" name="initial_balance" placeholder="0.00"
                            class="w-full pl-10 pr-5 py-4 rounded-2xl border border-slate-200 font-bold text-slate-700 focus:border-sky-500 focus:bg-white bg-slate-50 outline-none transition">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-sky-600 text-white font-bold text-lg py-4 rounded-2xl hover:bg-sky-700 transition shadow-xl shadow-sky-200/50 active:scale-[0.98] mt-4">
                    Simpan Akun
                </button>
            </form>
        </div>
    </div>
</div>
@endsection