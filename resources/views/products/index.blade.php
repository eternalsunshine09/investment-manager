@extends('layouts.app')

@section('title', 'Master Data Produk - Porto Tracking')

@section('content')
<div class="h-full overflow-y-auto pt-20 md:pt-8 pb-10 px-4" x-data="{ 
        showCreateModal: false, 
        showEditModal: false,
        editData: { id: null, category: '', sub_category: '', code: '', name: '' } 
     }">

    <div class="max-w-[1600px] mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                    <span class="text-indigo-500">🧩</span> Master Data Produk
                </h1>
                <p class="text-slate-500 font-medium mt-1">Daftar aset investasi yang terdaftar di sistem.</p>
            </div>

            <button @click="showCreateModal = true"
                class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 hover:scale-105 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Produk
            </button>
        </div>

        @if(session('success'))
        <div
            class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-xl"></i>
            <p class="font-bold">{{ session('success') }}</p>
        </div>
        @endif

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-5 font-bold text-slate-500 text-xs uppercase tracking-wider w-1/4">Kategori</th>
                        <th class="p-5 font-bold text-slate-500 text-xs uppercase tracking-wider w-1/6">Kode</th>
                        <th class="p-5 font-bold text-slate-500 text-xs uppercase tracking-wider">Nama Produk</th>
                        <th class="p-5 font-bold text-slate-500 text-xs uppercase tracking-wider text-center w-1/6">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50 transition group">
                        <td class="p-5">
                            <span
                                class="px-3 py-1 rounded-lg text-xs font-bold uppercase border
                                {{ $product->category == 'saham' ? 'bg-orange-50 text-orange-600 border-orange-100' : '' }}
                                {{ $product->category == 'crypto' ? 'bg-purple-50 text-purple-600 border-purple-100' : '' }}
                                {{ $product->category == 'emas' ? 'bg-yellow-50 text-yellow-600 border-yellow-100' : '' }}
                                {{ $product->category == 'reksadana' ? 'bg-blue-50 text-blue-600 border-blue-100' : '' }}
                                {{ $product->category == 'valas' ? 'bg-green-50 text-green-600 border-green-100' : '' }}">
                                {{ $product->category }}
                                @if($product->sub_category)
                                <span class="opacity-50 mx-1">|</span> {{ $product->sub_category }}
                                @endif
                            </span>
                        </td>
                        <td class="p-5">
                            <span
                                class="font-black text-slate-800 bg-slate-100 px-2 py-1 rounded">{{ $product->code }}</span>
                        </td>
                        <td class="p-5 text-slate-600 font-bold">{{ $product->name }}</td>
                        <td class="p-5 text-center flex justify-center gap-2">
                            <button @click="editData = { 
                                        id: {{ $product->id }}, 
                                        category: '{{ $product->category }}', 
                                        sub_category: '{{ $product->sub_category }}',
                                        code: '{{ $product->code }}', 
                                        name: '{{ $product->name }}' 
                                    }; showEditModal = true"
                                class="text-slate-400 hover:text-indigo-600 transition p-2 hover:bg-indigo-50 rounded-lg"
                                title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>

                            <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                onsubmit="return confirm('Hapus produk ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-slate-400 hover:text-red-500 transition p-2 hover:bg-red-50 rounded-lg"
                                    title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-16 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-300">
                                <i class="fas fa-box-open text-5xl mb-4"></i>
                                <p class="text-slate-500 font-bold">Belum ada Master Data.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="showCreateModal" x-cloak
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4"
        x-transition.opacity>
        <div @click.away="showCreateModal = false"
            class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl overflow-hidden p-8"
            x-data="{ selectedCat: 'saham' }">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">🧩 Produk Baru</h3>
                <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600"><i
                        class="fas fa-times text-xl"></i></button>
            </div>
            <form action="{{ route('products.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Kategori</label>
                    <select name="category" x-model="selectedCat"
                        class="w-full border border-slate-200 p-3.5 rounded-xl bg-slate-50 font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="saham">Saham</option>
                        <option value="reksadana">Reksadana</option>
                        <option value="crypto">Crypto</option>
                        <option value="emas">Emas</option>
                        <option value="valas">Valas</option>
                    </select>
                </div>

                <div x-show="selectedCat === 'reksadana'" x-transition
                    class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                    <label class="block text-xs font-bold text-blue-600 uppercase mb-1">Jenis Reksadana</label>
                    <select name="sub_category"
                        class="w-full border border-blue-200 p-2.5 rounded-xl bg-white text-blue-800 font-bold outline-none">
                        <option value="Pasar Uang">Pasar Uang (RDPU)</option>
                        <option value="Pendapatan Tetap">Pendapatan Tetap (RDPT)</option>
                        <option value="Saham">Saham (RDS)</option>
                        <option value="Campuran">Campuran</option>
                    </select>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-1">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Kode</label>
                        <input type="text" name="code" placeholder="BBCA"
                            class="w-full border border-slate-200 p-3.5 rounded-xl font-black uppercase text-slate-800 outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Nama Produk</label>
                        <input type="text" name="name" placeholder="Bank Central Asia"
                            class="w-full border border-slate-200 p-3.5 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 text-white font-bold py-4 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition transform hover:-translate-y-1 mt-4">Simpan</button>
            </form>
        </div>
    </div>

    <div x-show="showEditModal" x-cloak
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4"
        x-transition.opacity>
        <div @click.away="showEditModal = false"
            class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl overflow-hidden p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">✏️ Edit Produk</h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600"><i
                        class="fas fa-times text-xl"></i></button>
            </div>
            <form x-bind:action="'/products/' + editData.id" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Kategori</label>
                    <select name="category" x-model="editData.category"
                        class="w-full border border-slate-200 p-3.5 rounded-xl bg-slate-50 font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="saham">Saham</option>
                        <option value="reksadana">Reksadana</option>
                        <option value="crypto">Crypto</option>
                        <option value="emas">Emas</option>
                        <option value="valas">Valas</option>
                    </select>
                </div>

                <div x-show="editData.category === 'reksadana'" x-transition
                    class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                    <label class="block text-xs font-bold text-blue-600 uppercase mb-1">Jenis Reksadana</label>
                    <select name="sub_category" x-model="editData.sub_category"
                        class="w-full border border-blue-200 p-2.5 rounded-xl bg-white text-blue-800 font-bold outline-none">
                        <option value="Pasar Uang">Pasar Uang (RDPU)</option>
                        <option value="Pendapatan Tetap">Pendapatan Tetap (RDPT)</option>
                        <option value="Saham">Saham (RDS)</option>
                        <option value="Campuran">Campuran</option>
                    </select>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-1">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Kode</label>
                        <input type="text" name="code" x-model="editData.code"
                            class="w-full border border-slate-200 p-3.5 rounded-xl font-black uppercase text-slate-800 outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Nama Produk</label>
                        <input type="text" name="name" x-model="editData.name"
                            class="w-full border border-slate-200 p-3.5 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-emerald-600 text-white font-bold py-4 rounded-xl hover:bg-emerald-700 shadow-lg shadow-emerald-500/30 transition transform hover:-translate-y-1 mt-4">Update
                    Data</button>
            </form>
        </div>
    </div>
</div>
@endsection