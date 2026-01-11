<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Investasi - InvestApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
    }

    [x-cloak] {
        display: none !important;
    }
    </style>
</head>

<body class="bg-gray-50 flex text-slate-800" x-data="{ showImportModal: false }">

    @include('components.sidebar')

    <main class="flex-1 h-screen overflow-y-auto p-6 md:p-8">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-slate-900">📊 Laporan & Analisis</h1>

            <div class="flex gap-2">
                <button @click="showImportModal = true"
                    class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-sm transition">
                    <span>📤</span> Import CSV
                </button>

                <a href="{{ route('reports.export') }}" target="_blank"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-emerald-200 transition">
                    <span>📥</span> Export CSV
                </a>
            </div>
        </div>

        @if(session('success'))
        <div
            class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r flex items-center shadow-sm">
            <span class="text-xl mr-2">✅</span>
            <p>{{ session('success') }}</p>
        </div>
        @endif

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
            <form action="{{ route('reports.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Transaksi</label>
                        <select name="type"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-medium bg-white">
                            <option value="all" {{ $type == 'all' ? 'selected' : '' }}>Semua Jenis</option>
                            <option value="beli" {{ $type == 'beli' ? 'selected' : '' }}>Pembelian</option>
                            <option value="jual" {{ $type == 'jual' ? 'selected' : '' }}>Penjualan</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-xl font-bold shadow-lg shadow-indigo-200 transition">
                            Tampilkan Laporan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-gray-400 text-xs font-bold uppercase mb-1">Total Transaksi</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $totalTransaksi }}</h3>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-emerald-500 text-xs font-bold uppercase mb-1">Total Pembelian</p>
                <h3 class="text-xl font-black text-slate-800">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-orange-500 text-xs font-bold uppercase mb-1">Total Penjualan</p>
                <h3 class="text-xl font-black text-slate-800">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-purple-500 text-xs font-bold uppercase mb-1">Total Fee Broker</p>
                <h3 class="text-xl font-black text-slate-800">Rp {{ number_format($totalFee, 0, ',', '.') }}</h3>
            </div>
            <div
                class="bg-gradient-to-br from-indigo-600 to-blue-500 p-5 rounded-2xl shadow-lg shadow-indigo-200 text-white">
                <p class="text-indigo-100 text-xs font-bold uppercase mb-1">Realized Profit (Global)</p>
                <h3 class="text-xl font-black">
                    {{ $totalRealizedProfit >= 0 ? '+' : '' }}Rp {{ number_format($totalRealizedProfit, 0, ',', '.') }}
                </h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-slate-800 mb-4">📈 Tren Transaksi Bulanan ({{ $chartYear }})</h2>
                <div class="relative h-64 w-full">
                    <canvas id="investmentChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-slate-800 mb-4">🍰 Alokasi Pembelian (Kategori)</h2>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="allocationChart"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-slate-800">Rincian Riwayat Transaksi</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                            <th class="px-6 py-4 font-bold">Tanggal</th>
                            <th class="px-6 py-4 font-bold">Jenis</th>
                            <th class="px-6 py-4 font-bold">Produk</th>
                            <th class="px-6 py-4 font-bold">Akun</th>
                            <th class="px-6 py-4 font-bold text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($transactions as $t)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-slate-700">
                                {{ \Carbon\Carbon::parse($t->transaction_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-bold 
                                    {{ in_array($t->type, ['beli', 'topup']) ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ ucfirst(str_replace('_', ' ', $t->type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700">{{ optional($t->product)->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ optional($t->account)->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-right font-bold text-slate-800">Rp
                                {{ number_format($t->total_value, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada transaksi di periode
                                ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="h-10"></div>

    </main>

    <div x-show="showImportModal" x-cloak
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        x-transition.opacity>
        <div @click.away="showImportModal = false"
            class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">📤 Import Data Transaksi</h3>
                <button @click="showImportModal = false" class="text-gray-400 hover:text-red-500 transition">✕</button>
            </div>

            <form action="{{ route('reports.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 mb-4 text-sm text-blue-800">
                    <p class="font-bold mb-1">💡 Tips:</p>
                    <ul class="list-disc ml-4 space-y-1">
                        <li>Gunakan file <b>.CSV</b>.</li>
                        <li>Format kolom harus sama dengan hasil Export.</li>
                        <li>Pastikan kode produk sudah ada di Master Data.</li>
                    </ul>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pilih File CSV</label>
                        <input type="file" name="file" accept=".csv" required
                            class="w-full border border-gray-200 p-2.5 rounded-xl bg-white focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" @click="showImportModal = false"
                        class="text-gray-500 font-bold px-4 py-2 hover:bg-gray-100 rounded-lg">Batal</button>
                    <button type="submit"
                        class="bg-indigo-600 text-white font-bold px-6 py-2 rounded-lg hover:bg-indigo-700 shadow-lg">Upload
                        & Import</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Data Line Chart
    const $buyChart = array_values($buyChart);
    const $sellChart = array_values($sellChart);


    // Data Doughnut Chart
    // PERBAIKAN: Tanda panah "->" harus menyatu tanpa spasi
    $allocationLabels = $allocation - > keys() - > values();
    $allocationValues = $allocation - > values();




    // 1. Line Chart (Bulanan)
    const ctx = document.getElementById('investmentChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                    label: 'Pembelian',
                    data: buyDataRaw,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Penjualan',
                    data: sellDataRaw,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    grid: {
                        borderDash: [2, 4],
                        color: '#f3f4f6'
                    },
                    beginAtZero: true
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // 2. Doughnut Chart (Alokasi)
    const ctx2 = document.getElementById('allocationChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: allocationLabels,
            datasets: [{
                data: allocationValues,
                backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8
                    }
                }
            },
            cutout: '70%'
        }
    });
    </script>
</body>

</html>