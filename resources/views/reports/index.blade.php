@extends('layouts.app')

@section('title', 'Laporan & Analisis - Porto Tracking')

@section('content')
<div class="h-full overflow-y-auto pt-20 md:pt-8 pb-10 px-4" x-data="{ showImportModal: false }">
    <div class="max-w-[1600px] mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                    <span class="text-indigo-500">📊</span> Laporan & Analisis
                </h1>
                <p class="text-slate-500 font-medium mt-1">Ringkasan performa investasi dan biaya transaksi.</p>
            </div>

            <div class="flex gap-3">
                <button @click="showImportModal = true"
                    class="bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-indigo-600 px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-sm transition">
                    <i class="fas fa-file-import"></i> Import CSV
                </button>

                <a href="{{ route('reports.export') }}" target="_blank"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-emerald-200 transition transform hover:-translate-y-0.5">
                    <i class="fas fa-file-export"></i> Export CSV
                </a>
            </div>
        </div>

        @if(session('success'))
        <div
            class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-xl"></i>
            <p class="font-bold">{{ session('success') }}</p>
        </div>
        @endif

        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 mb-8">
            <form action="{{ route('reports.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-slate-700 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-slate-700 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Jenis
                            Transaksi</label>
                        <select name="type"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-slate-700 outline-none bg-white">
                            <option value="all" {{ $type == 'all' ? 'selected' : '' }}>Semua Jenis</option>
                            <option value="beli" {{ $type == 'beli' ? 'selected' : '' }}>Pembelian</option>
                            <option value="jual" {{ $type == 'jual' ? 'selected' : '' }}>Penjualan</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-bold shadow-lg shadow-indigo-200 transition transform hover:-translate-y-0.5">
                            Tampilkan Laporan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white p-5 rounded-[1.5rem] shadow-sm border border-slate-100">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-1">Total Transaksi</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $totalTransaksi }}</h3>
            </div>
            <div class="bg-white p-5 rounded-[1.5rem] shadow-sm border border-slate-100">
                <p class="text-emerald-500 text-[10px] font-bold uppercase tracking-wider mb-1">Total Pembelian</p>
                <h3 class="text-xl font-black text-slate-800">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-5 rounded-[1.5rem] shadow-sm border border-slate-100">
                <p class="text-orange-500 text-[10px] font-bold uppercase tracking-wider mb-1">Total Penjualan</p>
                <h3 class="text-xl font-black text-slate-800">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-5 rounded-[1.5rem] shadow-sm border border-slate-100 relative overflow-hidden">
                <div class="absolute right-0 top-0 p-4 opacity-5"><i class="fas fa-receipt text-6xl text-rose-500"></i>
                </div>
                <p class="text-rose-500 text-[10px] font-bold uppercase tracking-wider mb-1">Total Fee Broker</p>
                <h3 class="text-xl font-black text-slate-800">Rp {{ number_format($totalFee, 0, ',', '.') }}</h3>
            </div>
            <div
                class="bg-gradient-to-br from-indigo-600 to-blue-500 p-5 rounded-[1.5rem] shadow-lg shadow-indigo-200 text-white">
                <p class="text-indigo-100 text-[10px] font-bold uppercase tracking-wider mb-1">Net Cashflow</p>
                <h3 class="text-xl font-black">
                    {{ $totalRealizedProfit >= 0 ? '+' : '' }}Rp {{ number_format($totalRealizedProfit, 0, ',', '.') }}
                </h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2 bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                <h2 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                    <span>📈</span> Tren Transaksi (Tahunan)
                </h2>
                <div class="relative h-72 w-full">
                    <canvas id="investmentChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                <h2 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                    <span>🍰</span> Alokasi Aset
                </h2>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="allocationChart"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-lg font-black text-slate-800">Rincian Riwayat Transaksi</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-100">
                            <th class="px-6 py-4 font-bold">Tanggal</th>
                            <th class="px-6 py-4 font-bold">Jenis</th>
                            <th class="px-6 py-4 font-bold">Produk</th>
                            <th class="px-6 py-4 font-bold">Akun</th>
                            <th class="px-6 py-4 font-bold text-right text-rose-500">Fee</th>
                            <th class="px-6 py-4 font-bold text-right">Total Net</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($transactions as $t)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-bold text-slate-700">
                                {{ \Carbon\Carbon::parse($t->transaction_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 rounded-lg text-xs font-bold border 
                                    {{ in_array($t->type, ['beli', 'topup']) ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : '' }}
                                    {{ in_array($t->type, ['jual', 'tarik']) ? 'bg-orange-50 text-orange-700 border-orange-100' : '' }}
                                    {{ in_array($t->type, ['dividen_cash']) ? 'bg-blue-50 text-blue-700 border-blue-100' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $t->type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700">{{ optional($t->product)->code ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ optional($t->account)->name ?? '-' }}</td>

                            <td class="px-6 py-4 text-right text-rose-500 font-medium">
                                @if($t->fee > 0)
                                ({{ number_format($t->fee, 0, ',', '.') }})
                                @else
                                -
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right font-black text-slate-800">
                                <span
                                    class="{{ in_array($t->type, ['jual', 'topup', 'dividen_cash']) ? 'text-emerald-600' : 'text-slate-800' }}">
                                    Rp {{ number_format($t->total_value, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-4xl mb-3 text-slate-200"></i>
                                    <p class="text-sm font-medium">Belum ada transaksi di periode ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div x-show="showImportModal" x-cloak
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4"
        x-transition.opacity>
        <div @click.away="showImportModal = false"
            class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl overflow-hidden p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-slate-800">📤 Import Transaksi</h3>
                <button @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 transition"><i
                        class="fas fa-times text-xl"></i></button>
            </div>
            <form action="{{ route('reports.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 mb-6 text-xs text-indigo-800">
                    <p class="font-bold mb-2 flex items-center gap-1"><i class="fas fa-info-circle"></i> Petunjuk:</p>
                    <ul class="list-disc ml-4 space-y-1 opacity-80">
                        <li>Gunakan format <b>.CSV</b>.</li>
                        <li>Pastikan kode produk sudah ada di Master Data.</li>
                    </ul>
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">File CSV</label>
                    <input type="file" name="file" accept=".csv" required
                        class="w-full border border-slate-200 p-3 rounded-xl bg-slate-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showImportModal = false"
                        class="text-slate-500 font-bold px-4 py-3 hover:bg-slate-50 rounded-xl transition">Batal</button>
                    <button type="submit"
                        class="bg-indigo-600 text-white font-bold px-6 py-3 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition transform hover:-translate-y-1">Upload
                        & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@php
// PREPARE DATA FOR CHART.JS
$buyData = array_values($buyChart);
$sellData = array_values($sellChart);

if ($allocation instanceof \Illuminate\Support\Collection) {
$donutLabels = $allocation->keys()->values();
$donutValues = $allocation->values();
} else {
$donutLabels = array_keys((array) $allocation);
$donutValues = array_values((array) $allocation);
}
@endphp

<script>
const buyData = @json($buyData);
const sellData = @json($sellData);
const allocationLabels = @json($donutLabels);
const allocationValues = @json($donutValues);

const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

// LINE CHART
const ctx = document.getElementById('investmentChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Pembelian',
            data: buyData,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16,185,129,0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            pointRadius: 3
        }, {
            label: 'Penjualan',
            data: sellData,
            borderColor: '#f97316',
            backgroundColor: 'rgba(249,115,22,0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            pointRadius: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// DONUT CHART
const ctx2 = document.getElementById('allocationChart').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: allocationLabels,
        datasets: [{
            data: allocationValues,
            backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899',
                '#6366f1'
            ],
            borderWidth: 0,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '75%',
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush