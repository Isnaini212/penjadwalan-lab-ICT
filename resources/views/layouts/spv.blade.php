<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Lab ICT</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>[x-cloak] { display: none !important; }</style>

    @yield('styles')
</head>
<body class="bg-slate-100 font-sans text-slate-800">
    <div x-data="{ sidebarOpen: false, profileOpen: false }" class="min-h-screen lg:flex">
        <div
            x-cloak
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"
            @click="sidebarOpen = false"
            aria-hidden="true"
        ></div>

        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-blue-900 text-white shadow-2xl transition-transform duration-300 lg:translate-x-0"
            :class="{ 'translate-x-0': sidebarOpen }"
        >
            <div class="flex items-center gap-3 px-5 py-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/20">
                    <i class="fa-solid fa-flask text-base text-white"></i>
                </div>
                <div>
                    <h1 class="text-sm font-extrabold leading-tight">Laboratorium</h1>
                    <p class="text-xs font-bold leading-tight text-blue-100">ICT Budi Luhur</p>
                </div>
            </div>

            <nav class="mt-2 flex-1 space-y-1.5 px-4 text-xs font-bold">
                <a href="{{ route('spv.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-3 transition {{ request()->routeIs('spv.dashboard') ? 'bg-white/18 text-white shadow-lg shadow-blue-950/20' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-house w-5"></i>
                    Dashboard
                </a>
                <a href="{{ route('spv.jadwal') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-3 transition {{ request()->routeIs('spv.jadwal') ? 'bg-white/18 text-white shadow-lg shadow-blue-950/20' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-calendar-days w-5"></i>
                    Manajemen Jadwal
                </a>
                <a href="#" class="flex items-center gap-4 rounded-xl px-3.5 py-3 text-blue-100 transition hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-business-time"></i>
                    Jadwal Asisten
                </a>
                <a href="#" class="flex items-center gap-3 rounded-xl px-3.5 py-3 text-blue-100 transition hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-desktop w-5"></i>
                    Data Lab
                </a>
            </nav>

            <div class="border-t border-white/10 p-4">
                <button type="button" class="flex w-full items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-xs font-extrabold text-white transition hover:bg-white/15">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Log out
                </button>
            </div>
        </aside>

        <div class="min-h-screen flex-1 lg:pl-64">
            <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-200 bg-white/90 px-5 backdrop-blur lg:px-7">
                <button
                    type="button"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 lg:hidden"
                    @click="sidebarOpen = true"
                    aria-label="Buka menu"
                >
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div class="hidden lg:block"></div>

                <div class="relative" @click.outside="profileOpen = false">
                    <button
                        type="button"
                        class="flex items-center gap-3 rounded-2xl px-3 py-2 text-left transition hover:bg-slate-100"
                        @click="profileOpen = !profileOpen"
                    >
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-sky-100 text-sm font-extrabold text-sky-700">
                            <i class="fa-solid fa-user text-xl "></i>
                        </div>
                        <div class="hidden leading-tight sm:block">
                            <div class="text-sm font-extrabold text-slate-800">SPV Penjadwalan</div>
                            <div class="text-xs font-semibold text-slate-500">{{ Auth::user()?->name ?? 'Administrator' }}</div>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                    </button>

                    <div
                        x-cloak
                        x-show="profileOpen"
                        x-transition
                        class="absolute right-0 mt-3 w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/10"
                    >
                        <div class="border-b border-slate-100 bg-slate-50 px-4 py-3">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Masuk sebagai</p>
                            <p class="mt-1 truncate text-sm font-extrabold text-slate-800">{{ Auth::user()?->email ?? 'admin@student.budiluhur.ac.id' }}</p>
                        </div>
                        <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                            <i class="fa-solid fa-user-circle w-5 text-slate-400"></i>
                            Edit Profil
                        </a>
                        <button type="button" class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-semibold text-red-600 hover:bg-red-50">
                            <i class="fa-solid fa-right-from-bracket w-5"></i>
                            Log out
                        </button>
                    </div>
                </div>
            </header>

            <main class="p-5 lg:p-7">
                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
