@extends('layouts.app')

@section('title', 'Dompet & Aset Valas')

@section('content')
<div class="h-full overflow-y-auto pt-20 md:pt-8 pb-10 px-4">
    <div class="max-w-[1600px] mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                    <span class="text-sky-500">🌎</span> Dompet & Aset Valas
                </h1>
                <p class="text-slate-500 font-medium mt-1">Kelola saldo USD dan mata uang asing lainnya.</p>
            </div>

            <a href="{{ route('conversion.index') }}"
                class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-slate-300 hover:bg-slate-800 transition flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Top Up Valas (Konversi)
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div
                class="md:col-span-1 bg-gradient-to-br from-sky-500 to-blue-600 text-white p-6 rounded-[2rem] shadow-lg shadow-sky-200 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                <p class="text-sky-100 text-xs font-bold uppercase tracking-wider mb-2">Total Aset Valas (Est. USD)</p>
                <h3 class="text-4xl font-black tracking-tight">$ {{ number_format($totalForeignBalance, 2, ',', '.') }}
                </h3>
                <p class="text-xs text-sky-100 mt-2 opacity-80">*Total gabungan semua akun asing</p>
            </div>

            <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($accounts as $acc)
                <div
                    class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="font-bold text-slate-700 text-lg">{{ $acc->name }}</h4>
                            <span
                                class="text-[10px] font-bold bg-sky-50 text-sky-600 px-2 py-1 rounded-md uppercase">{{ $acc->currency }}</span>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase mb-1">Saldo Tersedia</p>
                        <h4 class="text-2xl font-black text-slate-800">
                            {{ $acc->currency == 'USD' ? '$' : $acc->currency }} {{ number_format($acc->balance, 2) }}
                        </h4>
                    </div>
                </div>
                @endforeach

                @if($accounts->isEmpty())
                <div
                    class="bg-slate-50 border-2 border-dashed border-slate-200 p-6 rounded-[2rem] flex flex-col items-center justify-center text-center">
                    <p class="text-slate-400 font-bold mb-2">Belum ada akun Valas</p>
                    <button class="text-sm font-bold text-indigo-600 hover:underline">Buat Akun Baru</button>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50">
                <h3 class="font-bold text-lg text-slate-800">Riwayat Mutasi Valas</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead
                        class="bg-slate-50 text-slate-500 text-xs uppercase font-extrabold tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Akun</th>
                            <th class="px-6 py-4">Tipe</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 text-sm font-bold text-slate-700">
                                {{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $trx->account->name }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded-md text-[10px] font-bold uppercase border
                                    {{ in_array($trx->type, ['topup', 'jual']) ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }}">
                                    {{ $trx->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 truncate max-w-xs">
                                {{ $trx->notes ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-black text-slate-800">
                                {{ in_array($trx->type, ['topup', 'jual']) ? '+' : '-' }}
                                {{ $trx->account->currency == 'USD' ? '$' : '' }}
                                {{ number_format($trx->total_value, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400">Belum ada transaksi valas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection