<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Transaksi - InvestApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-slate-50 font-sans">
    <div class="flex h-screen overflow-hidden">
        <x-sidebar />
        <main class="flex-1 overflow-y-auto p-8">
            <div class="max-w-2xl mx-auto">
                <h1 class="text-2xl font-bold text-slate-800 mb-6">✏️ Edit Transaksi</h1>

                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100"
                    x-data="{ transactionType: '{{ $transaction->type }}' }">

                    <form action="{{ route('transactions.update', $transaction->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Akun RDN</label>
                                <select name="account_id"
                                    class="w-full border p-2.5 rounded-xl bg-gray-50 outline-none">
                                    @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}"
                                        {{ $transaction->account_id == $acc->id ? 'selected' : '' }}>
                                        {{ $acc->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Jenis</label>
                                    <select name="type" x-model="transactionType"
                                        class="w-full border p-2.5 rounded-xl bg-gray-50 outline-none">
                                        <option value="beli">Beli</option>
                                        <option value="jual">Jual</option>
                                        <option value="topup">Top Up</option>
                                        <option value="tarik">Tarik Dana</option>
                                        <option value="dividen_cash">Dividen Tunai</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal</label>
                                    <input type="date" name="transaction_date"
                                        value="{{ $transaction->transaction_date }}"
                                        class="w-full border p-2.5 rounded-xl outline-none">
                                </div>
                            </div>

                            <div x-show="['beli', 'jual', 'dividen_cash'].includes(transactionType)">
                                <label class="block text-sm font-bold text-slate-700 mb-1">Aset Investasi</label>
                                <select name="product_id"
                                    class="w-full border p-2.5 rounded-xl bg-gray-50 outline-none">
                                    <option value="">-- Pilih Aset --</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ $transaction->product_id == $product->id ? 'selected' : '' }}>
                                        {{ $product->code }} - {{ $product->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="['beli', 'jual'].includes(transactionType)">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-1">Harga / Unit</label>
                                        <input type="number" name="price_per_unit"
                                            value="{{ $transaction->price_per_unit + 0 }}"
                                            class="w-full border p-2.5 rounded-xl outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-1">Jumlah Unit</label>
                                        <input type="number" step="0.000001" name="amount"
                                            value="{{ $transaction->amount + 0 }}"
                                            class="w-full border p-2.5 rounded-xl outline-none">
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Fee (Rp)</label>
                                    <input type="number" name="fee" value="{{ $transaction->fee + 0 }}"
                                        class="w-full border p-2.5 rounded-xl outline-none">
                                </div>
                            </div>

                            <div x-show="['topup', 'tarik', 'dividen_cash'].includes(transactionType)">
                                <label class="block text-sm font-bold text-slate-700 mb-1">Total Rupiah</label>
                                <input type="number" name="total_value" value="{{ $transaction->total_value + 0 }}"
                                    class="w-full border p-2.5 rounded-xl outline-none font-bold text-teal-600">
                            </div>

                            <div class="flex gap-3 pt-6">
                                <a href="{{ route('transactions.index') }}"
                                    class="flex-1 text-center py-3 border rounded-xl font-bold text-gray-500">Batal</a>
                                <button type="submit"
                                    class="flex-1 py-3 bg-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-indigo-200">Simpan
                                    Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>

</html>