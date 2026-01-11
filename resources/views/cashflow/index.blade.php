<!DOCTYPE html>
<html lang="id">

<head>
    <title>Cash Flow - InvestApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    [x-cloak] {
        display: none !important;
    }
    </style>
</head>

<body class="bg-slate-50 text-slate-800" x-data="{ showModal: false }">

    <div class="flex h-screen overflow-hidden">

        <!-- Pastikan komponen sidebar ada -->
        @if(View::exists('components.sidebar'))
        <x-sidebar />
        @endif

        <main class="flex-1 overflow-y-auto p-6 md:p-10 relative">

            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div class="w-full md:w-auto">
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>💸</span> Arus Kas Bulanan
                    </h1>
                    <p class="text-slate-500 mt-2 font-medium">Monitor cashflow untuk maksimalkan investasi.</p>
                </div>

                <button @click="showModal = true"
                    class="w-full md:w-auto bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:scale-105 transition transform duration-200 flex items-center justify-center gap-2 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Catat Transaksi
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 items-stretch">

                <div
                    class="bg-gradient-to-br from-emerald-500 to-emerald-600 text-white p-6 rounded-4xl shadow-xl shadow-emerald-200 relative overflow-hidden h-full flex flex-col justify-between">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                    <div>
                        <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">Pemasukan (Bulan
                            Ini)</p>
                        <h3 class="text-2xl lg:text-3xl font-black tracking-tight">+ Rp
                            {{ number_format($income ?? 0, 0, ',', '.') }}</h3>
                    </div>
                </div>

                <div
                    class="bg-white text-slate-800 p-6 rounded-4xl shadow-sm border border-slate-100 h-full flex flex-col justify-between">
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
                    class="bg-white p-6 rounded-4xl shadow-sm border border-slate-100 h-full flex flex-col justify-between">
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
                        <h3
                            class="text-4xl font-black {{ $savingsRate >= 20 ? 'text-indigo-600' : 'text-orange-500' }}">
                            {{ number_format($savingsRate, 1) }}<span class="text-xl text-slate-400">%</span>
                        </h3>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full mt-3 overflow-hidden">
                        <div class="h-full {{ $savingsRate >= 20 ? 'bg-indigo-600' : 'bg-orange-500' }}"
                            style="width: {{ $widthPercentage }}%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-4xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-slate-800">Riwayat Transaksi</h3>
                    <span
                        class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">{{ count($flows ?? []) }}
                        Data</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50/50 text-slate-500 text-xs uppercase font-bold tracking-wider">
                            <tr>
                                <th class="p-5 pl-8">Tanggal</th>
                                <th class="p-5">Kategori</th>
                                <th class="p-5">Keterangan</th>
                                <th class="p-5 text-right">Nominal</th>
                                <th class="p-5 pr-8"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($flows ?? [] as $flow)
                            <tr class="hover:bg-slate-50/80 transition duration-150 group">
                                <td class="p-5 pl-8 whitespace-nowrap">
                                    <div class="font-bold text-slate-700">{{ $flow->date->format('d') }}</div>
                                    <div class="text-xs text-slate-400 uppercase font-bold">
                                        {{ $flow->date->format('M Y') }}</div>
                                </td>
                                <td class="p-5">
                                    <span
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold inline-flex items-center gap-1.5
                                        {{ $flow->type == 'income' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                                        {{ $flow->type == 'income' ? '💰' : '💸' }}
                                        {{ $flow->category }}
                                    </span>
                                </td>
                                <td class="p-5 text-sm font-medium text-slate-500 max-w-xs truncate">
                                    {{ $flow->description ?? '-' }}</td>
                                <td
                                    class="p-5 text-right font-bold whitespace-nowrap {{ $flow->type == 'income' ? 'text-emerald-600' : 'text-slate-800' }}">
                                    {{ $flow->type == 'income' ? '+' : '-' }} Rp
                                    {{ number_format($flow->amount, 0, ',', '.') }}
                                </td>
                                <td class="p-5 pr-8 text-right">
                                    <form action="{{ route('cashflow.destroy', $flow->id) }}" method="POST"
                                        class="inline-block opacity-0 group-hover:opacity-100 transition-opacity"
                                        onsubmit="return confirm('Hapus transaksi ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-slate-300 hover:text-red-500 p-2 rounded hover:bg-red-50 transition"
                                            title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-indigo-50 p-4 rounded-full mb-4 animate-pulse">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-400"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </div>
                                        <p class="text-slate-900 font-bold text-lg">Belum ada transaksi</p>
                                        <p class="text-slate-400 text-sm mb-4">Mulai catat pemasukan dan pengeluaranmu.
                                        </p>
                                        <button @click="showModal = true"
                                            class="text-indigo-600 font-bold text-sm hover:underline">
                                            + Tambah Sekarang
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <div x-show="showModal" x-cloak x-transition
        class="fixed inset-0 z-50 bg-slate-900/40 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
        <div @click.away="showModal = false" class="bg-white rounded-4xl w-full max-w-lg p-8 shadow-2xl">
            <h3 class="text-2xl font-bold mb-6 text-slate-800">Catat Transaksi Baru</h3>
            <form action="{{ route('cashflow.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid grid-cols-2 gap-4 p-1 bg-slate-100 rounded-xl" x-data="{ type: 'expense' }">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="income" class="peer sr-only" x-model="type">
                        <div
                            class="text-center py-3 rounded-lg font-bold text-sm text-slate-500 peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm transition-all">
                            💰 Pemasukan
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="expense" class="peer sr-only" x-model="type">
                        <div
                            class="text-center py-3 rounded-lg font-bold text-sm text-slate-500 peer-checked:bg-white peer-checked:text-red-500 peer-checked:shadow-sm transition-all">
                            💸 Pengeluaran
                        </div>
                    </label>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Tanggal</label>
                    <input type="date" name="date"
                        class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 transition"
                        value="{{ date('Y-m-d') }}">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Kategori & Nominal</label>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="category" placeholder="Contoh: Makan"
                            class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 transition"
                            required>
                        <input type="number" name="amount" placeholder="Rp 0"
                            class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 transition"
                            required>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Keterangan</label>
                    <input type="text" name="description" placeholder="Catatan tambahan..."
                        class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl font-medium text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                <button type="submit"
                    class="w-full bg-slate-900 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-slate-800 hover:-translate-y-0.5 transition duration-200">
                    Simpan Transaksi
                </button>
            </form>
        </div>
    </div>

    <script>
    // Tambahkan konfirmasi sebelum menghapus
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('form[action*="cashflow.destroy"]');

        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Yakin ingin menghapus transaksi ini?')) {
                    e.preventDefault();
                }
            });
        });
    });
    </script>
</body>

</html>