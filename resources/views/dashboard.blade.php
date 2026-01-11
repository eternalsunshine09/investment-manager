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

        <div class="grid md:grid-cols-3 gap-6">
            <div class="md:col-span-2 bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-lg text-slate-800">📈 Tren Portofolio (30 Hari)</h3>
                    <select
                        class="bg-slate-50 text-xs font-bold text-slate-500 py-2 px-3 rounded-lg outline-none cursor-pointer">
                        <option>30 Hari</option>
                        <option>Year to Date</option>
                    </select>
                </div>
                <div class="h-72 w-full">
                    <canvas id="portfolioChart"></canvas>
                </div>
            </div>

            <div
                class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 flex flex-col h-full relative overflow-hidden">
                <div class="flex justify-between items-center mb-6 z-10 relative">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                        <span>🎯</span> Watchlist
                    </h3>
                    <a href="{{ route('watchlist.index') }}"
                        class="text-xs font-bold text-indigo-600 hover:underline">Lihat Semua &rarr;</a>
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
                            <a href="{{ route('watchlist.index') }}"
                                class="text-indigo-500 font-bold text-xs mt-2 block">+ Tambah</a>
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

                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 flex flex-col justify-center">
                    <h3 class="font-bold text-lg mb-6 text-slate-800">🎯 Target Keuangan</h3>

                    @if($goal)
                    <div class="mb-6">
                        <div class="flex justify-between items-end mb-2">
                            <div>
                                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Goal Utama</p>
                                <p class="text-xl font-black text-slate-800">{{ $goal->name }}</p>
                            </div>
                            <p class="text-sm font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg">
                                Rp {{ number_format($goal->target_amount, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="w-full bg-slate-100 h-4 rounded-full overflow-hidden relative">
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-full rounded-full relative overflow-hidden"
                                style="width: {{ $goal->percentage ?? 65 }}%">
                                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                            </div>
                        </div>

                        <div class="flex justify-between mt-3 text-xs font-bold">
                            <span class="text-slate-400">0%</span>
                            <span class="text-indigo-600">{{ $goal->percentage ?? 65 }}% Tercapai</span>
                            <span class="text-slate-400">100%</span>
                        </div>

                        <div
                            class="mt-6 p-4 bg-emerald-50 rounded-xl border border-emerald-100 flex items-center gap-2 text-emerald-700 text-sm font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            On Track — Estimasi Tercapai: Okt 2026
                        </div>
                    </div>
                    @else
                    <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <p class="text-slate-400 text-sm font-bold">Belum ada target keuangan aktif.</p>
                        <button class="mt-3 text-indigo-600 font-bold hover:underline">+ Buat Target Baru</button>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
    @endsection

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. PORTFOLIO TREND CHART ---
        const ctxLine = document.getElementById('portfolioChart').getContext('2d');

        // Gradient untuk area chart
        const gradientLine = ctxLine.createLinearGradient(0, 0, 0, 400);
        gradientLine.addColorStop(0, 'rgba(102, 126, 234, 0.2)'); // Warna Awal
        gradientLine.addColorStop(1, 'rgba(102, 126, 234, 0)'); // Transparan

        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['1 Jan', '5 Jan', '10 Jan', '15 Jan', '20 Jan', '25 Jan', '30 Jan'],
                datasets: [{
                    label: 'Portfolio Value (Juta Rp)',
                    data: [90, 91.5, 93, 92, 94.5, 96, 97.35],
                    borderColor: '#667eea',
                    backgroundColor: gradientLine,
                    borderWidth: 3,
                    tension: 0.4, // Membuat garis melengkung halus
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#667eea',
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: {
                            family: 'Outfit',
                            size: 13
                        },
                        bodyFont: {
                            family: 'Outfit',
                            size: 13
                        },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(0,0,0,0.03)',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            font: {
                                family: 'Outfit'
                            },
                            color: '#94a3b8'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Outfit'
                            },
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });

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