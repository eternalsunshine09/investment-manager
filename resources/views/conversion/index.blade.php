@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto pt-10">
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
        <h2 class="text-2xl font-black text-slate-800 mb-6 flex items-center gap-2">
            <span>💱</span> Konversi Mata Uang
        </h2>

        <form action="{{ route('conversion.store') }}" method="POST" x-data="{ idr: 0, rate: 15500 }">
            @csrf

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Dari Akun (IDR)</label>
                <select name="from_account_id" class="w-full p-3 rounded-xl border bg-slate-50 font-bold">
                    @foreach($idrAccounts as $acc)
                    <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->balance) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Ke Akun (USD)</label>
                <select name="to_account_id" class="w-full p-3 rounded-xl border bg-slate-50 font-bold">
                    @foreach($usdAccounts as $acc)
                    <option value="{{ $acc->id }}">{{ $acc->name }} ($ {{ number_format($acc->balance, 2) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Nominal Rupiah (IDR)</label>
                <input type="number" name="amount_idr" x-model="idr"
                    class="w-full p-3 rounded-xl border border-indigo-200 focus:ring-indigo-500 font-bold text-slate-700"
                    placeholder="0">
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Kurs Tukar (Rate)</label>
                <input type="number" name="exchange_rate" x-model="rate"
                    class="w-full p-3 rounded-xl border border-orange-200 focus:ring-orange-500 font-bold text-slate-700">
                <p class="text-[10px] text-slate-400 mt-1">*Masukkan kurs jual bank/sekuritas.</p>
            </div>

            <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100 mb-6 text-center">
                <p class="text-xs text-emerald-600 font-bold uppercase">Estimasi Diterima</p>
                <h3 class="text-3xl font-black text-emerald-700">
                    $ <span x-text="(idr / rate).toFixed(2)">0.00</span>
                </h3>
            </div>

            <button type="submit"
                class="w-full bg-slate-900 text-white font-bold py-4 rounded-xl hover:bg-slate-800 transition">
                Konversi Sekarang
            </button>
        </form>
    </div>
</div>
@endsection