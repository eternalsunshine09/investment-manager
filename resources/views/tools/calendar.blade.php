<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Keuangan - InvestApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
    }

    [x-cloak] {
        display: none !important;
    }
    </style>
</head>

<body class="bg-[#F8FAFC] text-slate-800" x-data="{ showModal: false }">

    <div class="flex h-screen overflow-hidden w-full">

        <x-sidebar />

        <main class="flex-1 overflow-y-auto p-6 md:p-8 w-0 bg-[#F8FAFC]">

            <div class="flex justify-between items-end mb-8">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">📅 Kalender Keuangan</h1>
                    <p class="text-slate-500 mt-2 font-medium">Catat tanggal penting: RUPS, Dividen, atau Jatuh Tempo.
                    </p>
                </div>
                <button @click="showModal = true"
                    class="bg-indigo-600 text-white px-5 py-3 rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 hover:-translate-y-0.5 flex items-center gap-2">
                    <span>+</span> Tambah Agenda
                </button>
            </div>

            @if(session('success'))
            <div
                class="bg-emerald-50 border border-emerald-100 text-emerald-700 p-4 mb-6 rounded-2xl flex items-center gap-3 shadow-sm">
                <span class="bg-emerald-100 p-1 rounded-full">✅</span>
                <p class="font-bold text-sm">{{ session('success') }}</p>
            </div>
            @endif

            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 min-h-[500px]">

                <div class="border-l-2 border-indigo-50 pl-8 space-y-10 relative mt-2">

                    @forelse($events as $event)
                    <div class="relative group">
                        @php
                        $colorClass = match($event->type) {
                        'dividend' => 'bg-emerald-500 border-emerald-100',
                        'rups' => 'bg-indigo-500 border-indigo-100',
                        'bond' => 'bg-orange-500 border-orange-100',
                        default => 'bg-slate-400 border-slate-100',
                        };
                        $textClass = match($event->type) {
                        'dividend' => 'text-emerald-600 bg-emerald-50',
                        'rups' => 'text-indigo-600 bg-indigo-50',
                        'bond' => 'text-orange-600 bg-orange-50',
                        default => 'text-slate-500 bg-slate-100',
                        };
                        @endphp

                        <div
                            class="absolute -left-[43px] top-1 w-6 h-6 rounded-full border-4 {{ $colorClass }} shadow-sm z-10">
                        </div>

                        <div
                            class="flex flex-col md:flex-row justify-between items-start gap-4 p-4 rounded-2xl hover:bg-slate-50 transition border border-transparent hover:border-slate-100">

                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md {{ $textClass }}">
                                        {{ ucfirst($event->type) }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-400">
                                        {{ \Carbon\Carbon::parse($event->event_date)->diffForHumans() }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800">{{ $event->title }}</h3>
                                <p class="text-slate-500 text-sm mt-1 leading-relaxed">{{ $event->description ?: '-' }}
                                </p>
                            </div>

                            <div class="text-right flex flex-col items-end gap-2">
                                <div
                                    class="font-bold text-slate-700 bg-white border border-slate-200 px-4 py-2 rounded-xl shadow-sm text-center min-w-[100px]">
                                    <span
                                        class="block text-2xl font-black">{{ \Carbon\Carbon::parse($event->event_date)->format('d') }}</span>
                                    <span
                                        class="block text-xs uppercase text-slate-400">{{ \Carbon\Carbon::parse($event->event_date)->format('M Y') }}</span>
                                </div>

                                <form action="{{ route('tools.calendar.destroy', $event->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Hapus jadwal ini?')"
                                        class="text-red-400 hover:text-red-600 text-xs font-bold py-1 px-2 rounded hover:bg-red-50 transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-16">
                        <div
                            class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                            📅</div>
                        <h3 class="text-slate-800 font-bold mb-1">Belum ada agenda</h3>
                        <p class="text-slate-400 text-sm">Tambahkan jadwal dividen atau RUPS pertamamu.</p>
                    </div>
                    @endforelse

                </div>
            </div>

        </main>
    </div>

    <div x-show="showModal" x-cloak
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        x-transition.opacity>

        <div @click.away="showModal = false"
            class="bg-white rounded-[2rem] w-full max-w-md p-8 shadow-2xl transform transition-all scale-100">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">📝 Tambah Agenda</h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('tools.calendar.store') }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Judul
                            Agenda</label>
                        <input type="text" name="title" placeholder="Contoh: RUPS BBCA"
                            class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 font-bold text-slate-700"
                            required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal</label>
                            <input type="date" name="event_date"
                                class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl outline-none focus:border-indigo-500 font-bold text-slate-700"
                                required>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tipe</label>
                            <select name="type"
                                class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl outline-none focus:border-indigo-500 font-bold text-slate-700 bg-white">
                                <option value="dividend">💰 Dividen</option>
                                <option value="rups">📢 RUPS</option>
                                <option value="bond">📜 Obligasi</option>
                                <option value="other">📌 Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Catatan
                            (Opsional)</label>
                        <textarea name="description" rows="3" placeholder="Detail tambahan..."
                            class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl outline-none focus:border-indigo-500 font-bold text-slate-700"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-8 pt-4 border-t border-slate-50">
                    <button type="button" @click="showModal = false"
                        class="flex-1 py-3 text-slate-500 font-bold hover:bg-slate-50 rounded-xl transition">Batal</button>
                    <button type="submit"
                        class="flex-1 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>