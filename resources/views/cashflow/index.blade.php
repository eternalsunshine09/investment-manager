@extends('layouts.app')

@section('title', 'Cash Flow - InvestApp')

@section('content')
<div class="h-full overflow-y-auto pt-20 md:pt-8 pb-10 px-4" x-data="{ showModal: false, importModal: false }">
    <div class="max-w-6xl mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div class="w-full md:w-auto">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <span>💸</span> Arus Kas Bulanan
                </h1>
                <p class="text-slate-500 mt-2 font-medium">Monitor cashflow untuk maksimalkan investasi.</p>
            </div>

            <div class="w-full md:w-auto flex flex-col md:flex-row gap-3">
                <button @click="importModal = true"
                    class="bg-emerald-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-emerald-200 hover:bg-emerald-700 hover:scale-105 transition transform duration-200 flex items-center justify-center gap-2 whitespace-nowrap">
                    <i class="fas fa-file-csv"></i> Import CSV
                </button>

                <button @click="showModal = true"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:scale-105 transition transform duration-200 flex items-center justify-center gap-2 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Catat Transaksi
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 items-stretch">
            <div
                class="bg-gradient-to-br from-emerald-500 to-emerald-600 text-white p-6 rounded-[2rem] shadow-xl shadow-emerald-200 relative overflow-hidden h-full flex flex-col justify-between">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <div>
                    <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">Pemasukan (Total)</p>
                    <h3 class="text-2xl lg:text-3xl font-black tracking-tight">+ Rp
                        {{ number_format($income ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>

            <div
                class="bg-white text-slate-800 p-6 rounded-[2rem] shadow-sm border border-slate-100 h-full flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Pengeluaran</p>
                    <div class="h-8 w-8 bg-red-50 text-red-500 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl lg:text-3xl font-black text-red-500 tracking-tight">- Rp
                    {{ number_format($expense ?? 0, 0, ',', '.') }}</h3>
            </div>

            <div
                class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 h-full flex flex-col justify-between">
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
                            <th class="px-6 py-4">Akun</th>
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
                            <td class="px-6 py-4 text-sm font-bold text-indigo-600">
                                {{ $flow->account->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold inline-flex items-center gap-1.5 {{ $flow->type == 'income' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $flow->type == 'income' ? '💰' : '💸' }} {{ $flow->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 truncate max-w-xs">
                                {{ $flow->description ?? '-' }}</td>
                            <td
                                class="px-6 py-4 text-right font-black whitespace-nowrap {{ $flow->type == 'income' ? 'text-emerald-600' : 'text-slate-800' }}">
                                {{ $flow->type == 'income' ? '+' : '-' }} Rp
                                {{ number_format($flow->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('cashflow.destroy', $flow->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus transaksi ini? Saldo akan dikembalikan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-slate-300 hover:text-rose-500 p-2 rounded-full hover:bg-rose-50 transition">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">Belum ada data transaksi.</td>
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
            class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden relative max-h-[90vh] overflow-y-auto">
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
                            💰 Pemasukan</div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="expense" class="peer sr-only" x-model="type">
                        <div
                            class="text-center py-3 rounded-xl font-bold text-sm text-slate-500 peer-checked:bg-white peer-checked:text-rose-500 peer-checked:shadow-sm transition-all">
                            💸 Pengeluaran</div>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Sumber Dana /
                        Akun</label>
                    <div class="relative">
                        <select name="account_id" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 transition-all appearance-none cursor-pointer">
                            <option value="" disabled selected>-- Pilih Akun --</option>
                            @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Rp
                                {{ number_format($acc->balance, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Tanggal</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Kategori</label>
                        <input type="text" name="category" placeholder="Contoh: Makan" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Nominal</label>
                        <input type="number" name="amount" placeholder="Rp 0" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Keterangan</label>
                    <input type="text" name="description" placeholder="Catatan tambahan..."
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 transition-all">
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 text-white font-bold text-lg py-4 rounded-2xl shadow-xl shadow-indigo-500/30 hover:bg-indigo-700 hover:scale-[1.02] transition-all">Simpan
                    Cashflow</button>
            </form>
        </div>
    </div>

    <div x-show="importModal" x-cloak
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4"
        x-transition.opacity>

        <div @click.away="importModal = false" class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl p-8 relative">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-slate-800">📂 Import Cashflow</h3>
                <button @click="importModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('cashflow.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 text-sm text-blue-800">
                    <p class="font-bold mb-1">💡 Penting:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        <li>Nama <b>Account</b> di CSV harus sama persis dengan nama Akun di aplikasi ini.</li>
                        <li>Maksimal ukuran file <b>2 MB</b>.</li>
                    </ul>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-extrabold text-slate-500 uppercase ml-1">Pilih File CSV</label>
                    <input type="file" name="file" accept=".csv" required
                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition cursor-pointer border border-slate-200 rounded-xl">
                </div>

                <button type="submit"
                    class="w-full bg-emerald-600 text-white font-bold py-4 rounded-xl hover:bg-emerald-700 transition shadow-lg shadow-emerald-500/30">
                    Proses Import
                </button>
            </form>
        </div>
    </div>
</div>

</div>
@endsection