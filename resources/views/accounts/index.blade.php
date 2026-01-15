@extends('layouts.app')

@section('title', 'Rekening & RDN')

@section('content')
<div class="min-h-screen bg-slate-50 pt-24 pb-12 px-4 sm:px-6 lg:px-8" x-data="{ 
        showModal: false, 
        isEdit: false, 
        form: { id: '', name: '', bank_name: '' } 
     }">

    <div class="max-w-6xl mx-auto space-y-8">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span
                        class="bg-white text-teal-600 p-2 rounded-2xl border border-slate-100 shadow-sm text-2xl">🏦</span>
                    Rekening & RDN
                </h1>
                <p class="text-slate-500 font-medium mt-2 ml-1">Kelola daftar akun sekuritas dan dompet RDN kamu.</p>
            </div>

            <button @click="showModal = true; isEdit = false; form = { id: '', name: '', bank_name: '' }"
                class="group bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:scale-105 transition-all flex items-center gap-2">
                <i class="fas fa-plus-circle text-lg"></i>
                <span>Tambah Rekening</span>
            </button>
        </div>

        @if(session('success'))
        <div
            class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm animate-fade-in-down">
            <i class="fas fa-check-circle text-xl"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
        @endif

        <div
            class="relative overflow-hidden bg-gradient-to-br from-slate-800 to-slate-900 rounded-[2.5rem] p-8 md:p-10 text-white shadow-2xl shadow-slate-200">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-teal-500 rounded-full opacity-20 blur-3xl">
            </div>
            <div
                class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-indigo-500 rounded-full opacity-20 blur-3xl">
            </div>

            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-6">
                    <div
                        class="w-20 h-20 rounded-3xl bg-white/10 backdrop-blur-md flex items-center justify-center text-4xl shadow-inner border border-white/20">
                        💰
                    </div>
                    <div>
                        <p class="text-slate-300 text-sm font-bold uppercase tracking-wider mb-1">Total Aset (Cash)</p>
                        @php $totalSaldo = isset($accounts) ? $accounts->sum('balance') : 0; @endphp
                        <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight">
                            Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                        </h2>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-sm px-6 py-3 rounded-2xl border border-white/10">
                    <span class="text-slate-200 font-medium text-sm">Tersebar di <b>{{ $accounts->count() }}</b>
                        Rekening</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                <h3 class="font-black text-xl text-slate-800 flex items-center gap-2">
                    <span>📋</span> Daftar Akun
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead
                        class="bg-slate-50/50 text-slate-500 text-xs uppercase font-extrabold tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-5">Nama Akun</th>
                            <th class="px-8 py-5">Bank (RDN)</th>
                            <th class="px-8 py-5 text-right">Saldo</th>
                            <th class="px-8 py-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($accounts as $account)
                        <tr class="hover:bg-slate-50 transition duration-150 group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center text-xl group-hover:bg-white group-hover:shadow-md transition-all">
                                        🏦
                                    </div>
                                    <div>
                                        <span class="block font-bold text-slate-800 text-lg">{{ $account->name }}</span>
                                        <span
                                            class="text-xs text-slate-400 font-bold bg-slate-100 px-2 py-0.5 rounded">ID:
                                            #{{ $account->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-600 border border-indigo-100">
                                    {{ $account->bank_name }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <span class="font-black text-teal-600 text-lg tracking-tight">
                                    Rp {{ number_format($account->balance, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('accounts.show', $account->id) }}"
                                        class="text-xs font-bold bg-slate-100 text-slate-600 px-4 py-2 rounded-xl hover:bg-teal-50 hover:text-teal-700 transition flex items-center gap-2">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>

                                    <button
                                        @click="showModal = true; isEdit = true; form = { id: '{{ $account->id }}', name: '{{ $account->name }}', bank_name: '{{ $account->bank_name }}' }"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition"
                                        title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>

                                    <form action="{{ route('accounts.destroy', $account->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Hapus rekening ini? Data tidak bisa dikembalikan.');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                            title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-20 text-center text-slate-500">
                                <div class="flex flex-col items-center opacity-60">
                                    <i class="fas fa-wallet text-4xl text-slate-300 mb-4"></i>
                                    <p class="font-bold text-lg">Belum ada rekening</p>
                                    <button
                                        @click="showModal = true; isEdit = false; form = { id: '', name: '', bank_name: '' }"
                                        class="text-indigo-600 font-bold hover:underline mt-2">+ Tambah
                                        Sekarang</button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showModal = false" x-transition.opacity>
        </div>

        <div class="relative bg-white rounded-[2.5rem] w-full max-w-md shadow-2xl p-8" x-transition.scale>
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-2xl font-black text-slate-800" x-text="isEdit ? 'Edit Akun' : 'Akun Baru'"></h3>
                    <p class="text-slate-400 text-sm mt-1">Isi detail informasi rekening.</p>
                </div>
                <button @click="showModal = false"
                    class="w-10 h-10 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-center text-slate-400 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form :action="isEdit ? '{{ url('accounts') }}/' + form.id : '{{ route('accounts.store') }}'" method="POST"
                class="space-y-6">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Nama Sekuritas /
                            Aplikasi</label>
                        <input type="text" name="name" x-model="form.name" placeholder="Contoh: Bibit, Ajaib"
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-indigo-500 focus:bg-white outline-none transition font-bold text-slate-700"
                            required>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Bank /
                            RDN</label>
                        <input type="text" name="bank_name" x-model="form.bank_name" placeholder="Contoh: RDN Jago"
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-indigo-500 focus:bg-white outline-none transition font-bold text-slate-700"
                            required>
                    </div>

                    <template x-if="!isEdit">
                        <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100">
                            <label class="block text-xs font-extrabold text-blue-600 uppercase mb-2 ml-1">Saldo Awal
                                (Opsional)</label>
                            <input type="number" name="initial_balance" placeholder="Rp 0"
                                class="w-full px-5 py-3 bg-white border border-blue-200 rounded-xl focus:border-blue-500 outline-none transition font-bold text-slate-700">
                            <p class="text-[10px] text-blue-400 mt-2 ml-1">*Hanya diisi jika akun ini sudah memiliki
                                saldo sebelumnya.</p>
                        </div>
                    </template>
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 text-white font-bold text-lg py-4 rounded-2xl hover:bg-indigo-700 transition shadow-xl shadow-indigo-500/30 active:scale-[0.98]">
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Buat Akun Sekarang'"></span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection