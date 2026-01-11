<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rebalancing Aset - InvestApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
    }

    /* Hilangkan panah di input number */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    </style>
</head>

<body class="bg-[#F8FAFC] text-slate-800">

    <div class="flex h-screen overflow-hidden w-full">

        <x-sidebar />

        <main class="flex-1 overflow-y-auto p-6 md:p-8 w-0 bg-[#F8FAFC]">

            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">⚖️ Rebalancing Aset</h1>
                <p class="text-slate-500 mt-2 font-medium">Sesuaikan portofoliomu agar tetap disiplin dengan target
                    awal.</p>
            </div>

            @if(session('success'))
            <div
                class="bg-emerald-50 border border-emerald-100 text-emerald-700 p-4 mb-8 rounded-2xl flex items-center gap-3 shadow-sm">
                <span class="bg-emerald-100 p-1 rounded-full">✅</span>
                <p class="font-bold text-sm">{{ session('success') }}</p>
            </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">

                <div class="xl:col-span-4 bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 mb-6 border-b border-slate-50 pb-4">
                        <div class="bg-indigo-50 p-2.5 rounded-xl text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Atur Target</h3>
                            <p class="text-xs text-slate-400">Tentukan persentase (%) ideal.</p>
                        </div>
                    </div>

                    <form action="{{ route('tools.rebalance.store') }}" method="POST">
                        @csrf
                        <div class="space-y-5">
                            @foreach($analysis as $item)
                            <div
                                class="bg-slate-50 p-4 rounded-2xl border border-slate-100 hover:border-indigo-200 transition group">
                                <div class="flex justify-between text-sm mb-2">
                                    <label
                                        class="font-bold text-slate-700 uppercase tracking-wide text-xs">{{ $item['category'] }}</label>
                                    <span class="text-xs font-medium text-slate-400">Saat ini: <span
                                            class="text-slate-600 font-bold">{{ number_format($item['current_pct'], 1) }}%</span></span>
                                </div>
                                <div class="relative flex items-center">
                                    <input type="number" step="0.1" name="allocation[{{ $item['category'] }}]"
                                        value="{{ $item['target_pct'] }}"
                                        class="w-full bg-white border border-slate-200 p-3 pl-4 pr-10 rounded-xl outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 font-bold text-slate-800 transition text-lg group-hover:shadow-sm"
                                        placeholder="0">
                                    <span class="absolute right-4 text-slate-400 font-bold">%</span>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-8 pt-4 border-t border-slate-50">
                            <button type="submit"
                                class="w-full bg-indigo-600 text-white py-3.5 rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 hover:-translate-y-0.5 hover:shadow-xl">
                                Simpan Target
                            </button>
                        </div>
                    </form>
                </div>

                <div class="xl:col-span-8">
                    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 h-full">

                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-emerald-50 p-2.5 rounded-xl text-emerald-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800 text-lg">Analisis Rebalancing</h3>
                                    <p class="text-xs text-slate-400">Tindakan yang diperlukan.</p>
                                </div>
                            </div>
                            <div class="bg-slate-50 px-5 py-3 rounded-2xl border border-slate-100">
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Total Aset
                                    Portofolio</p>
                                <p class="text-xl font-black text-slate-800">Rp
                                    {{ number_format($totalPortfolioValue, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-100">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-100">
                                        <th class="p-5 font-bold">Kategori</th>
                                        <th class="p-5 font-bold text-center w-1/3">Aktual vs Target</th>
                                        <th class="p-5 font-bold text-center">Status</th>
                                        <th class="p-5 font-bold text-right">Nominal Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    @foreach($analysis as $item)
                                    <tr class="hover:bg-slate-50/50 transition group">
                                        <td class="p-5">
                                            <div class="font-bold text-slate-700 uppercase tracking-wide">
                                                {{ $item['category'] }}</div>
                                            <div class="text-xs text-slate-400 mt-1">Rp
                                                {{ number_format($item['current_value'], 0, ',', '.') }}</div>
                                        </td>

                                        <td class="p-5">
                                            <div class="flex justify-between text-xs mb-2 font-bold">
                                                <span
                                                    class="{{ $item['current_pct'] > $item['target_pct'] ? 'text-orange-500' : ($item['current_pct'] < $item['target_pct'] ? 'text-indigo-500' : 'text-slate-500') }}">
                                                    {{ number_format($item['current_pct'], 1) }}%
                                                </span>
                                                <span class="text-slate-400">Target:
                                                    {{ number_format($item['target_pct'], 1) }}%</span>
                                            </div>
                                            <div
                                                class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden flex relative">
                                                <div class="h-full {{ $item['current_pct'] > $item['target_pct'] ? 'bg-orange-400' : 'bg-slate-400' }}"
                                                    style="width: {{ min($item['current_pct'], 100) }}%"></div>

                                                <div class="absolute h-full w-1 bg-black/20 z-10"
                                                    style="left: {{ min($item['target_pct'], 100) }}%"></div>
                                            </div>
                                        </td>

                                        <td class="p-5 text-center">
                                            @if($item['action'] == 'BELI')
                                            <span
                                                class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg text-xs font-bold border border-emerald-200">
                                                <span>🛒</span> BELI
                                            </span>
                                            @elseif($item['action'] == 'JUAL')
                                            <span
                                                class="inline-flex items-center gap-1.5 bg-orange-100 text-orange-700 px-3 py-1.5 rounded-lg text-xs font-bold border border-orange-200">
                                                <span>💰</span> JUAL
                                            </span>
                                            @else
                                            <span
                                                class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-500 px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-200">
                                                <span>✅</span> PAS
                                            </span>
                                            @endif
                                        </td>

                                        <td class="p-5 text-right">
                                            @if($item['action'] != 'TEPAT')
                                            <div class="font-bold text-slate-800 text-lg">
                                                Rp {{ number_format(abs($item['action_value']), 0, ',', '.') }}
                                            </div>
                                            <div
                                                class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1">
                                                Estimasi
                                            </div>
                                            @else
                                            <div class="text-slate-300 font-bold">-</div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 p-4 bg-indigo-50 rounded-xl border border-indigo-100 flex gap-3">
                            <span class="text-indigo-600 text-xl">💡</span>
                            <p class="text-sm text-indigo-800 leading-relaxed">
                                <b>Tips:</b> Rebalancing membantu menjaga profil risikomu. Jual aset yang persentasenya
                                berlebih (sudah untung besar), dan beli aset yang persentasenya kurang (sedang
                                murah/diskon).
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>
</body>

</html>