@extends('layouts.app')

@section('title', 'Transaksi Investasi - Porto Tracking')

@section('content')
<div class="h-full overflow-y-auto pt-20 md:pt-8 pb-10 px-4" x-data="{ showModal: false, type: 'beli' }">
    <div class="max-w-6xl mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                    <span class="text-orange-500">📜</span> Riwayat Transaksi
                </h1>
                <p class="text-slate-500 font-medium mt-1">Pantau setiap pergerakan jual-beli asetmu.</p>
            </div>
            <button @click="showModal = true"
                class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 hover:scale-105 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Transaksi Baru
            </button>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6">
            <form action="{{ route('transactions.index') }}" method="GET" class="flex flex-wrap gap-3 items-center">
                <select name="type" onchange="this.form.submit()"
                    class="bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 font-bold outline-none cursor-pointer">
                    <option value="">Semua Tipe</option>
                    <option value="beli" {{ request('type') == 'beli' ? 'selected' : '' }}>Pembelian</option>
                    <option value="jual" {{ request('type') == 'jual' ? 'selected' : '' }}>Penjualan</option>
                    <option value="topup" {{ request('type') == 'topup' ? 'selected' : '' }}>Top Up</option>
                    <option value="dividen_cash" {{ request('type') == 'dividen_cash' ? 'selected' : '' }}>Dividen
                    </option>
                    <option value="topup" {{ request('type') == 'topup' ? 'selected' : '' }}>Top Up</option>
                    <option value="tarik" {{ request('type') == 'tarik' ? 'selected' : '' }}>Tarik Dana</option>
                </select>

                <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
                    class="bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 font-bold outline-none cursor-pointer">

                @if(request()->has('type') || request()->has('date') || request()->has('account_id'))
                <a href="{{ route('transactions.index') }}"
                    class="text-rose-500 font-bold text-xs hover:bg-rose-50 px-3 py-2 rounded-lg transition ml-auto border border-rose-100">
                    Reset Filter ✕
                </a>
                @endif
            </form>
        </div>

        <div class="space-y-4">
            @forelse($transactions as $trx)
            <div
                class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition relative group">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shadow-sm
                            {{ $trx->type == 'beli' ? 'bg-blue-50 text-blue-600' : '' }}
                            {{ $trx->type == 'jual' ? 'bg-green-50 text-green-600' : '' }}
                            {{ $trx->type == 'topup' ? 'bg-emerald-50 text-emerald-600' : '' }}
                            {{ $trx->type == 'tarik' ? 'bg-red-50 text-red-600' : '' }}
                            {{ in_array($trx->type, ['dividen_cash', 'dividen_unit']) ? 'bg-yellow-50 text-yellow-600' : '' }}">

                            @if($trx->type == 'beli') <i class="fas fa-shopping-cart"></i>
                            @elseif($trx->type == 'jual') <i class="fas fa-money-bill-wave"></i>
                            @elseif($trx->type == 'topup') <i class="fas fa-wallet"></i>
                            @elseif($trx->type == 'dividen_cash') <i class="fas fa-gift"></i>
                            @else <i class="fas fa-exchange-alt"></i> @endif
                        </div>

                        <div>
                            @if($trx->product)
                            <h3 class="text-lg font-black text-slate-800">{{ $trx->product->code }}</h3>
                            <p class="text-sm font-medium text-slate-500">{{ $trx->product->name }}</p>
                            @else
                            <h3 class="text-lg font-black text-slate-800 uppercase">
                                {{ str_replace('_', ' ', $trx->type) }}</h3>
                            <p class="text-sm font-medium text-slate-500">Mutasi RDN</p>
                            @endif
                            <div class="text-xs text-slate-400 mt-1 font-bold">
                                {{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }} •
                                {{ $trx->account->name ?? 'Akun Terhapus' }}
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <span
                            class="block font-black text-lg {{ in_array($trx->type, ['beli', 'tarik']) ? 'text-slate-800' : 'text-emerald-600' }}">
                            {{ in_array($trx->type, ['beli', 'tarik']) ? '-' : '+' }} Rp
                            {{ number_format($trx->total_value, 0, ',', '.') }}
                        </span>
                        @if($trx->amount > 0)
                        <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded-lg">
                            {{ $trx->amount + 0 }} Unit @ Rp {{ number_format($trx->price_per_unit) }}
                        </span>
                        @endif
                    </div>
                </div>

                <div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                    <a href="{{ route('transactions.edit', $trx->id) }}"
                        class="bg-indigo-50 text-indigo-600 p-2 rounded-lg hover:bg-indigo-100 transition" title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST"
                        onsubmit="return confirm('Hapus transaksi ini? Saldo akan dikembalikan.')">
                        @csrf @method('DELETE')
                        <button class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition" title="Hapus">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-20">
                <div class="inline-block p-6 rounded-full bg-slate-100 mb-4 text-slate-300">
                    <i class="fas fa-receipt text-4xl"></i>
                </div>
                <p class="text-slate-500 font-bold">Belum ada transaksi.</p>
            </div>
            @endforelse

            <div class="mt-6">
                {{ $transactions->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <div x-show="showModal" x-cloak
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4"
        x-transition.opacity>
        <div @click.away="showModal = false"
            class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl overflow-hidden p-8 max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">📝 Transaksi Baru</h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-slate-600"><i
                        class="fas fa-times text-xl"></i></button>
            </div>

            <form action="{{ route('transactions.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Jenis Aktivitas</label>
                    <select name="type" x-model="type"
                        class="w-full border border-slate-200 p-3 rounded-xl bg-slate-50 font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500">
                        <optgroup label="Transaksi Pasar">
                            <option value="beli">Beli (Buy)</option>
                            <option value="jual">Jual (Sell)</option>
                        </optgroup>
                        <optgroup label="Kas & Dividen">
                            <option value="topup">Top Up RDN</option>
                            <option value="tarik">Tarik Dana (Withdraw)</option>
                            <option value="dividen_cash">💰 Dividen Tunai</option>
                        </optgroup>
                        <optgroup label="Aksi Korporasi (Unit)">
                            <option value="dividen_unit">📃 Bonus Saham / Dividen Unit</option>
                            <option value="stock_split">✂️ Stock Split (Tambah Unit)</option>
                            <option value="reverse_split">🪡 Reverse Split (Kurang Unit)</option>
                            <option value="right_issue">🎟️ Right Issue (Tebus)</option>
                        </optgroup>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Akun RDN</label>
                    <select name="account_id"
                        class="w-full border border-slate-200 p-3 rounded-xl font-medium outline-none focus:border-indigo-500"
                        required>
                        @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }} (Sal: {{ number_format($acc->balance) }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="!['topup', 'tarik'].includes(type)">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Produk Investasi</label>
                    <select name="product_id"
                        class="w-full border border-slate-200 p-3 rounded-xl font-medium outline-none focus:border-indigo-500">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Tanggal</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}"
                        class="w-full border border-slate-200 p-3 rounded-xl outline-none focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-4">

                    <div
                        x-show="['beli', 'jual', 'dividen_unit', 'stock_split', 'reverse_split', 'right_issue'].includes(type)">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">
                            <span
                                x-text="type == 'stock_split' || type == 'dividen_unit' ? 'Tambahan Unit' : (type == 'reverse_split' ? 'Pengurangan Unit' : 'Jml Unit')"></span>
                        </label>
                        <input type="number" step="any" name="amount" placeholder="0"
                            class="w-full border border-slate-200 p-3 rounded-xl outline-none focus:border-indigo-500">
                    </div>

                    <div x-show="['beli', 'jual', 'topup', 'tarik', 'dividen_cash', 'right_issue'].includes(type)"
                        :class="{'col-span-2': ['topup', 'tarik', 'dividen_cash'].includes(type)}">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">
                            <span
                                x-text="['topup', 'tarik', 'dividen_cash'].includes(type) ? 'Nominal Total (Rp)' : 'Harga / Unit (Rp)'"></span>
                        </label>
                        <input type="number" step="any" name="price" placeholder="0"
                            class="w-full border border-slate-200 p-3 rounded-xl outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div x-show="['beli', 'jual', 'right_issue'].includes(type)">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Biaya (Fee)</label>
                    <input type="number" name="fee" placeholder="0"
                        class="w-full border border-slate-200 p-3 rounded-xl outline-none focus:border-indigo-500">
                </div>

                <div x-show="['stock_split', 'dividen_unit'].includes(type)"
                    class="bg-blue-50 p-3 rounded-xl border border-blue-100 flex items-start gap-2">
                    <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                    <p class="text-xs text-blue-600">Transaksi ini hanya menambah jumlah unit asetmu tanpa mengurangi
                        saldo RDN.</p>
                </div>
                </template>

                <button type="submit"
                    class="w-full bg-indigo-600 text-white font-bold py-4 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition transform hover:-translate-y-1 mt-4">
                    Simpan Transaksi
                </button>
            </form>
        </div>
    </div>
</div>
</div>
@endsection