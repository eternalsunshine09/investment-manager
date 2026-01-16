@extends('layouts.app')

@section('title', 'Konversi Mata Uang')

@section('content')
<div class="max-w-xl mx-auto pt-24 pb-10 px-4">

    <a href="{{ route('foreign-accounts.index') }}"
        class="inline-flex items-center gap-2 text-slate-500 font-bold mb-6 hover:text-slate-800 transition">
        <i class="fas fa-arrow-left"></i> Kembali ke Dompet Valas
    </a>

    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-8 md:p-10">
        <h2 class="text-3xl font-black text-slate-800 mb-2 flex items-center gap-3">
            <span class="bg-sky-100 text-sky-600 p-2 rounded-xl text-2xl">💱</span>
            Konversi Valas
        </h2>
        <p class="text-slate-500 font-medium mb-8">Pindahkan saldo Rupiah ke akun mata uang asing.</p>

        @if($usdAccounts->isEmpty())
        <div class="bg-amber-50 border border-amber-100 rounded-2xl p-6 text-center">
            <div class="text-4xl mb-3">⚠️</div>
            <h3 class="font-bold text-amber-800 text-lg">Belum Ada Akun Valas</h3>
            <p class="text-amber-700/80 text-sm mb-4">Kamu harus membuat akun tujuan (misal: PayPal USD) terlebih dahulu
                sebelum melakukan konversi.</p>
            <a href="{{ route('foreign-accounts.index') }}"
                class="inline-block bg-amber-600 text-white font-bold px-6 py-3 rounded-xl hover:bg-amber-700 transition">
                + Buat Akun Valas
            </a>
        </div>
        @else
        <form action="{{ route('conversion.store') }}" method="POST" x-data="{ idr: 0, rate: 15500 }">
            @csrf

            <div class="space-y-6">
                <div class="relative">
                    <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 ml-1">Dari Akun (Sumber
                        Rupiah)</label>
                    <div class="relative">
                        <select name="from_account_id"
                            class="w-full p-4 pl-12 rounded-2xl border border-slate-200 bg-slate-50 font-bold text-slate-700 appearance-none focus:bg-white focus:border-indigo-500 outline-none transition">
                            @foreach($idrAccounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->balance) }})
                            </option>
                            @endforeach
                        </select>
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            🇮🇩
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 ml-1">Ke Akun (Tujuan
                        Valas)</label>
                    <div class="relative">
                        <select name="to_account_id"
                            class="w-full p-4 pl-12 rounded-2xl border border-slate-200 bg-slate-50 font-bold text-slate-700 appearance-none focus:bg-white focus:border-sky-500 outline-none transition">
                            @foreach($usdAccounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->currency }}
                                {{ number_format($acc->balance, 2) }})</option>
                            @endforeach
                        </select>
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            🌎
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 ml-1">Nominal
                            (IDR)</label>
                        <input type="number" name="amount_idr" x-model="idr"
                            class="w-full p-4 rounded-2xl border border-slate-200 focus:border-indigo-500 outline-none font-bold text-slate-700"
                            placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 ml-1">Kurs
                            (Rate)</label>
                        <input type="number" name="exchange_rate" x-model="rate"
                            class="w-full p-4 rounded-2xl border border-slate-200 focus:border-orange-500 outline-none font-bold text-slate-700">
                    </div>
                </div>

                <div
                    class="bg-gradient-to-r from-emerald-50 to-teal-50 p-6 rounded-2xl border border-emerald-100 flex flex-col items-center justify-center text-center shadow-sm">
                    <p class="text-xs text-emerald-600 font-extrabold uppercase tracking-wide mb-1">Estimasi Diterima
                    </p>
                    <h3 class="text-4xl font-black text-emerald-700 tracking-tight">
                        <span x-text="(idr / rate).toFixed(2)">0.00</span>
                        <span class="text-lg text-emerald-500/80 font-bold ml-1">Unit</span>
                    </h3>
                </div>

                <button type="submit"
                    class="w-full bg-slate-900 text-white font-bold text-lg py-4 rounded-2xl hover:bg-slate-800 transition shadow-xl shadow-slate-300 transform active:scale-[0.98]">
                    Konversi Sekarang
                </button>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection