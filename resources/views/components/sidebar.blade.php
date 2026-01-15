<aside class="sidebar-container flex flex-col" :class="{ 'active': sidebarOpen }">

    <div class="h-28 flex items-center px-8 border-b border-dashed border-slate-200">
        <div class="mt-8 ml-2">
            <h1 class="text-2xl font-black text-slate-800 tracking-tighter leading-none">PORTO</h1>
            <h1 class="text-2xl font-black text-[#4f46e5] tracking-tighter leading-none">TRACKING</h1>
        </div>
    </div>

    <nav class="flex-1 px-6 py-6 space-y-2 overflow-y-auto custom-scrollbar">

        <a href="{{ route('dashboard') }}"
            class="flex items-center px-4 py-3 rounded-xl font-bold transition group {{ Request::routeIs('dashboard') ? 'bg-[#4f46e5] text-white shadow-lg shadow-indigo-200' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
            <i class="fas fa-th-large w-6 text-center mr-2"></i>
            Dashboard
        </a>

        <p class="px-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mt-6 mb-2">Aset & Data</p>

        <a href="{{ route('portfolio.index') }}" class="nav-item {{ Request::routeIs('portfolio.*') ? 'active' : '' }}">
            <i class="fas fa-briefcase w-6 text-center mr-2 text-emerald-500"></i> Portofolio
        </a>
        <a href="{{ route('asset.summary') }}" class="nav-item {{ Request::routeIs('asset.summary') ? 'active' : '' }}">
            <i class="fas fa-chart-line w-6 text-center mr-2 text-blue-500"></i> Harga Aset
        </a>

        <div x-data="{ open: {{ Request::routeIs('products.*') || Request::routeIs('accounts.*') ? 'true' : 'false' }} }"
            class="pt-1">
            <button @click="open = !open"
                class="flex items-center justify-between w-full px-4 py-3 rounded-xl font-bold transition text-slate-500 hover:bg-slate-50 hover:text-indigo-600">
                <div class="flex items-center">
                    <i class="fas fa-database w-6 text-center mr-2 text-indigo-500"></i>
                    Master Data
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" x-collapse class="pl-12 pr-4 space-y-1 mt-1 border-l-2 border-slate-100 ml-6">
                <a href="{{ route('products.index') }}"
                    class="block py-2 text-sm font-bold {{ Request::routeIs('products.*') ? 'text-indigo-600' : 'text-slate-400 hover:text-indigo-600' }}">
                    📦 Produk Investasi
                </a>
                <a href="{{ route('accounts.index') }}"
                    class="block py-2 text-sm font-bold {{ Request::routeIs('accounts.*') ? 'text-indigo-600' : 'text-slate-400 hover:text-indigo-600' }}">
                    💳 Rekening / RDN
                </a>
            </div>
        </div>

        <p class="px-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mt-6 mb-2">Keuangan</p>

        <a href="{{ route('transactions.index') }}"
            class="nav-item {{ Request::routeIs('transactions.*') ? 'active' : '' }}">
            <i class="fas fa-exchange-alt w-6 text-center mr-2 text-orange-500"></i> Transaksi Aset IDR
        </a>
        <a href="{{ route('cashflow.index') }}" class="nav-item {{ Request::routeIs('cashflow.*') ? 'active' : '' }}">
            <i class="fas fa-wallet w-6 text-center mr-2 text-purple-500"></i> Cash Flow
        </a>

        <p class="px-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mt-6 mb-2">Analisis</p>

        <a href="{{ route('reports.index') }}" class="nav-item {{ Request::routeIs('reports.*') ? 'active' : '' }}">
            <i class="fas fa-file-alt w-6 text-center mr-2 text-rose-500"></i> Laporan
        </a>
        <a href="{{ route('watchlist.index') }}" class="nav-item {{ Request::routeIs('watchlist.*') ? 'active' : '' }}">
            <i class="fas fa-star w-6 text-center mr-2 text-yellow-500"></i> Watchlist
        </a>

    </nav>

    <div class="p-6 border-t border-dashed border-slate-200 bg-slate-50/50 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center font-bold text-indigo-600 shadow-sm">
                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
            </div>
            <div class="flex flex-col">
                <span class="text-xs font-bold text-slate-800">{{ explode(' ', Auth::user()->name)[0] }}</span>
                <span class="text-[10px] text-slate-400">Basic Plan</span>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="Btn">
                <div class="sign">
                    <svg viewBox="0 0 512 512">
                        <path
                            d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z">
                        </path>
                    </svg>
                </div>
                <div class="text">Logout</div>
            </button>
        </form>
    </div>
</aside>

<style>
.nav-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    border-radius: 12px;
    font-weight: 700;
    color: #64748b;
    transition: all 0.2s;
}

.nav-item:hover {
    background-color: #f1f5f9;
    color: #4f46e5;
}

.nav-item.active {
    background-color: #e0e7ff;
    color: #4338ca;
}
</style>