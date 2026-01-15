@extends('layouts.app')

@section('title', 'Cash Flow - InvestApp')

@section('content')
<div class="h-full overflow-y-auto pt-20 md:pt-8 pb-10 px-4" x-data="{ 
        showModal: false, 
        importModal: false,
        type: 'expense' 
     }">

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
                    class="bg-emerald-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-emerald-200 hover:bg-emerald-700 hover:scale-105 transition transform duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-file-csv"></i> Import CSV
                </button>
                <button @click="showModal = true; type='expense'"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:scale-105 transition transform duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-plus-circle"></i> Catat Transaksi
                </button>
            </div>
        </div>

        @if(session('success'))
        <div
            class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm mb-6 animate-fade-in-down">
            <i class="fas fa-check-circle text-xl"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 items-stretch">
            <div
                class="bg-gradient-to-br from-emerald-500 to-emerald-600 text-white p-6 rounded-[2rem] shadow-xl shadow-emerald-200 relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">Pemasukan</p>
                <h3 class="text-2xl lg:text-3xl font-black tracking-tight">+ Rp
                    {{ number_format($income ?? 0, 0, ',', '.') }}</h3>
            </div>

            <div
                class="bg-white text-slate-800 p-6 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Pengeluaran</p>
                    <div class="h-8 w-8 bg-red-50 text-red-500 rounded-full flex items-center justify-center bg-red-50">
                        <i class="fas fa-arrow-up rotate-45"></i>
                    </div>
                </div>
                <h3 class="text-2xl lg:text-3xl font-black text-red-500 tracking-tight">- Rp
                    {{ number_format($expense ?? 0, 0, ',', '.') }}</h3>
            </div>

            <div
                class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:scale-[1.02] transition-transform duration-300">
                <div class="flex justify-between items-center mb-1">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Savings Rate</p>
                    <span class="text-[10px] font-bold px-2 py-1 rounded bg-slate-100 text-slate-500">Target >20%</span>
                </div>
                <h3 class="text-4xl font-black {{ $savingsRate >= 20 ? 'text-indigo-600' : 'text-orange-500' }}">
                    {{ number_format($savingsRate, 1) }}<span class="text-xl text-slate-400">%</span>
                </h3>
                <div class="w-full bg-slate-100 h-2 rounded-full mt-3 overflow-hidden">
                    <div class="h-full {{ $savingsRate >= 20 ? 'bg-indigo-600' : 'bg-orange-500' }}"
                        style="width: {{ min($savingsRate, 100) }}%"></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                <h3 class="font-black text-xl text-slate-800 flex items-center gap-2"><span>🧾</span> Riwayat Transaksi
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead
                        class="bg-slate-50/50 text-slate-500 text-xs uppercase font-extrabold tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-5">Tanggal</th>
                            <th class="px-8 py-5">Akun</th>
                            <th class="px-8 py-5">Kategori</th>
                            <th class="px-8 py-5">Keterangan</th>
                            <th class="px-8 py-5 text-right">Nominal</th>
                            <th class="px-8 py-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($flows as $flow)
                        <tr class="hover:bg-slate-50 transition duration-150 group">
                            <td class="px-8 py-5 whitespace-nowrap">
                                <span
                                    class="text-lg font-black text-slate-700 block leading-none">{{ $flow->date->format('d') }}</span>
                                <span
                                    class="text-xs text-slate-400 font-bold uppercase">{{ $flow->date->format('M Y') }}</span>
                            </td>
                            <td class="px-8 py-5 text-sm font-bold text-indigo-600">
                                {{ $flow->account->name ?? 'Unknown' }}</td>
                            <td class="px-8 py-5">
                                <span
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold inline-flex items-center gap-2 {{ $flow->type == 'income' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $flow->type == 'income' ? '💰' : '💸' }} {{ $flow->category }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-sm text-slate-500 truncate max-w-xs font-medium">
                                {{ $flow->description ?? '-' }}</td>
                            <td
                                class="px-8 py-5 text-right font-black whitespace-nowrap text-base tracking-tight {{ $flow->type == 'income' ? 'text-emerald-600' : 'text-slate-800' }}">
                                {{ $flow->type == 'income' ? '+' : '-' }} Rp
                                {{ number_format($flow->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-8 py-5 text-center">
                                <div
                                    class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <a href="{{ route('cashflow.edit', $flow->id) }}"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition"
                                        title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('cashflow.destroy', $flow->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus data ini? Saldo akan dikembalikan.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                            title="Hapus"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-8 py-16 text-center text-slate-400">Belum ada data transaksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($flows->hasPages())
            <div class="px-8 py-4 border-t border-slate-100 bg-slate-50/50">{{ $flows->links() }}</div>
            @endif
        </div>
    </div>

    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>

        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showModal = false"></div>

        <div class="relative bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            <div class="px-8 pt-8 pb-4 flex justify-between items-center bg-white sticky top-0 z-10">
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Catat Transaksi</h3>
                <button @click="showModal = false"
                    class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 hover:bg-slate-100 flex items-center justify-center"><i
                        class="fas fa-times text-lg"></i></button>
            </div>

            <form action="{{ route('cashflow.store') }}" method="POST" class="px-8 pb-8 space-y-6">
                @csrf
                <div class="grid grid-cols-3 gap-2 p-1.5 bg-slate-100 rounded-2xl">
                    <label class="cursor-pointer group">
                        <input type="radio" name="type" value="income" class="peer sr-only" x-model="type">
                        <div
                            class="text-center py-3.5 rounded-xl font-bold text-xs text-slate-500 peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm transition-all group-hover:text-slate-700">
                            Pemasukan</div>
                    </label>
                    <label class="cursor-pointer group">
                        <input type="radio" name="type" value="expense" class="peer sr-only" x-model="type">
                        <div
                            class="text-center py-3.5 rounded-xl font-bold text-xs text-slate-500 peer-checked:bg-white peer-checked:text-rose-500 peer-checked:shadow-sm transition-all group-hover:text-slate-700">
                            Pengeluaran</div>
                    </label>
                    <label class="cursor-pointer group">
                        <input type="radio" name="type" value="transfer" class="peer sr-only" x-model="type">
                        <div
                            class="text-center py-3.5 rounded-xl font-bold text-xs text-slate-500 peer-checked:bg-white peer-checked:text-blue-600 peer-checked:shadow-sm transition-all group-hover:text-slate-700">
                            Transfer</div>
                    </label>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-extrabold text-slate-500 uppercase ml-1"
                        x-text="type === 'transfer' ? 'Dari Akun' : 'Akun Dompet'"></label>
                    <div class="relative">
                        <select name="account_id"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 transition-all appearance-none cursor-pointer"
                            required>
                            <option value="" disabled selected>-- Pilih Akun --</option>
                            @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Rp
                                {{ number_format($acc->balance, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center px-5 pointer-events-none text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i></div>
                    </div>
                </div>

                <div x-show="type === 'transfer'" x-transition
                    class="bg-blue-50/50 p-5 rounded-2xl border border-blue-100 space-y-2">
                    <label class="block text-xs font-extrabold text-blue-600 uppercase mb-1 ml-1">Ke Akun
                        (Tujuan)</label>
                    <div class="relative">
                        <select name="to_account_id"
                            class="w-full bg-white border border-blue-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:outline-none focus:border-blue-500 transition-all appearance-none cursor-pointer">
                            <option value="" disabled selected>-- Pilih Akun Tujuan --</option>
                            @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-blue-400">
                            <i class="fas fa-chevron-down text-xs"></i></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div x-show="type !== 'transfer'">
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Kategori</label>
                        <input type="text" name="category" placeholder="Contoh: Makan"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 transition-all">
                    </div>
                    <div :class="type === 'transfer' ? 'col-span-2' : ''">
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Nominal</label>
                        <input type="number" name="amount" placeholder="0" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Tanggal</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Catatan
                        (Opsional)</label>
                    <input type="text" name="description" placeholder="Keterangan tambahan..."
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 transition-all">
                </div>

                <button type="submit"
                    class="w-full text-white font-bold text-lg py-4 rounded-2xl shadow-xl hover:scale-[1.02] active:scale-[0.98] transition-all"
                    :class="{ 'bg-emerald-600 shadow-emerald-500/30': type === 'income', 'bg-rose-600 shadow-rose-500/30': type === 'expense', 'bg-blue-600 shadow-blue-500/30': type === 'transfer' }">
                    <span x-text="type === 'transfer' ? 'Proses Transfer' : 'Simpan Transaksi'"></span>
                </button>
            </form>
        </div>
    </div>

    <div x-show="importModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="importModal = false"></div>
        <div class="relative bg-white rounded-[2.5rem] w-full max-w-md shadow-2xl p-8"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-slate-800">📂 Import Cashflow</h3>
                <button @click="importModal = false" class="text-slate-400 hover:text-slate-600"><i
                        class="fas fa-times text-xl"></i></button>
            </div>
            <form action="{{ route('cashflow.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 text-sm text-blue-800">
                    <p class="font-bold mb-1">💡 Penting:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        <li>Nama <b>Account</b> di CSV harus sama persis dengan nama Akun di aplikasi.</li>
                        <li>Maksimal ukuran file <b>2 MB</b>.</li>
                    </ul>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-extrabold text-slate-500 uppercase ml-1">Pilih File CSV</label>
                    <input type="file" name="file" accept=".csv" required
                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition cursor-pointer border border-slate-200 rounded-xl">
                </div>
                <button type="submit"
                    class="w-full bg-emerald-600 text-white font-bold py-4 rounded-xl hover:bg-emerald-700 transition shadow-lg shadow-emerald-500/30">Proses
                    Import</button>
            </form>
        </div>
    </div>
</div>
@endsection