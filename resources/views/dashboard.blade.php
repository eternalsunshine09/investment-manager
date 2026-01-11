@extends('layouts.app')

@section('title', 'Dashboard - Porto Tracking')

@section('content')
<div class="h-full overflow-auto pb-10" x-data="{ 
        ...chartComponent(), 
        showGoalModal: false, 
        isEdit: false, 
        form: { id: '', name: '', target_amount: '', product_ids: [] } 
     }">

    <div class="max-w-[1600px] mx-auto space-y-6">

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
                <p class="text-indigo-200 text-sm font-bold uppercase tracking-wider mb-2">Total Kekayaan Bersih</p>
                <h1 class="text-5xl md:text-6xl font-black mb-2 tracking-tight">
                    Rp {{ number_format($netWorth ?? 0, 0, ',', '.') }}
                </h1>
                <div class="flex items-center gap-2 mb-8">
                    <span
                        class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold border border-white/10">
                        @if(($growthPercentage ?? 0) >= 0)
                        <span class="text-emerald-100">▲ +{{ number_format($growthPercentage ?? 0, 1) }}% bulan
                            ini</span>
                        @else
                        <span class="text-rose-100">▼ {{ number_format($growthPercentage ?? 0, 1) }}% bulan ini</span>
                        @endif
                    </span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/10">
                        <p class="text-xs text-indigo-200 font-bold uppercase mb-1">Cash (RDN)</p>
                        <p class="font-bold text-xl">Rp {{ number_format(($totalCash ?? 0)/1000000, 0) }}jt</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/10">
                        <p class="text-xs text-indigo-200 font-bold uppercase mb-1">Invested</p>
                        <p class="font-bold text-xl">Rp {{ number_format(($totalInvested ?? 0)/1000000, 0) }}jt</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/10">
                        <p class="text-xs text-indigo-200 font-bold uppercase mb-1">Profit/Loss</p>
                        <p class="font-bold text-xl {{ ($isProfit ?? false) ? 'text-green-300' : 'text-red-300' }}">
                            {{ ($isProfit ?? false) ? '+' : '' }}Rp {{ number_format(($profit ?? 0)/1000000, 1) }}jt
                        </p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/10">
                        <p class="text-xs text-indigo-200 font-bold uppercase mb-1">Risk Level</p>
                        <p class="font-bold text-xl text-yellow-300">⚠️ Medium</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6">

            <div class="col-span-12 xl:col-span-8 bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2"><span>📈</span> Tren Nilai
                        Portofolio</h3>
                    <div class="flex bg-slate-50 p-1 rounded-xl border border-slate-200 overflow-x-auto max-w-full">
                        @foreach(['1M'=>'1 Bln', '3M'=>'3 Bln', '6M'=>'6 Bln', '1Y'=>'1 Thn', 'ALL'=>'Semua'] as $key =>
                        $label)
                        <button @click="filterChart('{{ $key }}')"
                            :class="activeFilter === '{{ $key }}' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>
                <div class="h-72 w-full relative">
                    <canvas id="portfolioChart"></canvas>
                    <div x-show="isLoading"
                        class="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-sm transition">
                        <svg class="animate-spin h-8 w-8 text-indigo-500" viewBox="0 0 24 24">
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
                class="col-span-12 xl:col-span-4 bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 flex flex-col h-full relative overflow-hidden">
                <div class="flex justify-between items-center mb-6 z-10 relative">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2"><span>🎯</span> Watchlist Saham
                    </h3>
                    <a href="{{ route('watchlist.index') }}"
                        class="text-xs font-bold text-indigo-600 hover:underline">Lihat Semua &rarr;</a>
                </div>
                <div class="flex-1 space-y-3 overflow-y-auto custom-scrollbar z-10 relative max-h-[300px]">
                    @forelse($watchlists ?? [] as $item)
                    @php
                    $isCheap = $item->current_price <= $item->target_price;
                        $diffPct = ($item->target_price > 0) ? (($item->current_price - $item->target_price) /
                        $item->target_price) * 100 : 0;
                        @endphp
                        <div
                            class="group flex items-center justify-between p-3 rounded-2xl border border-slate-50 hover:bg-slate-50 hover:border-slate-100 transition cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-xs">
                                    {{ Str::limit($item->code, 4, '') }}</div>
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
                                <span
                                    class="text-[10px] font-bold text-orange-400">{{ number_format(abs($diffPct), 1) }}%
                                    lagi</span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-10 text-slate-400">
                            <p class="text-sm">Belum ada incaran.</p>
                            <a href="{{ route('watchlist.index') }}"
                                class="text-indigo-500 font-bold text-xs mt-2 block hover:underline">+ Tambah</a>
                        </div>
                        @endforelse
                </div>
                <div
                    class="absolute -bottom-10 -right-10 w-32 h-32 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

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
                class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 flex flex-col h-full relative overflow-hidden">
                <div class="flex justify-between items-center mb-4 z-10 relative">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2"><span>🎯</span> Target Keuangan
                    </h3>

                    <button type="button"
                        @click="isEdit = false; form = { id: '', name: '', target_amount: '', product_ids: [] }; showGoalModal = true"
                        class="bg-indigo-50 text-indigo-600 p-2 rounded-lg hover:bg-indigo-100 transition cursor-pointer"
                        title="Tambah Target">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>

                @if(isset($goals) && $goals->count() > 0)
                <div class="flex-1 overflow-y-auto custom-scrollbar z-10 relative space-y-4 max-h-[300px] pr-2">
                    @foreach($goals as $item)
                    <div
                        class="bg-slate-50 p-4 rounded-2xl border border-slate-100 relative group transition hover:shadow-md">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-lg shadow-sm">
                                    {{ substr($item->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-700 text-sm leading-tight">{{ $item->name }}</h4>
                                    <p class="text-[10px] text-slate-400 font-bold">Target: Rp
                                        {{ number_format($item->target_amount/1000000, 0) }} Jt</p>
                                </div>
                            </div>

                            <div class="flex gap-1">
                                <button @click="isEdit = true; 
                                                form = { 
                                                    id: '{{ $item->id }}', 
                                                    name: '{{ $item->name }}', 
                                                    target_amount: '{{ $item->target_amount }}', 
                                                    product_ids: {{ $item->products->pluck('id') }} 
                                                }; 
                                                showGoalModal = true"
                                    class="text-slate-300 hover:text-indigo-600 transition p-1" title="Edit">
                                    <i class="fas fa-pencil-alt text-xs"></i>
                                </button>

                                <form action="{{ route('goals.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus target {{ $item->name }}?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-300 hover:text-red-500 transition p-1"
                                        title="Hapus">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div
                            class="relative w-full bg-white h-3 rounded-full overflow-hidden mb-1 border border-slate-100">
                            <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all duration-1000"
                                style="width: {{ $item->percentage }}%"></div>
                        </div>
                        <div class="flex justify-between items-center text-[10px] font-bold">
                            <span class="text-indigo-600">{{ $item->percentage }}%</span>
                            @if($item->percentage >= 100)
                            <span class="text-emerald-500 flex items-center gap-1"><i class="fas fa-check-circle"></i>
                                TERCAPAI</span>
                            @else
                            <span class="text-slate-400">On Process</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-10 z-10 relative">
                    <div
                        class="bg-slate-50 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300">
                        <i class="fas fa-bullseye text-2xl"></i>
                    </div>
                    <p class="text-slate-500 font-bold mb-1 text-sm">Belum ada target.</p>
                    <p class="text-xs text-slate-400 mb-6">Tentukan tujuan investasimu sekarang.</p>
                    <button
                        @click="isEdit = false; form = { id: '', name: '', target_amount: '', product_ids: [] }; showGoalModal = true"
                        class="text-indigo-600 font-bold text-xs hover:underline mt-2 cursor-pointer">
                        + Buat Target Pertama
                    </button>
                </div>
                @endif
                <div
                    class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none">
                </div>
            </div>
        </div>

    </div>

    <div x-show="showGoalModal" x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity">
        <div @click.away="showGoalModal = false"
            class="bg-white rounded-[2rem] w-full max-w-lg p-8 shadow-2xl transform transition-all max-h-[90vh] overflow-y-auto custom-scrollbar">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-black text-slate-800" x-text="isEdit ? '✏️ Edit Target' : '🎯 Target Baru'">
                </h3>
                <button @click="showGoalModal = false" class="text-slate-400 hover:text-slate-600"><i
                        class="fas fa-times text-xl"></i></button>
            </div>

            <form x-bind:action="isEdit ? '/goals/' + form.id : '{{ route('goals.store') }}'" method="POST"
                class="space-y-5">
                @csrf
                <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Nama Tujuan</label>
                    <input type="text" name="name" x-model="form.name" placeholder="Contoh: Beli Rumah" required
                        class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Nominal Target
                        (Rp)</label>
                    <input type="number" name="target_amount" x-model="form.target_amount" placeholder="0" required
                        class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Hubungkan Aset
                        Investasi</label>
                    <div
                        class="bg-slate-50 border border-slate-200 rounded-xl p-4 max-h-48 overflow-y-auto custom-scrollbar">
                        <div class="space-y-2">
                            @if(isset($allProducts))
                            @foreach($allProducts as $p)
                            <label
                                class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition cursor-pointer border border-transparent hover:border-slate-100">
                                <input type="checkbox" name="product_ids[]" value="{{ $p->id }}"
                                    x-model="form.product_ids"
                                    class="w-5 h-5 rounded text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <div class="flex-1">
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-slate-700 text-sm">{{ $p->code }}</span>
                                        <span
                                            class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-slate-200 text-slate-500">{{ $p->category }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 truncate">{{ $p->name }}</p>
                                </div>
                            </label>
                            @endforeach
                            @else
                            <p class="text-center text-sm text-slate-400 italic">Data aset belum tersedia.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-0.5 transition duration-200">
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Buat Target'"></span>
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function chartComponent() {
    return {
        activeFilter: '1Y',
        isLoading: false,
        chartInstance: null,
        allData: @json($allChartData ?? []),

        init() {
            if (this.allData.length > 0) {
                this.renderChart(this.filterData('1Y'));
            }
        },

        filterChart(period) {
            this.activeFilter = period;
            this.isLoading = true;
            setTimeout(() => {
                this.updateChart(this.filterData(period));
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
                case 'ALL':
                    cutoffDate = new Date(0);
                    break;
            }
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
                        pointRadius: 0,
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
            if (this.chartInstance) {
                this.chartInstance.data.labels = newData.map(d => new Date(d.x).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short'
                }));
                this.chartInstance.data.datasets[0].data = newData.map(d => d.y);
                this.chartInstance.update();
            }
        }
    }
}

const ctxDonut = document.getElementById('assetChart').getContext('2d');
const compositionData = @json($composition ?? []); // Aman jika null
new Chart(ctxDonut, {
    type: 'doughnut',
    data: {
        labels: Object.keys(compositionData),
        datasets: [{
            data: Object.values(compositionData),
            backgroundColor: ['#6366f1', '#10b981', '#f97316', '#eab308', '#ec4899', '#8b5cf6'],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '75%',
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
</script>
@endpush