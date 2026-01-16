@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
<div class="min-h-screen bg-slate-50 pt-24 pb-12 px-4">
    <div class="max-w-xl mx-auto">

        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('cashflow.index') }}"
                class="group flex items-center gap-2 text-slate-500 hover:text-indigo-600 transition font-bold">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                Kembali
            </a>
            <h1 class="text-2xl font-black text-slate-800">Edit Transaksi</h1>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 p-8">

            <form action="{{ route('cashflow.update', $flow->id) }}" method="POST" class="space-y-6"
                x-data="{ type: '{{ $flow->type }}' }">
                @csrf
                @method('PUT')

                <div class="bg-blue-50 text-blue-700 px-4 py-3 rounded-xl text-sm flex gap-3 items-start">
                    <i class="fas fa-info-circle mt-0.5"></i>
                    <p>Mengubah data akan otomatis mengkoreksi saldo akun terkait.</p>
                </div>

                <div class="grid grid-cols-2 gap-2 p-1.5 bg-slate-100 rounded-2xl">
                    <label class="cursor-pointer group">
                        <input type="radio" name="type" value="income" class="peer sr-only" x-model="type">
                        <div
                            class="text-center py-3.5 rounded-xl font-bold text-sm text-slate-500 peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm transition-all flex flex-col items-center gap-1">
                            <i class="fas fa-arrow-down"></i> Pemasukan
                        </div>
                    </label>
                    <label class="cursor-pointer group">
                        <input type="radio" name="type" value="expense" class="peer sr-only" x-model="type">
                        <div
                            class="text-center py-3.5 rounded-xl font-bold text-sm text-slate-500 peer-checked:bg-white peer-checked:text-rose-500 peer-checked:shadow-sm transition-all flex flex-col items-center gap-1">
                            <i class="fas fa-arrow-up"></i> Pengeluaran
                        </div>
                    </label>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-extrabold text-slate-500 uppercase ml-1">Akun</label>
                    <div class="relative">
                        <select name="account_id"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 transition-all appearance-none cursor-pointer">
                            @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ $flow->account_id == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }} (Saldo: Rp {{ number_format($acc->balance, 0, ',', '.') }})
                            </option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center px-5 pointer-events-none text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Tanggal
                        Transaksi</label>
                    <input type="date" name="date" value="{{ $flow->date->format('Y-m-d') }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 transition-all">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Kategori</label>
                        <input type="text" name="category" value="{{ $flow->category }}" placeholder="Makan, Gaji, dll"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Nominal
                            (Rp)</label>
                        <input type="number" name="amount" value="{{ old('amount', $flow->amount) }}" placeholder="0"
                            required
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Catatan
                        (Opsional)</label>
                    <textarea name="description" rows="3" placeholder="Keterangan tambahan..."
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 transition-all resize-none">{{ $flow->description }}</textarea>
                </div>

                <button type="submit"
                    class="w-full text-white font-bold text-lg py-4 rounded-2xl shadow-xl hover:scale-[1.02] active:scale-[0.98] transition-all duration-200"
                    :class="{ 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/30': type === 'income', 'bg-rose-600 hover:bg-rose-700 shadow-rose-500/30': type === 'expense' }">
                    Simpan Perubahan
                </button>

            </form>
        </div>
    </div>
</div>
@endsection