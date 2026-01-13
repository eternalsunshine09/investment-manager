@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Porto Tracking')

@section('content')
<div class="h-full overflow-y-auto pt-20 md:pt-8 pb-10 px-4" x-data="{ showModal: false, type: 'beli' }">
    <div class="max-w-[1600px] mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                    <span class="text-indigo-600">📊</span> Riwayat Transaksi
                </h1>
                <p class="text-slate-500 font-medium mt-1">Laporan detail arus kas, fee broker, dan pajak.</p>
            </div>
            <button @click="showModal = true"
                class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 hover:scale-105 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Transaksi Baru
            </button>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-6 flex flex-wrap gap-3 items-center">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-2"><i
                    class="fas fa-filter mr-1"></i> Filter:</span>
            <form action="{{ route('transactions.index') }}" method="GET"
                class="flex flex-wrap gap-3 items-center flex-1">
                <select name="type" onchange="this.form.submit()"
                    class="bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 font-bold outline-none cursor-pointer">
                    <option value="">Semua Tipe</option>
                    <option value="beli" {{ request('type') == 'beli' ? 'selected' : '' }}>Pembelian</option>
                    <option value="jual" {{ request('type') == 'jual' ? 'selected' : '' }}>Penjualan</option>
                    <option value="dividen_cash" {{ request('type') == 'dividen_cash' ? 'selected' : '' }}>Dividen
                    </option>
                    <option value="topup" {{ request('type') == 'topup' ? 'selected' : '' }}>Top Up</option>
                    <option value="tarik" {{ request('type') == 'tarik' ? 'selected' : '' }}>Tarik Dana</option>
                </select>

                <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
                    class="bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 font-bold outline-none cursor-pointer">

                @if(request()->has('type') || request()->has('date'))
                <a href="{{ route('transactions.index') }}"
                    class="text-rose-500 font-bold text-xs hover:bg-rose-50 px-3 py-2 rounded-lg transition ml-auto border border-rose-100">
                    Reset Filter ✕
                </a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500 font-extrabold tracking-wider">
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Aset / Keterangan</th>
                            <th class="px-6 py-4 text-center">Tipe</th>
                            <th class="px-6 py-4 text-right">Harga Avg</th>
                            <th class="px-6 py-4 text-center">Lot</th>
                            <th class="px-6 py-4 text-right">Gross Value</th>
                            <th class="px-6 py-4 text-right text-rose-500">Fee</th>
                            <th class="px-6 py-4 text-right">Net Total</th>
                            <th class="px-4 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50 transition duration-150 group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-slate-700 block">
                                    {{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}
                                </span>
                                <span
                                    class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">
                                    {{ $trx->account->name ?? '-' }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                @if($trx->product)
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs mr-3 border border-indigo-100">
                                        {{ substr($trx->product->code, 0, 4) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-slate-800">{{ $trx->product->code }}</div>
                                        <div class="text-xs text-slate-500 truncate max-w-[150px]">
                                            {{ $trx->product->name }}</div>
                                    </div>
                                </div>
                                @else
                                <div class="text-sm font-bold text-slate-700">
                                    {{ ucwords(str_replace('_', ' ', $trx->type)) }}</div>
                                <div class="text-xs text-slate-400">Mutasi Saldo</div>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wide border
                                    {{ $trx->type == 'beli' ? 'bg-rose-50 text-rose-600 border-rose-100' : '' }}
                                    {{ $trx->type == 'jual' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : '' }}
                                    {{ $trx->type == 'topup' ? 'bg-blue-50 text-blue-600 border-blue-100' : '' }}
                                    {{ $trx->type == 'dividen_cash' ? 'bg-yellow-50 text-yellow-600 border-yellow-100' : '' }}">
                                    {{ $trx->type }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right text-sm font-medium text-slate-600">
                                @if($trx->price_per_unit > 0)
                                {{ number_format($trx->price_per_unit, 0, ',', '.') }}
                                @else
                                -
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center text-sm font-bold text-slate-700">
                                @if($trx->amount > 0)
                                {{ $trx->amount + 0 }}
                                @else
                                -
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right text-sm text-slate-500">
                                @if(in_array($trx->type, ['beli', 'jual']) && $trx->amount > 0)
                                {{ number_format($trx->amount * $trx->price_per_unit, 0, ',', '.') }}
                                @else
                                -
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right text-sm font-medium text-rose-500">
                                @if($trx->fee > 0)
                                ({{ number_format($trx->fee, 0, ',', '.') }})
                                @else
                                -
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <span
                                    class="text-sm font-black {{ in_array($trx->type, ['beli', 'tarik']) ? 'text-slate-800' : 'text-emerald-600' }}">
                                    {{ in_array($trx->type, ['beli', 'tarik']) ? '-' : '+' }}
                                    {{ number_format($trx->total_value, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="px-4 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('transactions.edit', $trx->id) }}"
                                        class="text-slate-300 hover:text-indigo-600 transition p-2 rounded-full hover:bg-indigo-50"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus transaksi ini? Saldo akan dikembalikan.')">
                                        @csrf @method('DELETE')
                                        <button
                                            class="text-slate-300 hover:text-rose-500 transition p-2 rounded-full hover:bg-rose-50"
                                            title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-receipt text-4xl mb-3 text-slate-200"></i>
                                    <p>Belum ada data transaksi.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $transactions->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <div x-show="showModal" x-cloak
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4"
        x-transition.opacity>
        <div @click.away="showModal = false"
            class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden relative max-h-[90vh] overflow-y-auto custom-scrollbar">

            <div class="px-8 pt-8 pb-4 flex justify-between items-center bg-white sticky top-0 z-10">
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Transaksi Baru</h3>
                    <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-wide">Input Data Aktivitas</p>
                </div>
                <button @click="showModal = false"
                    class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition flex items-center justify-center">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form action="{{ route('transactions.store') }}" method="POST" class="px-8 pb-8 space-y-6">
                @csrf

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Jenis
                        Aktivitas</label>
                    <div class="relative">
                        <select name="type" x-model="type"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-4 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer">
                            <optgroup label="Pasar Modal">
                                <option value="beli">📉 Beli (Buy)</option>
                                <option value="jual">📈 Jual (Sell)</option>
                            </optgroup>
                            <optgroup label="Kas & RDN">
                                <option value="topup">💰 Top Up Saldo</option>
                                <option value="tarik">💸 Tarik Dana</option>
                                <option value="dividen_cash">🎁 Dividen Tunai</option>
                            </optgroup>
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i></div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Akun RDN</label>
                    <div class="relative">
                        <select name="account_id"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-4 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer">
                            @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Sal: Rp
                                {{ number_format($acc->balance, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                            <i class="fas fa-wallet text-xs"></i></div>
                    </div>
                </div>

                <div x-show="!['topup', 'tarik'].includes(type)">
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Produk
                        Investasi</label>
                    <div class="relative">
                        <select name="product_id"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-4 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer">
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                            <i class="fas fa-chart-line text-xs"></i></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Tanggal</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                    <div x-show="['beli', 'jual'].includes(type)">
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Jumlah
                            Unit</label>
                        <input type="number" step="any" name="amount" placeholder="0"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5"
                    x-show="['beli', 'jual', 'topup', 'tarik', 'dividen_cash'].includes(type)">
                    <div :class="{'col-span-2': ['topup', 'tarik', 'dividen_cash'].includes(type)}">
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">
                            <span
                                x-text="['topup', 'tarik', 'dividen_cash'].includes(type) ? 'Nominal Total (Rp)' : 'Harga / Unit (Rp)'"></span>
                        </label>
                        <input type="number" step="any" name="price" placeholder="0"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>

                    <div x-show="['beli', 'jual'].includes(type)">
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Biaya
                            (Fee)</label>
                        <input type="number" name="fee" placeholder="0"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 font-bold text-slate-700 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 text-white font-bold text-lg py-4 rounded-2xl shadow-xl shadow-indigo-500/30 hover:bg-indigo-700 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                    Simpan Transaksi
                </button>
            </form>
        </div>
    </div>
</div>
@endsection