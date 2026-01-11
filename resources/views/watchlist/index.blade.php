@extends('layouts.app')

@section('title', 'Watchlist - Porto Tracking')

@section('content')
<div class="h-full overflow-y-auto pt-20 md:pt-8 pb-10 px-4">
    <div class="max-w-[1600px] mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="text-yellow-500">🎯</span> Watchlist Saham
                </h1>
                <p class="text-slate-500 font-medium mt-1">Daftar incaran aset investasi masa depanmu.</p>
            </div>

            <button onclick="document.getElementById('addModal').classList.remove('hidden')"
                class="bg-[#4F46E5] text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:scale-105 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Incaran
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            @forelse($watchlists as $item)
            @php
            $isBuyable = $item->current_price <= $item->target_price && $item->current_price > 0;
                $percent = ($item->current_price > 0) ? ($item->target_price / $item->current_price) * 100 : 0;
                @endphp

                <div
                    class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 relative group flex flex-col justify-between h-full">

                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="bg-indigo-50 text-indigo-700 h-12 w-12 flex items-center justify-center rounded-2xl font-black text-sm border border-indigo-100">
                                    {{ Str::limit($item->code, 4, '') }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg text-slate-800">{{ $item->code }}</h3>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                        {{ Str::limit($item->name, 15) }}</p>
                                </div>
                            </div>

                            <form action="{{ route('watchlist.destroy', $item->id) }}" method="POST"
                                onsubmit="return confirm('Hapus dari watchlist?');">
                                @csrf @method('DELETE')
                                <button
                                    class="text-slate-300 hover:text-red-500 p-2 rounded-xl hover:bg-red-50 transition">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <div class="bg-emerald-50 p-3 rounded-2xl border border-emerald-100 text-center">
                                <p class="text-[10px] text-emerald-600 uppercase font-bold tracking-wider mb-1">Target
                                    Beli</p>
                                <p class="text-base font-black text-emerald-700">
                                    {{ number_format($item->target_price) }}</p>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-center">
                                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Harga
                                    Pasar</p>
                                <p class="text-base font-bold text-slate-700">{{ number_format($item->current_price) }}
                                </p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="flex justify-between text-xs font-bold mb-2">
                                <span class="text-slate-400">Status</span>
                                @if($isBuyable)
                                <span class="text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded text-[10px]">SIKAT!
                                    🚀</span>
                                @else
                                <span class="text-orange-400 bg-orange-50 px-2 py-0.5 rounded text-[10px]">WAIT</span>
                                @endif
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="h-full transition-all duration-1000 {{ $isBuyable ? 'bg-emerald-500 w-full' : 'bg-orange-400' }}"
                                    style="width: {{ $isBuyable ? '100%' : min($percent, 80).'%' }}"></div>
                            </div>
                        </div>
                    </div>

                    @if($item->note)
                    <div class="mt-4 pt-4 border-t border-slate-50">
                        <p class="text-xs text-slate-500 italic line-clamp-2">
                            <span class="not-italic mr-1">📝</span> {{ $item->note }}
                        </p>
                    </div>
                    @endif

                </div>
                @empty
                <div class="col-span-full py-20 text-center">
                    <div class="inline-block p-6 rounded-full bg-slate-100 mb-4 text-slate-300">
                        <i class="fas fa-search text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">Watchlist Kosong</h3>
                    <p class="text-slate-400 text-sm">Mulai pantau saham incaranmu sekarang.</p>
                </div>
                @endforelse
        </div>

    </div>
</div>

<div id="addModal"
    class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-[2rem] w-full max-w-lg p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-slate-800">Tambah Incaran</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('watchlist.store') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Kode</label>
                    <input type="text" name="code" placeholder="BBCA" required
                        class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl font-bold uppercase focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Nama Aset</label>
                    <input type="text" name="name" placeholder="Bank Central Asia"
                        class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl font-medium focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-emerald-600 uppercase ml-1">Target Beli</label>
                    <input type="number" name="target_price" placeholder="0" required
                        class="w-full bg-emerald-50 border border-emerald-100 text-emerald-700 p-3.5 rounded-xl font-bold focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Harga Sekarang</label>
                    <input type="number" name="current_price" placeholder="0"
                        class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl font-bold text-slate-600 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Catatan Strategi</label>
                <textarea name="note" placeholder="Tunggu breakout di 9200..."
                    class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl font-medium text-slate-700 text-sm h-24 focus:ring-2 focus:ring-indigo-500 outline-none resize-none"></textarea>
            </div>

            <button type="submit"
                class="w-full bg-[#4F46E5] text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition">
                Simpan ke Watchlist
            </button>
        </form>
    </div>
</div>
@endsection