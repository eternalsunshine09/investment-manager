@extends('layouts.app')

@section('title', 'Dashboard - Porto Tracking')

@section('content')
<div class="h-full overflow-auto pt-20 md:pt-8 pb-10">
    <div class="max-w-7xl mx-auto px-4 space-y-6">

        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Dashboard</h1>
                <p class="text-slate-500 font-medium mt-1">Selamat datang kembali, {{ Auth::user()->name }}! 👋</p>
            </div>
            <div class="hidden md:block bg-white px-4 py-2 rounded-full shadow-sm border border-slate-100">
                <span class="text-xs font-bold text-slate-500">Update Terakhir: {{ date('d M Y') }}</span>
            </div>
        </div>

        <div class="rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">

            <div
                class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none">
            </div>

            <div class="relative z-10">
                <p class="text-indigo-200 text-sm font-bold uppercase tracking-wider mb-2">Total Net Worth</p>
                <h1 class="text-5xl md:text-6xl font-black mb-2 tracking-tight">
                    Rp {{ number_format($netWorth, 0, ',', '.') }}
                </h1>

                <div class="flex items-center gap-2 mb-8">
                    <span
                        class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold border border-white/10 flex items-center gap-1">
                        @if($isProfit)
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" />
                        </svg>
                        +2.45% bulan ini
                        @else
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" />
                        </svg>
                        -1.2% bulan ini
                        @endif
                    </span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div
                        class="bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/10 hover:transform hover:-translate-y-1 transition duration-200">
                        <p class="text-xs text-indigo-200 font-bold uppercase mb-1">Cash (RDN)</p>
                        <p class="font-bold text-xl">Rp {{ number_format($totalCash/1000000, 0) }}jt</p>
                    </div>
                    <div
                        class="bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/10 hover:transform hover:-translate-y-1 transition duration-200">
                        <p class="text-xs text-indigo-200 font-bold uppercase mb-1">Invested</p>
                        <p class="font-bold text-xl">Rp {{ number_format($totalInvested/1000000, 0) }}jt</p>
                    </div>
                    <div
                        class="bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/10 hover:transform hover:-translate-y-1 transition duration-200">
                        <p class="text-xs text-indigo-200 font-bold uppercase mb-1">Profit/Loss</p>
                        <p class="font-bold text-xl {{ $isProfit ? 'text-green-300' : 'text-red-300' }}">
                            {{ $isProfit ? '+' : '' }}Rp {{ number_format($profit/1000000, 1) }}jt
                        </p>
                    </div>
                    <div
                        class="bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/10 hover:transform hover:-translate-y-1 transition duration-200">
                        <p class="text-xs text-indigo-200 font-bold uppercase mb-1">Risk Level</p>
                        <p class="font-bold text-xl text-yellow-300">⚠️ Medium</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-2 bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100"
            x-data="chartComponent()">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                    <span>📈</span> Tren Nilai Portofolio
                </h3>

                <div class="flex bg-slate-50 p-1 rounded-xl border border-slate-200">
                    @foreach(['1M'=>'1 Bln', '3M'=>'3 Bln', '6M'=>'6 Bln', '1Y'=>'1 Thn', 'ALL'=>'Semua'] as $key =>
                    $label)
                    <button @click="filterChart('{{ $key }}')"
                        :class="activeFilter === '{{ $key }}' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="h-72 w-full relative">
                <canvas id="portfolioChart"></canvas>
                <div x-show="isLoading"
                    class="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-sm transition">
                    <svg class="animate-spin h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <div
            class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 flex flex-col h-full relative overflow-hidden">
            <div class="flex justify-between items-center mb-6 z-10 relative">
                <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                    <span>🎯</span> Watchlist
                </h3>
                <a href="{{ route('watchlist.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">Lihat
                    Semua &rarr;</a>
            </div>

            <div class="flex-1 space-y-4 overflow-y-auto custom-scrollbar z-10 relative">
                @forelse($watchlists as $item)
                @php
                $isCheap = $item->current_price <= $item->target_price;
                    $diff = $item->current_price - $item->target_price;
                    $diffPct = ($item->target_price > 0) ? ($diff / $item->target_price) * 100 : 0;
                    @endphp
                    <div
                        class="group flex items-center justify-between p-3 rounded-2xl border border-slate-50 hover:bg-slate-50 hover:border-slate-100 transition cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-xs">
                                {{ Str::limit($item->code, 4, '') }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-700 text-sm">{{ $item->code }}</p>
                                <p class="text-[10px] text-slate-400">Target:
                                    {{ number_format($item->target_price) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-sm text-slate-800">{{ number_format($item->current_price) }}
                            </p>
                            @if($isCheap)
                            <span
                                class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">BUY
                                NOW! 🚀</span>
                            @else
                            <span class="text-[10px] font-bold text-orange-400">{{ number_format($diffPct, 1) }}%
                                lagi</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-400">
                        <p class="text-sm">Belum ada incaran.</p>
                        <a href="{{ route('watchlist.index') }}" class="text-indigo-500 font-bold text-xs mt-2 block">+
                            Tambah</a>
                    </div>
                    @endforelse
            </div>

            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-indigo-50 rounded-full blur-3xl opacity-50">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100">
                <h3 class="font-bold text-lg mb-6 text-slate-800">🧩 Komposisi Aset</h3>
                <div class="h-72 flex items-center justify-center relative">
                    <canvas id="assetChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total</span>
                        <span class="text-xl font-black text-slate-800">100%</span>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 flex flex-col justify-center h-full relative overflow-hidden">
                <div class="flex justify-between items-center mb-6 relative z-10">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                        <span>🎯</span> Target Keuangan
                    </h3>
                    @if($goal)
                    <button class="text-xs font-bold text-slate-400 hover:text-indigo-600">Detail</button>
                    @endif
                </div>

                @if($goal)
                <div class="relative z-10">
                    <div class="text-center mb-6">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 text-2xl mb-3 shadow-inner">
                            🎯
                        </div>
                        <h4 class="text-xl font-black text-slate-800">{{ $goal->name }}</h4>
                        <p class="text-sm font-bold text-slate-400">Target: Rp
                            {{ number_format($goal->target_amount, 0, ',', '.') }}</p>
                    </div>

                    <div class="relative h-6 bg-slate-100 rounded-full overflow-hidden mb-2 shadow-inner">
                        <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-1000 ease-out"
                            style="width: {{ $goal->percentage }}%"></div>
                        <div
                            class="absolute inset-0 flex items-center justify-center text-[10px] font-black text-white drop-shadow-md">
                            {{ $goal->percentage }}% TERCAPAI
                        </div>
                    </div>

                    <div class="flex justify-between text-xs font-bold text-slate-400 px-1">
                        <span>Rp 0</span>
                        <span>Rp {{ number_format($goal->target_amount/1000000, 0) }} Jt</span>
                    </div>
                </div>
                @else
                <div class="text-center py-8 relative z-10">
                    <div
                        class="bg-slate-50 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300">
                        <i class="fas fa-bullseye text-2xl"></i>
                    </div>
                    <p class="text-slate-500 font-bold mb-1">Belum ada target aktif.</p>
                    <p class="text-xs text-slate-400 mb-6">Tentukan tujuan investasimu sekarang.</p>
                    <button onclick="/* Logic Buka Modal */"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                        + Buat Target Baru
                    </button>
                </div>
                @endif

                <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-50 rounded-full blur-3xl opacity-50"></div>
            </div>

        </div>
    </div>
    @endsection

    @push('scripts')
    <script>
    function chartComponent() {
        return {
            activeFilter: '1Y', // Default 1 Tahun
            isLoading: false,
            chartInstance: null,
            allData: @json($allChartData), // Data dari Controller

            init() {
                this.renderChart(this.filterData('1Y'));
            },

            filterChart(period) {
                this.activeFilter = period;
                this.isLoading = true;

                // Simulasi loading sebentar biar kerasa interaktif
                setTimeout(() => {
                    const filteredData = this.filterData(period);
                    this.updateChart(filteredData);
                    this.isLoading = false;
                }, 300);
            },

            filterData(period) {
                const now = new Date();
                let cutoffDate = new Date();

                switch (period) {
                    case '1M':
                        cutoffDate.setMonth(now.getMonth() - 1);
                        break;
                    case '3M':
                        cutoffDate.setMonth(now.getMonth() - 3);
                        break;
                    case '6M':
                        cutoffDate.setMonth(now.getMonth() - 6);
                        break;
                    case '1Y':
                        cutoffDate.setFullYear(now.getFullYear() - 1);
                        break;
                    case '5Y':
                        cutoffDate.setFullYear(now.getFullYear() - 5);
                        break;
                    case 'ALL':
                        cutoffDate = new Date(0);
                        break; // Tanggal awal banget
                }

                // Filter data array berdasarkan tanggal x
                return this.allData.filter(item => new Date(item.x) >= cutoffDate);
            },

            renderChart(data) {
                const ctx = document.getElementById('portfolioChart').getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
                gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

                this.chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map(d => new Date(d.x).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short'
                        })),
                        datasets: [{
                            label: 'Net Worth',
                            data: data.map(d => d.y),
                            borderColor: '#4f46e5',
                            backgroundColor: gradient,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0, // Titik hilang biar clean
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                grid: {
                                    color: '#f1f5f9',
                                    borderDash: [5, 5]
                                },
                                beginAtZero: false
                            }
                        }
                    }
                });
            },

            updateChart(newData) {
                this.chartInstance.data.labels = newData.map(d => new Date(d.x).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short'
                }));
                this.chartInstance.data.datasets[0].data = newData.map(d => d.y);
                this.chartInstance.update();
            }
        }
    }

    // --- SCRIPT ASSET COMPOSITION (TETAP SAMA) ---
    // ... (Script donut chart sebelumnya) ...
    </script>

    // --- 2. ASSET COMPOSITION DONUT CHART ---
    const ctxDonut = document.getElementById('assetChart').getContext('2d');
    const compositionData = @json($composition);

    new Chart(ctxDonut, {
    type: 'doughnut',
    data: {
    labels: Object.keys(compositionData),
    datasets: [{
    data: Object.values(compositionData),
    backgroundColor: ['#6366f1', '#10b981', '#f97316', '#eab308', '#ec4899'],
    borderWidth: 0,
    hoverOffset: 10
    }]
    },
    options: {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '75%', // Lubang tengah donut
    plugins: {
    legend: {
    position: 'bottom',
    labels: {
    usePointStyle: true,
    padding: 20,
    font: {
    family: 'Outfit',
    size: 12,
    weight: 'bold'
    },
    color: '#475569'
    }
    }
    }
    }
    });
    });
    </script>
    @endpush