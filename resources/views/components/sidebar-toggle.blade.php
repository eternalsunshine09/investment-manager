<aside
    class="w-full h-full bg-white border-r border-gray-200 flex flex-col font-sans overflow-y-auto custom-scrollbar shadow-lg">
    <!-- Header dengan Tombol Close -->
    <div
        class="h-24 flex items-center px-8 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50 relative">
        <div class="flex items-center gap-3">
            <div
                class="bg-gradient-to-tr from-indigo-500 to-purple-600 text-white p-2.5 rounded-xl shadow-lg shadow-indigo-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div>
                <span class="text-2xl font-black text-gray-900 tracking-tight block leading-none">Porto Tracking</span>
            </div>
        </div>

        <!-- Tombol Close (hanya untuk mobile) -->
        <button class="absolute right-6 text-gray-600 hover:text-indigo-600 transition md:hidden"
            @click="$dispatch('close-sidebar')">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-4 space-y-1 overflow-y-auto py-6 custom-scrollbar">
        <!-- ... navigation items tetap sama ... -->
    </nav>

    <!-- Footer dengan User Info -->
    <div class="p-5 border-t border-gray-200 bg-gray-50">
        <!-- User Profile -->
        <div class="flex items-center gap-3 mb-4 p-3 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div
                class="w-10 h-10 rounded-lg bg-gradient-to-tr from-indigo-500 to-purple-600 text-white flex items-center justify-center font-black text-lg shrink-0">
                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name ?? 'Guest' }}</p>
            </div>
        </div>

        <!-- Logout Button -->
        <div class="mt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full px-4 py-3 bg-white text-gray-700 border border-gray-200 rounded-xl font-bold hover:border-rose-300 hover:text-rose-600 transition flex items-center justify-center shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</aside>