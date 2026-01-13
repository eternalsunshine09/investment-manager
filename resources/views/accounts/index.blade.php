@extends('layouts.app')

@section('title', 'Rekening & RDN - Porto Tracking')

@section('content')
<div class="h-full overflow-y-auto pt-20 md:pt-8 pb-10 px-4" x-data="{ 
        showModal: false, 
        isEdit: false, 
        form: { id: '', name: '', bank_name: '' } 
     }">

    <div class="max-w-[1600px] mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                    <span class="text-teal-500">🏦</span> Rekening & RDN
                </h1>
                <p class="text-slate-500 font-medium mt-1">Daftar akun sekuritas, crypto exchange, atau dompet RDN kamu.
                </p>
            </div>

            <button @click="showModal = true; isEdit = false; form = { id: '', name: '', bank_name: '' }"
                class="bg-teal-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-teal-500/30 hover:bg-teal-700 hover:scale-105 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Rekening
            </button>
        </div>

        @if(session('success'))
        <div
            class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-xl"></i>
            <p class="font-bold">{{ session('success') }}</p>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @php $totalSaldo = isset($accounts) ? $accounts->sum('balance') : 0; @endphp
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center gap-5">
                <div class="p-4 bg-teal-50 text-teal-600 rounded-2xl text-2xl border border-teal-100">
                    💰
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total Cash (RDN)</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1">Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-5 font-bold text-slate-500 text-xs uppercase tracking-wider">Nama Akun / Sekuritas
                        </th>
                        <th class="p-5 font-bold text-slate-500 text-xs uppercase tracking-wider">Detail Bank (RDN)</th>
                        <th class="p-5 font-bold text-slate-500 text-xs uppercase tracking-wider text-right">Saldo Saat
                            Ini</th>
                        <th class="p-5 font-bold text-slate-500 text-xs uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @if(isset($accounts) && $accounts->count() > 0)
                    @foreach($accounts as $account)
                    <tr class="hover:bg-slate-50 transition group">
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-lg">
                                    🏦
                                </div>
                                <span class="font-bold text-slate-800 text-base">{{ $account->name }}</span>
                            </div>
                        </td>
                        <td class="p-5 text-slate-600">
                            <span
                                class="bg-slate-100 px-3 py-1 rounded-lg text-xs font-bold text-slate-500">{{ $account->bank_name }}</span>
                        </td>
                        <td class="p-5 text-right font-black text-teal-600 text-base">
                            Rp {{ number_format($account->balance, 0, ',', '.') }}
                        </td>
                        <td class="p-5 text-center flex justify-center gap-2">
                            <a href="{{ route('transactions.index', ['account_id' => $account->id]) }}"
                                class="text-slate-300 hover:text-teal-600 transition p-2 hover:bg-teal-50 rounded-lg"
                                title="Lihat Riwayat">
                                <i class="fas fa-history"></i>
                            </a>

                            <button @click="showModal = true; 
                                                isEdit = true; 
                                                form = { 
                                                    id: '{{ $account->id }}', 
                                                    name: '{{ $account->name }}', 
                                                    bank_name: '{{ $account->bank_name }}' 
                                                }"
                                class="text-slate-300 hover:text-indigo-600 transition p-2 hover:bg-indigo-50 rounded-lg"
                                title="Edit Akun">
                                <i class="fas fa-pencil-alt"></i>
                            </button>

                            <form action="{{ route('accounts.destroy', $account->id) }}" method="POST"
                                onsubmit="return confirm('Hapus rekening ini? Semua riwayat transaksi terkait juga akan terhapus.');">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-slate-300 hover:text-red-500 transition p-2 hover:bg-red-50 rounded-lg"
                                    title="Hapus Akun">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="4" class="p-16 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-300">
                                <i class="fas fa-university text-5xl mb-4"></i>
                                <p class="text-slate-500 font-bold">Belum ada rekening terdaftar.</p>
                                <p class="text-xs text-slate-400 mt-1">Klik tombol "Tambah Rekening" di atas.</p>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

    </div>

    <div x-show="showModal" x-cloak
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4"
        x-transition.opacity>

        <div @click.away="showModal = false"
            class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl overflow-hidden p-8 transform transition-all">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800" x-text="isEdit ? '✏️ Edit Akun' : '🏦 Tambah Akun Baru'">
                </h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form x-bind:action="isEdit ? '/accounts/' + form.id : '{{ route('accounts.store') }}'" method="POST"
                class="space-y-5">
                @csrf
                <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Nama Aplikasi /
                        Sekuritas</label>
                    <input type="text" name="name" x-model="form.name" placeholder="Contoh: BCA Sekuritas, Bibit"
                        class="w-full border border-slate-200 p-3.5 rounded-xl focus:border-teal-500 focus:ring-2 focus:ring-teal-100 transition outline-none font-bold text-slate-700"
                        required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Info Bank / RDN</label>
                    <input type="text" name="bank_name" x-model="form.bank_name" placeholder="Contoh: RDN BCA"
                        class="w-full border border-slate-200 p-3.5 rounded-xl focus:border-teal-500 focus:ring-2 focus:ring-teal-100 transition outline-none font-medium text-slate-700"
                        required>
                </div>

                <div x-show="!isEdit" class="bg-blue-50 p-4 rounded-xl flex gap-3 items-start border border-blue-100">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <p class="text-xs text-blue-700 font-medium leading-relaxed">
                        Saldo awal otomatis <b>Rp 0</b>. <br>
                        Lakukan <b>Transaksi Top-Up</b> nanti untuk mengisi saldo.
                    </p>
                </div>

                <button type="submit"
                    class="w-full bg-teal-600 text-white font-bold py-4 rounded-xl hover:bg-teal-700 shadow-lg shadow-teal-500/30 transition transform hover:-translate-y-1">
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Akun'"></span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection