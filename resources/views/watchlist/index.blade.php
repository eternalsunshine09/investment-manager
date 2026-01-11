<!DOCTYPE html>
<html lang="id">

<head>
    <title>Watchlist - InvestApp</title>
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

        <x-sidebar />

        <main class="flex-1 overflow-y-auto p-6 md:p-10 relative">

            <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4">
                <div class="w-full md:w-auto">
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>🎯</span> Watchlist Saham
                    </h1>
                    <p class="text-slate-500 mt-2 font-medium">Incaran aset investasi untuk masa depan.</p>
                </div>

                <button @click="showModal = true"
                    class="w-full md:w-auto bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:scale-105 transition transform duration-200 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Tambah Incaran
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($watchlists as $item)
                @php
                // Logic sederhana untuk visualisasi progress
                $isBuyable = $item->current_price <= $item->target_price && $item->current_price > 0;
                    // Menghitung persentase bar (cuma visual)
                    $percent = 0;
                    if($item->current_price > 0) {
                    $percent = ($item->target_price / $item->current_price) * 100;
                    }
                    @endphp

                    <div
                        class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 relative group flex flex-col h-full justify-between">

                        <div>
                            <div class="flex justify-between items-start mb-6">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="bg-indigo-50 text-indigo-700 h-14 w-14 flex items-center justify-center rounded-2xl font-black text-sm border border-indigo-100 shadow-sm">
                                        {{ Str::limit($item->code, 4, '') }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg text-slate-800">{{ $item->code }}</h3>
                                        <p class="text-xs text-slate-500 font-medium">
                                            {{ Str::limit($item->name ?? 'Aset Investasi', 20) }}</p>
                                    </div>
                                </div>

                                <form action="{{ route('watchlist.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus pantauan ini?');">
                                    @csrf @method('DELETE')
                                    <button
                                        class="text-slate-300 hover:text-red-500 p-2 rounded-xl hover:bg-red-50 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-6">
                                <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100">
                                    <p class="text-[10px] text-emerald-600 uppercase font-bold tracking-wider mb-1">
                                        Target Beli</p>
                                    <p class="text-lg font-black text-emerald-700">Rp
                                        {{ number_format($item->target_price, 0, ',', '.') }}</p>
                                </div>
                                <div class="p-4 rounded-2xl border border-slate-50 bg-slate-50">
                                    <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Pasar
                                    </p>
                                    <p class="text-lg font-bold text-slate-700">Rp
                                        {{ number_format($item->current_price, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="flex justify-between text-xs font-bold mb-2">
                                    <span class="text-slate-400">Status Harga</span>
                                    @if($isBuyable)
                                    <span class="text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded text-[10px]">SIKAT!
                                        🚀</span>
                                    @else
                                    <span class="text-orange-400 bg-orange-50 px-2 py-0.5 rounded text-[10px]">WAIT &
                                        SEE</span>
                                    @endif
                                </div>
                                <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                    <div class="h-full transition-all duration-1000 {{ $isBuyable ? 'bg-emerald-500 w-full' : 'bg-orange-400' }}"
                                        style="width: {{ $isBuyable ? '100%' : min($percent, 80).'%' }}"></div>
                                </div>
                            </div>
                        </div>

                        @if($item->note)
                        <div class="mt-2 pt-4 border-t border-slate-50">
                            <p class="text-xs text-slate-500 italic line-clamp-2">
                                <span class="font-bold text-slate-400 not-italic mr-1">📝</span>
                                {{ $item->note }}
                            </p>
                        </div>
                        @endif

                        @if($isBuyable)
                        <span class="absolute top-6 right-14 flex h-3 w-3">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                        </span>
                        @endif

                    </div>

                    @empty
                    <div class="col-span-full py-20 text-center">
                        <div
                            class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 rounded-[3rem] p-10 bg-slate-50/50">
                            <div class="bg-white p-4 rounded-full mb-4 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-300" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-700">Watchlist Kosong</h3>
                            <p class="text-slate-400 text-sm mt-1 mb-4">Mulai pantau saham incaranmu sekarang.</p>
                            <button @click="showModal = true" class="text-indigo-600 font-bold text-sm hover:underline">
                                + Tambah Saham
                            </button>
                        </div>
                    </div>
                    @endforelse
            </div>
        </main>
    </div>

    <div x-show="showModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm transition-opacity">
        <div @click.away="showModal = false"
            class="bg-white rounded-[2rem] w-full max-w-lg p-8 shadow-2xl transform transition-all">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-slate-800">Tambah Incaran</h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('watchlist.store') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">Kode</label>
                        <input type="text" name="code" placeholder="BBCA"
                            class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl font-bold uppercase focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition text-slate-900"
                            required>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">Nama Aset</label>
                        <input type="text" name="name" placeholder="Bank Central Asia"
                            class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl font-medium focus:ring-2 focus:ring-indigo-500 outline-none transition text-slate-900">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-emerald-600 uppercase ml-1">Target Beli</label>
                        <input type="number" name="target_price" placeholder="0"
                            class="w-full bg-emerald-50 border border-emerald-100 text-emerald-700 p-3.5 rounded-xl font-bold focus:ring-2 focus:ring-emerald-500 outline-none transition"
                            required>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">Harga Sekarang</label>
                        <input type="number" name="current_price" placeholder="0"
                            class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl font-bold text-slate-600 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Catatan Strategi</label>
                    <textarea name="note" placeholder="Tunggu breakout di 9200..."
                        class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl font-medium text-slate-700 text-sm h-24 focus:ring-2 focus:ring-indigo-500 outline-none transition resize-none"></textarea>
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-0.5 transition duration-200">
                    Simpan ke Watchlist
                </button>
            </form>
        </div>
    </div>

</body>

</html>