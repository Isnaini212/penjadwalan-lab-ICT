<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - Lab ICT</title>
    
    {{-- Hubungkan ke Tailwind via Vite bawaan proyek temanmu --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Font & Icons FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @yield('styles')
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-blue-100 font-sans text-slate-800 antialiased">

    {{-- ================= SIDEBAR CONTAINER ================= --}}
    <aside class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-white/60 bg-white/80 backdrop-blur transition-transform duration-300 md:translate-x-0 -translate-x-full" id="sidebar">
        
        {{-- Sidebar Header --}}
        <div class="flex h-20 items-center gap-3 border-b border-slate-100 px-6">
            <img src="{{ asset('img/logo-ubl.png') }}" alt="Logo ICT" 
                 class="h-10 w-10 rounded-full object-cover shadow-md shadow-blue-900/10"
                 onerror="this.src='https://ui-avatars.com/api/?name=ICT&background=0284c7&color=fff'">
            <div class="leading-tight">
                <h2 class="text-sm font-extrabold tracking-tight text-blue-900 uppercase">Laboratorium</h2>
                <span class="text-xs font-semibold text-slate-500">ICT Budi Luhur</span>
            </div>
        </div>
        
        {{-- Sidebar Menu Links --}}
        <nav class="flex-1 space-y-1 px-4 py-6">

    <a href="/spv/dashboard" 
       class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold tracking-wide transition group {{ request()->routeIs('spv.dashboard') ? 'bg-blue-700 text-white shadow-lg shadow-blue-700/20' : 'text-slate-600 hover:bg-blue-50/80 hover:text-blue-700' }}">
        <i class="fas fa-calendar-alt text-base transition group-hover:scale-110"></i> 
        <span>Dashboard</span>
    </a>
    {{-- 1. Manajemen Jadwal (Ikon: Kalender) --}}
    <a href="/spv/jadwal" 
       class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold tracking-wide transition group {{ request()->routeIs('spv.jadwal') ? 'bg-blue-700 text-white shadow-lg shadow-blue-700/20' : 'text-slate-600 hover:bg-blue-50/80 hover:text-blue-700' }}">
        <i class="fas fa-calendar-alt text-base transition group-hover:scale-110"></i> 
        <span>Manajemen Jadwal</span>
    </a>

    {{-- 2. Manajemen Lab (Ikon: Monitor Desktop Lab ICT) --}}
    <a href="/spv/lab" 
       class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold tracking-wide transition group {{ request()->path() == 'spv/lab' ? 'bg-blue-700 text-white shadow-lg shadow-blue-700/20' : 'text-slate-600 hover:bg-blue-50/80 hover:text-blue-700' }}">
        <i class="fas fa-desktop text-base transition group-hover:scale-110"></i> 
        <span>Manajemen Lab</span>
    </a>

    {{-- 3. Import Asisten (Ikon: File Upload / Import Excel) --}}
    <a href="/spv/asisten" 
       class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold tracking-wide transition group {{ request()->path() == 'spv/asisten' ? 'bg-blue-700 text-white shadow-lg shadow-blue-700/20' : 'text-slate-600 hover:bg-blue-50/80 hover:text-blue-700' }}">
        <i class="fas fa-file-import text-base transition group-hover:scale-110"></i> 
        <span>Import Asisten</span>
    </a>

    {{-- 4. Manajemen Asisten / Jasis Blueprint Matrix (Ikon: Group User / Tim Asisten) --}}
    <a href="/spv/jasis" 
       class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold tracking-wide transition group {{ request()->path() == 'spv/jasis' ? 'bg-blue-700 text-white shadow-lg shadow-blue-700/20' : 'text-slate-600 hover:bg-blue-50/80 hover:text-blue-700' }}">
        <i class="fas fa-users text-base transition group-hover:scale-110"></i> 
        <span>Manajemen Asisten</span>
    </a>
</nav>
        
        {{-- Sidebar Footer (Logout Button) --}}
        <div class="border-t border-slate-100 p-4">
            {{-- FIX: Ditambahkan tag pembuka form logout yang tadinya bocor/hilang --}}
            <form method="POST" action="/logout" class="margin-0">
                @csrf
                <button type="button"
    onclick="window.location.href='/'"
    class="flex w-full items-center gap-3 rounded-xl bg-red-50 px-4 py-3 text-sm font-bold text-red-700 transition hover:bg-red-100">
    <i class="fas fa-home text-base"></i>
    <span>Home</span>
</button>
            </form>
        </div>
    </aside>

    {{-- Overelay background untuk mode HP pas sidebar kebuka --}}
    <div class="fixed inset-0 z-40 bg-slate-900/20 backdrop-blur-xs transition-opacity duration-300 md:hidden hidden" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    {{-- ================= KONTEN AREA UTAMA ================= --}}
    <div class="flex flex-col md:pl-64" id="main-content">
        
        {{-- TOPBAR STICKY --}}
        <header class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-white/60 bg-white/60 px-6 backdrop-blur lg:px-8">
            
            {{-- Tombol Burger Menu (Khusus Layar HP) --}}
            <div>
                <button class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 md:hidden" onclick="toggleSidebar()">
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>
            
            {{-- DROPDOWN PROFIL (USER SECTION) --}}
            <div class="relative" id="profileDropdownContainer">
                
                {{-- Tombol Pemicu Dropdown --}}
                <button onclick="toggleProfileDropdown(event)" class="flex items-center gap-3 rounded-xl border border-transparent bg-transparent px-3 py-1.5 text-left transition hover:border-slate-200 hover:bg-white/80 focus:outline-none">
                    
                    {{-- Avatar Otomatis UI-Avatars --}}
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'A') }}&background=e0f2fe&color=0284c7&bold=true" 
                         alt="Avatar" 
                         class="h-10 w-10 rounded-full shadow-md shadow-blue-900/10">
                    
                    <div class="hidden leading-tight sm:block">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-400">SPV Penjadwalan</div>
                        <div class="text-sm font-extrabold text-slate-700">
                            {{ Auth::user()->name ?? 'Administrator' }} 
                        </div>
                    </div>
                    <i class="fas fa-chevron-down text-xs text-slate-400 ml-1 hidden sm:block"></i>
                </button>

                {{-- Kotak Dropdown Item --}}
                <div id="profileDropdown" class="absolute right-0 top-full mt-2 hidden w-56 origin-top-right flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-2xl shadow-blue-950/10">
                    
                    <div class="bg-slate-50 p-4 border-b border-slate-100">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Masuk sebagai:</div>
                        <div class="truncate text-sm font-bold text-slate-700">
                            {{ Auth::user()->email ?? 'admin@budiluhur.ac.id' }}
                        </div>
                    </div>

                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-blue-700">
                        <i class="fas fa-user-circle text-base text-slate-400"></i> Edit Profil
                    </a>
                    
                    <form method="POST" action="/logout" class="margin-0 border-t border-slate-100">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                            <i class="fas fa-sign-out-alt text-base text-red-400"></i> Log out
                        </button>
                    </form>
                </div>
            </div>
            
        </header>

        {{-- AREA UNTUK INJECT CONTENT ANAK (JADWAL / LAB) --}}
        <main class="mx-auto w-full max-w-7xl px-6 py-10 lg:px-8">
            @yield('content')
        </main>
    </div>

    {{-- ================= CORE JAVASCRIPT SYSTEM ================= --}}
    <script>
        // Fungsi Buka-Tutup Dropdown Profil Topbar
        function toggleProfileDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('hidden');
            dropdown.classList.toggle('flex');
        }

        // Fungsi Buka-Tutup Sidebar (Mode Responsif HP)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        // Auto-close dropdown ketika user mengklik area luar layar
        window.addEventListener('click', function() {
            const dropdown = document.getElementById('profileDropdown');
            if (!dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
                dropdown.classList.remove('flex');
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>