<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekapan Aset - InvestApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 font-sans">
    <div class="flex h-screen overflow-hidden">

        <x-sidebar />

        <main class="flex-1 overflow-y-auto p-8 flex flex-col">

            <div class="flex justify-between items-center mb-10 w-full">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-2">
                        📝 Rekapan & Update Aset
                    </h1>
                    <p class="text-gray-500 mt-1">Perbarui harga pasar secara berkala agar nilai portofolio akurat.</p>
                </div>

                <button type="submit" form="priceForm"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition flex items-center gap-2">
                    💾 Simpan Perubahan
                </button>
            </div>

            @if(session('success'))
            <div
                class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r flex items-center shadow-sm">
                <span class="text-xl mr-2">✅</span>
                <p>{{ session('success') }}</p>
            </div>
            @endif

            <form id="priceForm" action="{{ route('portfolio.update') }}" method="POST">
                @csrf

                @if(isset($portfolio) && $portfolio->isEmpty())
                <div class="bg-white p-10 rounded-2xl shadow-sm border border-gray-100 text-center py-24">
                    <span class="text-6xl">📭</span>
                    <h3 class="text-xl font-bold text-slate-700 mt-4">Aset Kosong</h3>
                    <p class="text-gray-400">Kamu belum memiliki aset aktif untuk diupdate.</p>
                </div>
                @elseif(isset($portfolio))

                @foreach($portfolio as $category => $items)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">

                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3
                            class="text-lg font-extrabold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                            @if($category == 'saham') 🏢
                            @elseif($category == 'reksadana') 📃
                            @elseif($category == 'crypto') 🪙
                            @elseif($category == 'emas') 🥇
                            @else 📦 @endif
                            {{ $category }}
                        </h3>
                        <div class="flex gap-3 text-xs font-bold">
                            <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded-full">
                                Modal: Rp {{ number_format($items->sum('total_cost'), 0, ',', '.') }}
                            </span>
                            <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full">
                                Nilai Sekarang: Rp {{ number_format($items->sum('market_value'), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <table class="w-full text-left border-collapse">
                        <thead class="bg-white text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="p-5 text-xs uppercase font-bold w-1/4">Produk</th>
                                <th class="p-5 text-xs uppercase font-bold text-center">Unit</th>
                                <th class="p-5 text-xs uppercase font-bold w-1/4">Harga Pasar (Update)</th>
                                <th class="p-5 text-xs uppercase font-bold text-right text-gray-400">Total Modal</th>
                                <th class="p-5 text-xs uppercase font-bold text-right">Total Nilai Aset</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            @foreach($items as $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-5">
                                    <div class="font-bold text-slate-800 text-base">{{ $item['code'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $item['name'] }}</div>
                                </td>

                                <td class="p-5 font-mono text-slate-600 text-center">
                                    {{ number_format($item['quantity'] + 0, 4) }}
                                </td>

                                <td class="p-5">
                                    <div class="relative">
                                        <span class="absolute left-3 top-2.5 text-gray-400 font-bold text-xs">Rp</span>
                                        <input type="number" name="prices[{{ $item['id'] }}]"
                                            value="{{ $item['current_price'] }}"
                                            class="w-full pl-8 pr-3 py-2 border border-gray-200 rounded-lg font-bold text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-200 outline-none transition bg-yellow-50 focus:bg-white shadow-sm">
                                    </div>
                                </td>

                                <td class="p-5 text-right font-medium text-gray-500">
                                    Rp {{ number_format($item['total_cost'], 0, ',', '.') }}
                                </td>

                                <td class="p-5 text-right">
                                    <div class="font-bold text-slate-800 text-lg">
                                        Rp {{ number_format($item['market_value'], 0, ',', '.') }}
                                    </div>

                                    @php
                                    $pnl = $item['market_value'] - $item['total_cost'];
                                    $isProfit = $pnl >= 0;
                                    @endphp

                                    <div
                                        class="text-xs font-bold mt-1 {{ $isProfit ? 'text-emerald-600' : 'text-rose-500' }}">
                                        {{ $isProfit ? '+' : '' }}Rp {{ number_format($pnl, 0, ',', '.') }}
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endforeach

                @endif
            </form>
        </main>
    </div>
</body>

</html>