@extends('layouts.app')

@section('title', 'Rekening & RDN - Porto Tracking')

@section('content')
<div class="min-h-screen bg-slate-50 pt-24 pb-12 px-4 sm:px-6 lg:px-8" x-data="{ 
        showModal: false, 
        isEdit: false, 
        form: { id: '', name: '', bank_name: '' } 
     }">

    <div class="max-w-7xl mx-auto space-y-8">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span class="bg-teal-100 text-teal-600 p-2 rounded-xl text-2xl shadow-sm">🏦</span>
                    Rekening & RDN
                </h1>
                <p class="text-slate-500 font-medium mt-2 ml-1">Kelola daftar akun sekuritas dan dompet RDN kamu di
                    sini.</p>
            </div>

            <button @click="showModal = true; isEdit = false; form = { id: '', name: '', bank_name: '' }"
                class="group bg-teal-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-teal-500/20 hover:bg-teal-700 hover:shadow-teal-500/40 hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-plus transition-transform group-hover:rotate-90"></i>
                <span>Tambah Rekening</span>
            </button>
        </div>

        @if(session('success'))
        <div
            class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in-down">
            <i class="fas fa-check-circle text-xl"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php $totalSaldo = isset($accounts) ? $accounts->sum('balance') : 0; @endphp
            <div
                class="relative overflow-hidden bg-white p-6 rounded-2xl shadow-sm border border-slate-200 group hover:border-teal-300 transition-colors duration-300">
                <div
                    class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-teal-50 rounded-full opacity-50 blur-xl group-hover:bg-teal-100 transition-colors">
                </div>

                <div class="flex items-center gap-4 relative z-10">
                    <div
                        class="p-3 bg-teal-50 text-teal-600 rounded-xl text-2xl shadow-sm group-hover:scale-110 transition-transform duration-300">
                        💰
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total Cash (RDN)</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">
                            Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-5 font-bold text-slate-500 text-xs uppercase tracking-wider">Nama Akun /
                                Sekuritas</th>
                            <th class="px-6 py-5 font-bold text-slate-500 text-xs uppercase tracking-wider">Detail Bank
                                (RDN)</th>
                            <th class="px-6 py-5 font-bold text-slate-500 text-xs uppercase tracking-wider text-right">
                                Saldo Saat Ini</th>
                            <th
                                class="px-6 py-5 font-bold text-slate-500 text-xs uppercase tracking-wider text-center w-48">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($accounts as $account)
                        <tr class="hover:bg-slate-50/80 transition-colors duration-150 group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-lg shadow-inner group-hover:bg-white group-hover:shadow-md transition-all">
                                        🏦
                                    </div>
                                    <div>
                                        <span
                                            class="block font-bold text-slate-800 text-base">{{ $account->name }}</span>
                                        <span class="text-xs text-slate-400">ID: #{{ $account->id }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $account->bank_name }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <span class="font-black text-teal-600 text-base tracking-tight">
                                    Rp {{ number_format($account->balance, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('accounts.show', $account->id) }}"
                                        class="text-xs font-bold bg-teal-50 text-teal-700 px-3 py-2 rounded-lg hover:bg-teal-100 hover:text-teal-800 transition flex items-center gap-1">
                                        <i class="fas fa-history"></i> Riwayat
                                    </a>

                                    <div class="w-px h-4 bg-slate-300 mx-1"></div>

                                    <button @click="showModal = true; 
                                                    isEdit = true; 
                                                    form = { 
                                                        id: '{{ $account->id }}', 
                                                        name: '{{ $account->name }}', 
                                                        bank_name: '{{ $account->bank_name }}' 
                                                    }"
                                        class="text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 p-2 rounded-lg transition"
                                        title="Edit Akun">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>

                                    <form action="{{ route('accounts.destroy', $account->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Hapus rekening ini? Semua riwayat transaksi terkait juga akan terhapus.');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-slate-400 hover:text-rose-600 hover:bg-rose-50 p-2 rounded-lg transition"
                                            title="Hapus Akun">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-university text-3xl text-slate-300"></i>
                                    </div>
                                    <h3 class="text-slate-500 font-bold text-lg">Belum ada rekening</h3>
                                    <p class="text-slate-400 text-sm mt-1 mb-4">Mulai tambahkan akun investasi
                                        pertamamu.</p>
                                    <button
                                        @click="showModal = true; isEdit = false; form = { id: '', name: '', bank_name: '' }"
                                        class="text-teal-600 font-bold hover:underline text-sm">
                                        + Tambah Sekarang
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog" aria-modal="true">

        <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"
            @click="showModal = false"></div>

        <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative bg-white rounded-3xl w-full max-w-md shadow-2xl p-8 overflow-hidden">

            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-xl font-black text-slate-800" x-text="isEdit ? 'Edit Akun' : 'Tambah Akun Baru'">
                    </h3>
                    <p class="text-sm text-slate-500 mt-1"
                        x-text="isEdit ? 'Perbarui informasi rekening.' : 'Masukkan detail rekening baru.'"></p>
                </div>
                <button @click="showModal = false"
                    class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form x-bind:action="isEdit ? '/accounts/' + form.id : '{{ route('accounts.store') }}'" method="POST"
                class="space-y-6">
                @csrf
                <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

                <div class="space-y-4">
                    <div class="group">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Nama Aplikasi /
                            Sekuritas</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i
                                    class="fas fa-wallet text-slate-400 group-focus-within:text-teal-500 transition-colors"></i>
                            </div>
                            <input type="text" name="name" x-model="form.name"
                                placeholder="Contoh: Bibit, Ajaib, Stockbit"
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none font-semibold text-slate-700 placeholder:text-slate-400 text-sm"
                                required>
                        </div>
                    </div>

                    <div class="group">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Info Bank /
                            RDN</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i
                                    class="fas fa-university text-slate-400 group-focus-within:text-teal-500 transition-colors"></i>
                            </div>
                            <input type="text" name="bank_name" x-model="form.bank_name"
                                placeholder="Contoh: RDN Jago, RDN BCA"
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none font-semibold text-slate-700 placeholder:text-slate-400 text-sm"
                                required>
                        </div>
                    </div>
                </div>

                <div x-show="!isEdit"
                    class="bg-blue-50/50 p-4 rounded-xl flex gap-3 items-start border border-blue-100">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <p class="text-xs text-blue-600/80 font-medium leading-relaxed">
                        Saldo awal otomatis <b>Rp 0</b>. Kamu bisa menambahkan saldo melalui menu <b>Transaksi
                            Top-Up</b> setelah akun dibuat.
                    </p>
                </div>

                <button type="submit"
                    class="w-full bg-teal-600 text-white font-bold py-4 rounded-xl hover:bg-teal-700 shadow-lg shadow-teal-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <i class="fas" :class="isEdit ? 'fa-save' : 'fa-plus-circle'"></i>
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Buat Akun Baru'"></span>
                </button>
            </form>
        </div>
    </div>

</div>
@endsection