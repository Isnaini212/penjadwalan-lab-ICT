<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Profil</title>
    <link rel="icon" type="image/LogoICT.png" href="{{ asset('images/LogoICT.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-blue-100 font-sans text-slate-800 antialiased">
    <nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-white/70 bg-white/85 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8">
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-700 shadow-sm">
                    <i class="fas fa-user-cog text-lg"></i>
                </a>
                <div>
                    <div class="text-base font-black tracking-tight text-blue-950 sm:text-lg">Pengaturan Profil</div>
                    <div class="text-xs font-semibold text-slate-400">Kelola informasi akun dan keamanan login</div>
                </div>
            </div>

            <div class="hidden items-center gap-3 sm:flex">
                @auth
                    @if(auth()->user()->role === 'spv')
                        <a href="{{ route('spv.dashboard') }}" class="inline-flex items-center rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-800/15 transition hover:bg-slate-900">
                            Dashboard SPV
                        </a>
                    @elseif(auth()->user()->role === 'ormawa')
                        <a href="{{ route('ormawa.booking.index') }}" class="inline-flex items-center rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-800/15 transition hover:bg-slate-900">
                            Portal Ormawa
                        </a>
                    @elseif(auth()->user()->role === 'dosen')
                        <a href="{{ route('dosen.booking.index') }}" class="inline-flex items-center rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-800/15 transition hover:bg-slate-900">
                            Portal Dosen
                        </a>
                    @elseif(auth()->user()->role === 'asisten')
                        <a href="{{ route('asisten.jadwal') }}" class="inline-flex items-center rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-800/15 transition hover:bg-slate-900">
                            Jadwal Asisten
                        </a>
                    @else
                        <a href="{{ url('/') }}" class="inline-flex items-center rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-800/15 transition hover:bg-slate-900">
                            Beranda
                        </a>
                    @endif

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-left shadow-sm transition hover:border-blue-200 hover:bg-blue-50/40 focus:outline-none">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-xs font-black text-blue-700">
                                    {{ collect(explode(' ', Auth::user()->name))->map(fn($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('') }}
                                </span>
                                <span class="hidden leading-tight md:block">
                                    <span class="block text-xs font-bold uppercase tracking-wide text-slate-400">{{ strtoupper(Auth::user()->role ?? 'user') }}</span>
                                    <span class="block max-w-[150px] truncate text-sm font-extrabold text-slate-700">{{ Auth::user()->name }}</span>
                                </span>
                                <i class="fas fa-chevron-down text-xs text-slate-400"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                <i class="fas fa-user-cog mr-1"></i> {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600">
                                    <i class="fas fa-sign-out-alt mr-1"></i> {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-800">
                        Login Sistem
                    </a>
                @endauth
            </div>

            <button @click="open = ! open" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 sm:hidden">
                <i class="fas fa-bars" x-show="!open"></i>
                <i class="fas fa-times" x-show="open" x-cloak></i>
            </button>
        </div>

        <div x-show="open" x-transition class="border-t border-slate-100 bg-white px-5 py-4 sm:hidden" x-cloak>
            @auth
                <div class="mb-4 rounded-2xl bg-slate-50 p-4">
                    <div class="text-sm font-black text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="mt-1 text-xs font-semibold text-slate-500">{{ Auth::user()->email }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-xl bg-red-50 px-4 py-2.5 text-sm font-black text-red-600">
                        Log Out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block rounded-xl bg-blue-700 px-4 py-2.5 text-center text-sm font-bold text-white">Login Sistem</a>
            @endauth
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-5 py-10 lg:px-8">
        <section class="mb-8 overflow-hidden rounded-3xl border border-white/80 bg-white/85 shadow-2xl shadow-blue-950/10 backdrop-blur">
            <div class="grid gap-6 p-6 lg:grid-cols-[1.1fr_0.9fr] lg:p-8">
                <div>
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-black uppercase tracking-wide text-blue-700">
                        Akun {{ strtoupper($user->role ?? 'user') }}
                    </span>
                    <h1 class="mt-4 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Profil Pengguna</h1>
                    <p class="mt-3 max-w-2xl text-sm font-medium leading-7 text-slate-500">
                        Perbarui identitas akun, atur password, dan kelola akses akun Anda melalui halaman ini.
                    </p>
                </div>

                <div class="rounded-3xl border border-slate-100 bg-gradient-to-br from-blue-50 to-white p-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-700 text-xl font-black text-white shadow-lg shadow-blue-700/20">
                            {{ collect(explode(' ', $user->name))->map(fn($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('') }}
                        </div>
                        <div class="min-w-0">
                            <div class="truncate text-lg font-black text-slate-900">{{ $user->name }}</div>
                            <div class="truncate text-sm font-semibold text-slate-500">{{ $user->email }}</div>
                        </div>
                    </div>
                    <div class="mt-5 grid grid-cols-2 gap-3 text-xs font-bold text-slate-500">
                        <div class="rounded-2xl bg-white p-4 shadow-sm">
                            <div class="uppercase tracking-wide text-slate-400">Role</div>
                            <div class="mt-1 text-sm font-black text-blue-700">{{ strtoupper($user->role ?? '-') }}</div>
                        </div>
                        <div class="rounded-2xl bg-white p-4 shadow-sm">
                            <div class="uppercase tracking-wide text-slate-400">Status</div>
                            <div class="mt-1 text-sm font-black text-emerald-600">Aktif</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-[1fr_1fr]">
            <section class="rounded-3xl border border-white/80 bg-white p-6 shadow-xl shadow-blue-950/5 lg:p-8">
                <div class="mb-6 flex items-start gap-4">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-700">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-950">{{ __('Profile Information') }}</h2>
                        <p class="mt-1 text-sm font-medium leading-6 text-slate-500">{{ __("Update your account's profile information and email address.") }}</p>
                    </div>
                </div>

                <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                    @csrf
                </form>

                @php
                    $profileLocked = in_array($user->role, ['asisten', 'dosen', 'ormawa'], true);
                @endphp

                @if($profileLocked)
                    <div class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-700">
                        Nama dan email dikunci untuk role {{ strtoupper($user->role) }}. Hubungi SPV jika data akun perlu diperbarui.
                    </div>
                @endif

                <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('patch')

                    <div>
                        <x-input-label for="name" :value="__('Name')" class="text-xs font-black uppercase tracking-wide text-slate-500" />
                        <x-text-input id="name" name="name" type="text" class="mt-2 block h-12 w-full rounded-2xl border-slate-200 px-4 font-bold text-slate-700 focus:border-blue-500 focus:ring-blue-100 {{ $profileLocked ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-slate-50' }}" :value="old('name', $user->name)" required autofocus autocomplete="name" :readonly="$profileLocked" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email')" class="text-xs font-black uppercase tracking-wide text-slate-500" />
                        <x-text-input id="email" name="email" type="email" class="mt-2 block h-12 w-full rounded-2xl border-slate-200 px-4 font-bold text-slate-700 focus:border-blue-500 focus:ring-blue-100 {{ $profileLocked ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-slate-50' }}" :value="old('email', $user->email)" required autocomplete="username" :readonly="$profileLocked" />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-700">
                                {{ __('Your email address is unverified.') }}
                                <button form="send-verification" class="ml-1 underline decoration-2 underline-offset-4">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>

                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-2 font-bold text-emerald-600">
                                        {{ __('A new verification link has been sent to your email address.') }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        @unless($profileLocked)
                            <button type="submit" class="inline-flex h-11 items-center rounded-xl bg-blue-700 px-6 text-sm font-black text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-800">
                                {{ __('Save') }}
                            </button>
                        @endunless

                        @if (session('status') === 'profile-updated')
                            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm font-bold text-emerald-600">
                                {{ __('Saved.') }}
                            </p>
                        @endif

                        @if (session('status') === 'profile-locked')
                            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-bold text-amber-600">
                                Profil dikunci untuk role ini.
                            </p>
                        @endif
                    </div>
                </form>
            </section>

            <section class="rounded-3xl border border-white/80 bg-white p-6 shadow-xl shadow-blue-950/5 lg:p-8">
                <div class="mb-6 flex items-start gap-4">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-700">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-950">{{ __('Update Password') }}</h2>
                        <p class="mt-1 text-sm font-medium leading-6 text-slate-500">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
                    </div>
                </div>

                <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('put')

                    <div>
                        <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-xs font-black uppercase tracking-wide text-slate-500" />
                        <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-2 block h-12 w-full rounded-2xl border-slate-200 bg-slate-50 px-4 font-bold text-slate-700 focus:border-blue-500 focus:ring-blue-100" autocomplete="current-password" />
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="update_password_password" :value="__('New Password')" class="text-xs font-black uppercase tracking-wide text-slate-500" />
                        <x-text-input id="update_password_password" name="password" type="password" class="mt-2 block h-12 w-full rounded-2xl border-slate-200 bg-slate-50 px-4 font-bold text-slate-700 focus:border-blue-500 focus:ring-blue-100" autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-xs font-black uppercase tracking-wide text-slate-500" />
                        <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-2 block h-12 w-full rounded-2xl border-slate-200 bg-slate-50 px-4 font-bold text-slate-700 focus:border-blue-500 focus:ring-blue-100" autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <button type="submit" class="inline-flex h-11 items-center rounded-xl bg-indigo-700 px-6 text-sm font-black text-white shadow-lg shadow-indigo-700/20 transition hover:bg-indigo-800">
                            {{ __('Save') }}
                        </button>

                        @if (session('status') === 'password-updated')
                            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm font-bold text-emerald-600">
                                {{ __('Saved.') }}
                            </p>
                        @endif
                    </div>
                </form>
            </section>
        </div>

        <section class="mt-6 rounded-3xl border border-red-100 bg-white p-6 shadow-xl shadow-red-950/5 lg:p-8">
            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                <div class="flex items-start gap-4">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-950">{{ __('Delete Account') }}</h2>
                        <p class="mt-1 max-w-3xl text-sm font-medium leading-6 text-slate-500">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
                        </p>
                    </div>
                </div>

                <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="inline-flex h-11 items-center justify-center rounded-xl bg-red-600 px-6 text-sm font-black text-white shadow-lg shadow-red-600/20 transition hover:bg-red-700">
                    {{ __('Delete Account') }}
                </button>
            </div>

            <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                    @csrf
                    @method('delete')

                    <h2 class="text-lg font-black text-slate-950">{{ __('Are you sure you want to delete your account?') }}</h2>
                    <p class="mt-2 text-sm font-medium leading-6 text-slate-500">{{ __('Please enter your password to confirm you would like to permanently delete your account.') }}</p>

                    <div class="mt-6">
                        <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block h-12 w-full rounded-2xl border-slate-200 bg-slate-50 px-4" placeholder="{{ __('Password') }}" />
                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-secondary-button x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                        <x-danger-button>{{ __('Delete Account') }}</x-danger-button>
                    </div>
                </form>
            </x-modal>
        </section>
    </main>
</body>
</html>
