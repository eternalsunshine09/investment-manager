<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Investment Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
    /* From Uiverse.io by vinodjangid07 - Modified for Edit button */
    .edit-btn {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        width: 100px;
        height: 40px;
        border: none;
        padding: 0px 20px;
        background-color: rgb(124, 58, 237);
        /* Indigo color */
        color: white;
        font-weight: 500;
        cursor: pointer;
        border-radius: 10px;
        box-shadow: 5px 5px 0px rgb(99, 102, 241);
        /* Indigo-500 */
        transition-duration: .3s;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
    }

    .edit-btn .svg {
        width: 13px;
        position: absolute;
        right: 0;
        margin-right: 20px;
        fill: white;
        transition-duration: .3s;
    }

    .edit-btn:hover {
        color: transparent;
        background-color: rgb(109, 40, 217);
        /* Darker indigo on hover */
    }

    .edit-btn:hover .svg {
        right: 43%;
        margin: 0;
        padding: 0;
        border: none;
        transition-duration: .3s;
        width: 18px;
        /* Slightly bigger on hover */
    }

    .edit-btn:active {
        transform: translate(3px, 3px);
        transition-duration: .3s;
        box-shadow: 2px 2px 0px rgb(99, 102, 241);
    }

    /* From Uiverse.io by vinodjangid07 - Modified for Delete button */
    .delete-button {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: rgb(124, 58, 237);
        border: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.164);
        cursor: pointer;
        transition-duration: 0.3s;
        overflow: hidden;
        position: relative;
        padding: 0;
    }

    .delete-button .svgIcon {
        width: 12px;
        transition-duration: 0.3s;
    }

    .delete-button .svgIcon path {
        fill: white;
    }

    .delete-button:hover {
        width: 110px;
        border-radius: 40px;
        transition-duration: 0.3s;
        background-color: rgb(255, 69, 69);
        align-items: center;
    }

    .delete-button:hover .svgIcon {
        width: 30px;
        transition-duration: 0.3s;
        transform: translateY(60%);
    }

    .delete-button::before {
        position: absolute;
        top: -15px;
        content: "Hapus";
        color: white;
        transition-duration: 0.3s;
        font-size: 2px;
        opacity: 0;
    }

    .delete-button:hover::before {
        font-size: 12px;
        opacity: 1;
        transform: translateY(20px);
        transition-duration: 0.3s;
    }
    </style>
</head>

<body class="bg-slate-50 font-sans" x-data="{ showModal: false }">

    <div class="flex h-screen overflow-hidden">
        <x-sidebar />

        <main class="flex-1 overflow-y-auto p-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800">📜 Riwayat Transaksi</h1>
                    <p class="text-gray-500 mt-1">Pantau setiap pergerakan asetmu.</p>
                </div>
                <button @click="showModal = true"
                    class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition flex items-center gap-2">
                    <span>+</span> Transaksi Baru
                </button>
            </div>

            <form action="{{ route('transactions.index') }}" method="GET" class="mb-6">
                <div class="flex flex-wrap gap-4">
                    <select name="category" onchange="this.form.submit()"
                        class="border border-gray-200 px-4 py-2 rounded-xl focus:border-indigo-500 outline-none bg-white">
                        <option value="">Semua Investasi</option>
                        <option value="saham" {{ request('category') == 'saham' ? 'selected' : '' }}>Saham</option>
                        <option value="reksadana" {{ request('category') == 'reksadana' ? 'selected' : '' }}>Reksadana
                        </option>
                        <option value="crypto" {{ request('category') == 'crypto' ? 'selected' : '' }}>Crypto</option>
                        <option value="emas" {{ request('category') == 'emas' ? 'selected' : '' }}>Emas</option>
                    </select>

                    <select name="type" onchange="this.form.submit()"
                        class="border border-gray-200 px-4 py-2 rounded-xl focus:border-indigo-500 outline-none bg-white">
                        <option value="">Semua Transaksi</option>
                        <option value="beli" {{ request('type') == 'beli' ? 'selected' : '' }}>Pembelian</option>
                        <option value="jual" {{ request('type') == 'jual' ? 'selected' : '' }}>Penjualan</option>
                        <option value="dividen_cash" {{ request('type') == 'dividen_cash' ? 'selected' : '' }}>Dividen
                            Tunai</option>
                        <option value="topup" {{ request('type') == 'topup' ? 'selected' : '' }}>Top Up</option>
                    </select>

                    <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
                        class="border border-gray-200 px-4 py-2 rounded-xl focus:border-indigo-500 outline-none bg-white text-gray-600">

                    @if(request()->has('category') || request()->has('type') || request()->has('date'))
                    <a href="{{ route('transactions.index') }}"
                        class="px-4 py-2 text-red-500 font-bold hover:bg-red-50 rounded-xl transition border border-red-100">
                        Reset ✕
                    </a>
                    @endif
                </div>
            </form>

            @if(session('error'))
            <div
                class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r flex items-center shadow-sm">
                <span class="text-xl mr-2">⛔</span>
                <p>{{ session('error') }}</p>
            </div>
            @endif

            @if(session('success'))
            <div
                class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r flex items-center shadow-sm">
                <span class="text-xl mr-2">✅</span>
                <p>{{ session('success') }}</p>
            </div>
            @endif

            <div class="space-y-4">
                @forelse($transactions as $trx)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            @if($trx->product)
                            <h3 class="text-lg font-bold text-slate-800">{{ $trx->product->name }}</h3>
                            <div class="flex items-center gap-2 text-sm text-gray-500 mt-1">
                                <span class="font-mono font-bold text-slate-600">{{ $trx->product->code }}</span>
                                <span>•</span>
                                <span class="capitalize">{{ $trx->product->category }}</span>
                                <span>•</span>
                                <span>{{ date('d M Y', strtotime($trx->transaction_date)) }}</span>
                            </div>
                            @else
                            <h3 class="text-lg font-bold text-slate-800 uppercase">{{ $trx->type }} RDN</h3>
                            <div class="text-sm text-gray-500 mt-1">
                                {{ date('d M Y', strtotime($trx->transaction_date)) }}</div>
                            @endif
                        </div>

                        <div class="text-right">
                            <span
                                class="font-bold text-lg 
                                {{ ($trx->type == 'beli' || $trx->type == 'tarik') ? 'text-blue-600' : 'text-green-600' }}">
                                {{ ucfirst($trx->type) }}
                            </span>
                            @if($trx->amount > 0)
                            <div class="text-sm text-gray-400">{{ $trx->amount + 0 }} unit</div>
                            @endif
                        </div>
                    </div>

                    <hr class="border-gray-100 mb-4">

                    <div class="grid grid-cols-3 gap-4">
                        @if($trx->price_per_unit > 0)
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Harga per Unit</p>
                            <p class="font-bold text-slate-700">Rp
                                {{ number_format($trx->price_per_unit, 0, ',', '.') }}</p>
                        </div>
                        @else
                        <div></div>
                        @endif

                        <div>
                            <p class="text-xs text-gray-400 mb-1">Fee / Biaya</p>
                            <p class="font-bold text-slate-700">Rp {{ number_format($trx->fee, 0, ',', '.') }}</p>
                        </div>

                        <div class="text-right">
                            <p class="text-xs text-gray-400 mb-1">Total Nilai</p>
                            <p class="font-bold text-lg text-slate-800">Rp
                                {{ number_format($trx->total_value, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center">
                        <a href="{{ route('transactions.edit', $trx->id) }}" class="edit-btn">
                            Edit
                            <svg class="svg" viewBox="0 0 512 512">
                                <path
                                    d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z">
                                </path>
                            </svg>
                        </a>

                        <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus transaksi ini? Saldo RDN akan dikembalikan otomatis.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-button" title="Hapus transaksi">
                                <svg viewBox="0 0 448 512" class="svgIcon">
                                    <path
                                        d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z">
                                    </path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-10">
                    <p class="text-gray-400">Belum ada data transaksi.</p>
                </div>
                @endforelse
            </div>
        </main>
    </div>

    <div x-show="showModal"
        class="fixed inset-0 bg-slate-900 bg-opacity-50 z-50 flex items-center justify-center p-4 backdrop-blur-sm"
        style="display: none;" x-transition.opacity>
        <div @click.away="showModal = false" class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden p-6"
            x-data="{ type: 'beli' }">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">📝 Catat Transaksi / Aksi Korporasi</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-red-500 transition">✕</button>
            </div>

            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Jenis Aktivitas</label>
                        <select name="type" x-model="type"
                            class="w-full border border-gray-200 p-2.5 rounded-xl bg-gray-50 focus:bg-white outline-none">
                            <optgroup label="Transaksi Dasar">
                                <option value="beli">Beli (Buy)</option>
                                <option value="jual">Jual (Sell)</option>
                                <option value="topup">Top Up RDN</option>
                                <option value="tarik">Tarik Dana (Withdraw)</option>
                            </optgroup>
                            <optgroup label="Dividen & Bunga">
                                <option value="dividen_cash">💰 Dividen Tunai</option>
                                <option value="dividen_unit">📃 Dividen Unit / Bonus Saham</option>
                            </optgroup>
                            <optgroup label="Corporate Action">
                                <option value="stock_split">✂️ Stock Split (Tambah Unit)</option>
                                <option value="reverse_split">🪡 Reverse Split (Kurang Unit)</option>
                                <option value="right_issue">🎟️ Right Issue (Tebus)</option>
                            </optgroup>
                        </select>
                    </div>

                    <div x-show="['beli', 'jual', 'topup', 'tarik', 'dividen_cash', 'right_issue'].includes(type)">
                        <label class="block text-sm font-bold text-slate-700 mb-1">Akun / RDN</label>
                        <select name="account_id"
                            class="w-full border border-gray-200 p-2.5 rounded-xl bg-white outline-none">
                            <option value="">Pilih Akun...</option>
                            @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Sal: {{ number_format($acc->balance) }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="!['topup', 'tarik'].includes(type)">
                        <label class="block text-sm font-bold text-slate-700 mb-1">Produk Investasi</label>
                        <select name="product_id"
                            class="w-full border border-gray-200 p-2.5 rounded-xl bg-white outline-none">
                            <option value="">Pilih Produk...</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}"
                            class="w-full border border-gray-200 p-2.5 rounded-xl outline-none">
                    </div>

                    <div
                        x-show="['beli', 'jual', 'dividen_unit', 'stock_split', 'reverse_split', 'right_issue'].includes(type)">
                        <label class="block text-sm font-bold text-slate-700 mb-1"
                            x-text="type == 'stock_split' ? 'Tambahan Unit Baru' : (type == 'reverse_split' ? 'Pengurangan Unit' : 'Jumlah Unit')"></label>
                        <input type="number" step="any" name="amount" placeholder="0"
                            class="w-full border border-gray-200 p-2.5 rounded-xl outline-none">
                        <p x-show="type == 'stock_split'" class="text-xs text-gray-400 mt-1">*Masukkan selisih unit yang
                            bertambah.</p>
                    </div>

                    <div x-show="['beli', 'jual', 'topup', 'tarik', 'dividen_cash', 'right_issue'].includes(type)">
                        <label class="block text-sm font-bold text-slate-700 mb-1"
                            x-text="['dividen_cash'].includes(type) ? 'Total Dividen Diterima (Rp)' : (['topup','tarik'].includes(type) ? 'Nominal (Rp)' : 'Harga Per Unit (Rp)')"></label>
                        <input type="number" step="any" name="price" placeholder="0"
                            class="w-full border border-gray-200 p-2.5 rounded-xl outline-none">
                    </div>

                    <div x-show="['beli', 'jual', 'right_issue'].includes(type)">
                        <label class="block text-sm font-bold text-slate-700 mb-1">Fee / Biaya (Opsional)</label>
                        <input type="number" name="fee" placeholder="0"
                            class="w-full border border-gray-200 p-2.5 rounded-xl outline-none">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" @click="showModal = false"
                        class="text-gray-500 font-bold px-4 py-2 hover:bg-gray-100 rounded-lg">Batal</button>
                    <button type="submit"
                        class="bg-indigo-600 text-white font-bold px-6 py-2 rounded-lg hover:bg-indigo-700 shadow-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Tambahkan konfirmasi khusus untuk delete button
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-button');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('form');
                if (!confirm(
                        'Yakin ingin menghapus transaksi ini?\nSaldo RDN akan dikembalikan otomatis.'
                    )) {
                    e.preventDefault();
                }
            });
        });
    });
    </script>

</body>

</html>