<body class="bg-[#F8FAFC] text-slate-800" x-data="{ sidebarOpen: window.innerWidth >= 768 }" x-init="
        $watch('sidebarOpen', value => {
          if (value && window.innerWidth < 768) {
            document.body.style.overflow = 'hidden';
          } else {
            document.body.style.overflow = 'auto';
          }
        })
      ">

    <!-- Tombol Toggle -->
    <button class="sidebar-toggle-btn" @click="sidebarOpen = !sidebarOpen" :class="{ 'sidebar-open': sidebarOpen }">
        <svg x-show="!sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <svg x-show="sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Overlay -->
    <div class="sidebar-overlay" x-show="sidebarOpen && window.innerWidth < 768" @click="sidebarOpen = false"
        x-transition.opacity>
    </div>

    <!-- Sidebar -->
    <div class="sidebar-container" :class="{ 'active': sidebarOpen }"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
        @include('components.sidebar-toggle')
    </div>

    <!-- Main Content -->
    <main class="main-content-full" :class="{ 'sidebar-open': sidebarOpen }">
        <!-- ... konten dashboard ... -->
    </main>

    <script>
    // Pastikan Alpine.js sudah di-load
    document.addEventListener('alpine:init', () => {
        // Event listener untuk close sidebar dengan ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                Alpine.store('sidebarOpen', false);
            }
        });
    });

    // Close sidebar saat resize ke desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            Alpine.store('sidebarOpen', true);
        }
    });
    </script>
    <script>
    // Force reinitialize Alpine
    document.addEventListener('DOMContentLoaded', function() {
        // Manual toggle function
        window.toggleSidebar = function() {
            const sidebar = document.querySelector('.sidebar-container');
            const overlay = document.querySelector('.sidebar-overlay');
            const btn = document.querySelector('.sidebar-toggle-btn');

            if (sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                btn.classList.remove('sidebar-open');
            } else {
                sidebar.classList.add('active');
                overlay.classList.add('active');
                btn.classList.add('sidebar-open');
            }
        };

        // Attach event
        document.querySelector('.sidebar-toggle-btn').addEventListener('click', toggleSidebar);
        document.querySelector('.sidebar-overlay').addEventListener('click', toggleSidebar);

        // Close on link click
        document.querySelectorAll('.sidebar-container a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) toggleSidebar();
            });
        });
    });
    </script>
</body>