<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekening & RDN - Investment Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-slate-50 font-sans" x-data="{ showModal: false }">

    <div class="flex h-screen overflow-hidden">

        <x-sidebar />

        <main class="flex-1 overflow-y-auto p-8">

            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800">🏦 Rekening & RDN</h1>
                    <p class="text-gray-500 mt-1">Daftar akun sekuritas, crypto exchange, atau dompet RDN kamu.</p>
                </div>

                <button @click="showModal = true"
                    class="bg-teal-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-teal-700 shadow-lg shadow-teal-500/30 transition flex items-center gap-2">
                    <span>+</span> Tambah Rekening
                </button>
            </div>

            @if(session('success'))
            <div
                class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r flex items-center shadow-sm">
                <span class="text-xl mr-2">✅</span>
                <p>{{ session('success') }}</p>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                @php $totalSaldo = $accounts->sum('balance'); @endphp
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-4 bg-teal-50 text-teal-600 rounded-full text-2xl">💰</div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase">Total Cash (RDN)</p>
                        <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="p-5 font-bold text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                Nama Akun / Sekuritas</th>
                            <th
                                class="p-5 font-bold text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                Detail Bank (RDN)</th>
                            <th
                                class="p-5 font-bold text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100 text-right">
                                Saldo Saat Ini</th>
                            <th
                                class="p-5 font-bold text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100 text-center">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($accounts as $account)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="p-5">
                                <span class="font-bold text-slate-800 text-base">{{ $account->name }}</span>
                            </td>
                            <td class="p-5 text-gray-600">
                                <span
                                    class="bg-gray-100 px-2 py-1 rounded text-xs font-mono font-bold">{{ $account->bank_name }}</span>
                            </td>
                            <td class="p-5 text-right font-bold text-teal-600 text-base">
                                Rp {{ number_format($account->balance, 0, ',', '.') }}
                            </td>
                            <td class="p-5 text-center">
                                <button class="text-gray-400 hover:text-red-500 transition" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <span class="text-4xl mb-2">🏦</span>
                                    <p>Belum ada rekening terdaftar.</p>
                                    <p class="text-sm">Klik tombol "Tambah Rekening" di atas.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div x-show="showModal"
        class="fixed inset-0 bg-slate-900 bg-opacity-50 z-50 flex items-center justify-center p-4 backdrop-blur-sm"
        style="display: none;" x-transition.opacity>

        <div @click.away="showModal = false"
            class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden transform transition-all p-6">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">🏦 Tambah Akun Baru</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-red-500 transition">✕</button>
            </div>

            <form action="/accounts" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Nama Aplikasi / Sekuritas</label>
                        <input type="text" name="name" placeholder="Contoh: BCA Sekuritas, Bibit, Indodax"
                            class="w-full border border-gray-200 p-2.5 rounded-xl focus:border-teal-500 focus:ring-2 focus:ring-teal-100 transition outline-none"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Info Bank / RDN</label>
                        <input type="text" name="bank_name" placeholder="Contoh: RDN BCA, Bank Jago"
                            class="w-full border border-gray-200 p-2.5 rounded-xl focus:border-teal-500 focus:ring-2 focus:ring-teal-100 transition outline-none"
                            required>
                    </div>

                    <div class="bg-blue-50 p-3 rounded-lg flex gap-3 items-start">
                        <span class="text-blue-500 text-lg">ℹ️</span>
                        <p class="text-xs text-blue-600 leading-relaxed">
                            Saldo awal akan otomatis <b>Rp 0</b>. <br>
                            Untuk mengisi saldo, silakan lakukan <b>Transaksi Top-Up</b> nanti.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" @click="showModal = false"
                        class="text-gray-500 font-bold px-4 py-2 hover:bg-gray-100 rounded-lg transition">Batal</button>
                    <button type="submit"
                        class="bg-teal-600 text-white font-bold px-6 py-2 rounded-lg hover:bg-teal-700 shadow-lg shadow-teal-500/30 transition transform hover:-translate-y-1">Simpan
                        Akun</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>