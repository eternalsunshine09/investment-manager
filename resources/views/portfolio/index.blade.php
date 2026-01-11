<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Portofolio - InvestApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 font-sans">
    <div class="flex h-screen overflow-hidden">

        <x-sidebar />

        <main class="flex-1 overflow-y-auto p-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800">📈 Portofolio Aset</h1>
                    <p class="text-gray-500 mt-1">Pantau performa untung/rugi asetmu secara real-time.</p>
                </div>
                <a href="{{ route('asset.summary') }}"
                    class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-indigo-700 transition flex items-center gap-2 shadow-lg shadow-indigo-200">
                    <span>✏️</span> Update Harga Pasar
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[1400px]">
                    <thead class="bg-slate-900 text-white text-sm">
                        <tr>
                            <th class="p-4 uppercase">Produk</th>
                            <th class="p-4 uppercase text-center">Unit Sisa</th>
                            <th class="p-4 uppercase text-right">Avg Buy (NAB)</th>
                            <th class="p-4 uppercase text-right">Total Modal</th>
                            <th class="p-4 uppercase text-right">Harga Pasar</th>
                            <th class="p-4 uppercase text-right">Total Nilai</th>
                            <th class="p-4 uppercase text-right">Realized PnL</th>
                            <th class="p-4 uppercase text-right">Unrealized PnL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($summary as $item)
                        <tr class="text-sm hover:bg-gray-50 transition">
                            <td class="p-4 font-bold text-slate-700">
                                {{ $item['code'] }}
                                <span
                                    class="block text-[10px] font-normal text-gray-400">{{ strtoupper($item['category']) }}</span>
                            </td>

                            <td class="p-4 text-center font-mono text-slate-600">
                                {{ number_format($item['units'] + 0, 4) }}
                            </td>

                            <td class="p-4 text-right text-gray-500">
                                Rp {{ number_format($item['avg_price'], 0, ',', '.') }}
                            </td>

                            <td class="p-4 text-right font-medium text-slate-700">
                                Rp {{ number_format($item['total_cost'], 0, ',', '.') }}
                            </td>

                            <td class="p-4 text-right font-bold text-indigo-600">
                                Rp {{ number_format($item['current_price'], 0, ',', '.') }}
                            </td>

                            <td class="p-4 text-right font-bold text-slate-800">
                                Rp {{ number_format($item['market_value'], 0, ',', '.') }}
                            </td>

                            <td
                                class="p-4 text-right font-bold {{ $item['realized_pnl'] >= 0 ? 'text-teal-600' : 'text-orange-600' }}">
                                Rp {{ number_format($item['realized_pnl'], 0, ',', '.') }}
                            </td>

                            <td
                                class="p-4 text-right font-bold {{ $item['unrealized_pnl'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                <div>Rp {{ number_format($item['unrealized_pnl'], 0, ',', '.') }}</div>
                                <div class="text-[10px]">{{ number_format($item['pnl_pct'], 2) }}%</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-400">Belum ada data portofolio.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>