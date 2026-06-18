<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - Lab ICT</title>
    <link rel="icon" type="image/LogoICT.png" href="{{ asset('images/LogoICT.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @yield('styles')
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-blue-100 font-sans text-slate-800 antialiased">

    <!-- Sidebar Container (Warna disesuaikan dengan image_fca9a7.png) -->
    <aside class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-[#476f84] text-white transition-transform duration-300 md:translate-x-0 -translate-x-full" id="sidebar">

        <!-- Header Sidebar -->
        <div class="flex h-24 items-center gap-3 px-6 border-b border-white/10">
            <img src="{{ asset('images/LogoICT.png') }}" alt="Logo" class="h-12 w-12 rounded-full object-cover bg-white p-0.5" onerror="this.src='https://ui-avatars.com/api/?name=ICT&background=fff&color=0284c7'">
            <div class="leading-tight text-white">
                <h2 class="text-sm font-medium tracking-wide">Laboratorium</h2>
                <span class="text-base font-extrabold tracking-wide block">ICT Budi Luhur</span>
            </div>
        </div>

        <!-- Menu Navigasi -->
       <nav class="flex-1 space-y-1 px-4 py-6 overflow-y-auto">

    {{-- Dashboard --}}
    <a href="/spv/dashboard"
        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold tracking-wide transition {{ request()->is('spv/dashboard') ? 'bg-white/20 text-white shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
        <i class="fas fa-gauge-high text-base"></i>
        <span>Dashboard</span>
    </a>

    {{-- Approve Booking --}}
    <a href="/spv/booking"
        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold tracking-wide transition {{ request()->is('spv/booking') ? 'bg-white/20 text-white shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
        <i class="fas fa-clipboard-check text-base"></i>
        <span>Approve Booking</span>
    </a>

    {{-- Manajemen Jadwal --}}
    <a href="/spv/jadwal"
        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold tracking-wide transition {{ request()->is('spv/jadwal') || request('filter_date') ? 'bg-white/20 text-white shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
        <i class="fas fa-calendar-days text-base"></i>
        <span>Manajemen Jadwal</span>
    </a>

    {{-- Import Jadwal Asisten --}}
    <a href="/spv/asisten"
        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold tracking-wide transition {{ request()->is('spv/asisten') ? 'bg-white/20 text-white shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
        <i class="fas fa-file-import text-base"></i>
        <span>Import Jadwal Asisten</span>
    </a>

    {{-- Jadwal Asisten --}}
    <a href="/spv/jasis"
        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold tracking-wide transition {{ request()->is('spv/jasis') ? 'bg-white/20 text-white shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
        <i class="fas fa-user-clock text-base"></i>
        <span>Jadwal Asisten</span>
    </a>

    {{-- Data Lab --}}
    <a href="/spv/lab"
        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold tracking-wide transition {{ request()->is('spv/lab') ? 'bg-white/20 text-white shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
        <i class="fas fa-flask text-base"></i>
        <span>Data Lab</span>
    </a>

    {{-- TV Monitor --}}
    <a href="/spv/tv"
        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold tracking-wide transition {{ request()->is('spv/tv') ? 'bg-white/20 text-white shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
        <i class="fas fa-display text-base"></i>
        <span>TV Monitor</span>
    </a>

    {{-- Buat Akun --}}
    <a href="/spv/akun"
        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold tracking-wide transition {{ request()->is('spv/akun') ? 'bg-white/20 text-white shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
        <i class="fas fa-user-plus text-base"></i>
        <span>Buat Akun</span>
    </a>

</nav>


        </nav>

        <div class="p-4 border-t border-white/10 bg-[#3f6579]">
    {{--  LOGIC BREEZE: Ubah action menjadi route('logout') resmi --}}
    <form method="POST" action="{{ route('logout') }}" class="m-0">
        @csrf
        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-white/30 bg-white/5 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-white/15 focus:outline-none cursor-pointer">
            <i class="fas fa-sign-out-alt text-base"></i>
            <span>Log out</span>
        </button>
    </form>
</div>
</aside>

    <!-- Overlay Mobile Background -->
    <div class="fixed inset-0 z-40 bg-slate-900/20 backdrop-blur-sm transition-opacity duration-300 md:hidden hidden" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <!-- Main Content Area -->
    <div class="flex flex-col md:pl-64" id="main-content">

        <!-- Topbar Header -->
        <header class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-white/60 bg-white/60 px-6 backdrop-blur lg:px-8">
            <div>
                <button class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 md:hidden" onclick="toggleSidebar()">
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>

            <!-- User Profile Section -->
            <div class="relative" id="profileDropdownContainer">
                <button onclick="toggleProfileDropdown(event)" class="flex items-center gap-3 rounded-xl border border-transparent bg-transparent px-3 py-1.5 text-left transition hover:border-slate-200 hover:bg-white/80 focus:outline-none">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'A') }}&background=e0f2fe&color=0284c7&bold=true" alt="Avatar" class="h-10 w-10 rounded-full shadow-md shadow-blue-900/10">
                    <div class="hidden leading-tight sm:block">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-400">SPV Penjadwalan</div>
                        <div class="text-sm font-extrabold text-slate-700">
                            {{ Auth::user()->name ?? 'Administrator' }}
                        </div>
                    </div>
                    <i class="fas fa-chevron-down text-xs text-slate-400 ml-1 hidden sm:block"></i>
                </button>

                <!-- Profile Dropdown Menu -->
                <div id="profileDropdown" class="absolute right-0 top-full mt-2 hidden w-56 origin-top-right flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-2xl shadow-blue-950/10">
                    <div class="bg-slate-50 p-4 border-b border-slate-100">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Masuk sebagai:</div>
                        <div class="truncate text-sm font-bold text-slate-700">
                            {{ Auth::user()->email ?? 'admin@budiluhur.ac.id' }}
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-blue-700">
    <i class="fas fa-user-circle text-base text-slate-400"></i> Edit Profil
</a>

{{-- LOGIC BREEZE: Form Logout yang udah bener jalurnya --}}
<form method="POST" action="{{ route('logout') }}" class="m-0 border-t border-slate-100">
    @csrf
    <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-red-50 hover:text-red-600 text-left cursor-pointer">
        <i class="fas fa-sign-out-alt text-base text-slate-400 transition-colors group-hover:text-red-500"></i>
        <span>Keluar / Logout</span>
    </button>
</form>
            </div>
        </header>

        <!-- Content Inject Area -->
        <main class="mx-auto w-full max-w-7xl px-3 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
            @yield('content')
        </main>
    </div>

    <!-- Layout Scripts -->
    <script>
        function toggleProfileDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('hidden');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');

            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        window.addEventListener('click', function() {
            const dropdown = document.getElementById('profileDropdown');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            if (window.location.href.indexOf("aprove") > -1) {
                const sidebar = document.getElementById('sidebar');
                if (sidebar) {
                    sidebar.classList.add('-translate-x-full');
                }
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
