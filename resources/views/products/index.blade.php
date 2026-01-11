<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Data - Investment Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-slate-50 font-sans" x-data="{ 
          showCreateModal: false, 
          showEditModal: false,
          editData: { id: null, category: '', sub_category: '', code: '', name: '' } 
      }">

    <div class="flex h-screen overflow-hidden">

        <x-sidebar />

        <main class="flex-1 overflow-y-auto p-8">

            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800">🧩 Master Data</h1>
                    <p class="text-gray-500 mt-1">Daftar aset investasi yang kamu miliki.</p>
                </div>

                <button @click="showCreateModal = true"
                    class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition flex items-center gap-2">
                    <span>+</span> Tambah Produk Baru
                </button>
            </div>

            @if(session('success'))
            <div
                class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r flex items-center shadow-sm">
                <span class="text-xl mr-2">✅</span>
                <p>{{ session('success') }}</p>
            </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="p-5 font-bold text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100 w-1/4">
                                Kategori</th>
                            <th
                                class="p-5 font-bold text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100 w-1/6">
                                Kode</th>
                            <th
                                class="p-5 font-bold text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                Nama Produk</th>
                            <th
                                class="p-5 font-bold text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100 text-center w-1/6">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($products as $product)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="p-5">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase
                                    {{ $product->category == 'saham' ? 'bg-orange-100 text-orange-600' : '' }}
                                    {{ $product->category == 'crypto' ? 'bg-purple-100 text-purple-600' : '' }}
                                    {{ $product->category == 'emas' ? 'bg-yellow-100 text-yellow-600' : '' }}
                                    {{ $product->category == 'reksadana' ? 'bg-blue-100 text-blue-600' : '' }}
                                    {{ $product->category == 'valas' ? 'bg-green-100 text-green-600' : '' }}">

                                    {{ $product->category }}

                                    {{-- Tampilkan Sub Kategori jika ada --}}
                                    @if($product->sub_category)
                                    <span class="opacity-70 text-[10px] ml-1 border-l border-current pl-1">
                                        {{ $product->sub_category }}
                                    </span>
                                    @endif
                                </span>
                            </td>
                            <td class="p-5 font-bold text-slate-800">{{ $product->code }}</td>
                            <td class="p-5 text-gray-600 font-medium">{{ $product->name }}</td>

                            <td class="p-5 text-center flex justify-center gap-2">
                                <button @click="editData = { 
                                        id: {{ $product->id }}, 
                                        category: '{{ $product->category }}', 
                                        sub_category: '{{ $product->sub_category }}',
                                        code: '{{ $product->code }}', 
                                        name: '{{ $product->name }}' 
                                    }; showEditModal = true"
                                    class="text-indigo-400 hover:text-indigo-600 transition p-1" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-300 hover:text-red-500 transition p-1"
                                        title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <span class="text-4xl mb-2">📭</span>
                                    <p>Belum ada Master Data.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div x-show="showCreateModal" x-cloak
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        x-transition.opacity>

        <div @click.away="showCreateModal = false"
            class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden p-6"
            x-data="{ selectedCat: 'saham' }">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">🧩 Tambah Produk Baru</h3>
                <button @click="showCreateModal = false" class="text-gray-400 hover:text-red-500 transition">✕</button>
            </div>

            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Kategori Investasi</label>
                        <select name="category" x-model="selectedCat"
                            class="w-full border border-gray-200 p-2.5 rounded-xl bg-gray-50 focus:bg-white focus:border-indigo-500 outline-none">
                            <option value="saham">Saham</option>
                            <option value="reksadana">Reksadana</option>
                            <option value="crypto">Crypto</option>
                            <option value="emas">Emas</option>
                            <option value="valas">Valas</option>
                        </select>
                    </div>

                    <div x-show="selectedCat === 'reksadana'" x-transition
                        class="bg-blue-50 p-3 rounded-xl border border-blue-100">
                        <label class="block text-sm font-bold text-blue-600 mb-1">Jenis Reksadana</label>
                        <select name="sub_category"
                            class="w-full border border-blue-200 p-2.5 rounded-xl bg-white focus:border-blue-500 outline-none text-blue-800 font-medium">
                            <option value="Pasar Uang">Pasar Uang (RDPU)</option>
                            <option value="Pendapatan Tetap">Pendapatan Tetap (RDPT)</option>
                            <option value="Saham">Saham (RDS)</option>
                            <option value="Campuran">Campuran</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Kode / Ticker</label>
                        <input type="text" name="code" placeholder="Contoh: BBCA"
                            class="w-full border border-gray-200 p-2.5 rounded-xl focus:border-indigo-500 outline-none uppercase font-bold"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Nama Produk</label>
                        <input type="text" name="name" placeholder="Contoh: Bank Central Asia"
                            class="w-full border border-gray-200 p-2.5 rounded-xl focus:border-indigo-500 outline-none"
                            required>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" @click="showCreateModal = false"
                        class="text-gray-500 font-bold px-4 py-2 hover:bg-gray-100 rounded-lg">Batal</button>
                    <button type="submit"
                        class="bg-indigo-600 text-white font-bold px-6 py-2 rounded-lg hover:bg-indigo-700 shadow-lg shadow-indigo-500/30">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showEditModal" x-cloak
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        x-transition.opacity>

        <div @click.away="showEditModal = false"
            class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden p-6">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">✏️ Edit Produk</h3>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-red-500 transition">✕</button>
            </div>

            <form x-bind:action="'/products/' + editData.id" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Kategori Investasi</label>
                        <select name="category" x-model="editData.category"
                            class="w-full border border-gray-200 p-2.5 rounded-xl bg-gray-50 focus:bg-white focus:border-indigo-500 outline-none">
                            <option value="saham">Saham</option>
                            <option value="reksadana">Reksadana</option>
                            <option value="crypto">Crypto</option>
                            <option value="emas">Emas</option>
                            <option value="valas">Valas</option>
                        </select>
                    </div>

                    <div x-show="editData.category === 'reksadana'" x-transition
                        class="bg-blue-50 p-3 rounded-xl border border-blue-100">
                        <label class="block text-sm font-bold text-blue-600 mb-1">Jenis Reksadana</label>
                        <select name="sub_category" x-model="editData.sub_category"
                            class="w-full border border-blue-200 p-2.5 rounded-xl bg-white focus:border-blue-500 outline-none text-blue-800 font-medium">
                            <option value="">- Pilih Jenis -</option>
                            <option value="Pasar Uang">Pasar Uang (RDPU)</option>
                            <option value="Pendapatan Tetap">Pendapatan Tetap (RDPT)</option>
                            <option value="Saham">Saham (RDS)</option>
                            <option value="Campuran">Campuran</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Kode / Ticker</label>
                        <input type="text" name="code" x-model="editData.code"
                            class="w-full border border-gray-200 p-2.5 rounded-xl focus:border-indigo-500 outline-none uppercase font-bold"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Nama Produk</label>
                        <input type="text" name="name" x-model="editData.name"
                            class="w-full border border-gray-200 p-2.5 rounded-xl focus:border-indigo-500 outline-none"
                            required>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" @click="showEditModal = false"
                        class="text-gray-500 font-bold px-4 py-2 hover:bg-gray-100 rounded-lg">Batal</button>
                    <button type="submit"
                        class="bg-green-600 text-white font-bold px-6 py-2 rounded-lg hover:bg-green-700 shadow-lg shadow-green-500/30">Update
                        Data</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>