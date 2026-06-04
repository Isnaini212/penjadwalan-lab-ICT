@extends('layouts.auth')
@section('title', 'Login SPV')
@section('content')
    <div class="min-h-screen bg-gradient-to-b from-white to-blue-400 flex items-center justify-center px-4">

        <div class="w-full max-w-5xl bg-white rounded-lg shadow-xl overflow-hidden grid md:grid-cols-2">

            <div class="hidden md:block">
                <img
                    src="{{ asset('images/.png') }}"
                    alt="Laboratorium ICT"
                    class="w-full h-full object-cover"
                >
            </div>

            <div class="px-10 py-12 flex flex-col justify-center">
                <div class="text-center mb-8">
                    <img
                        src="{{ asset('images/LogoICT.png') }}"
                        alt="Logo ICT"
                        class="w-20 h-20 object-contain mx-auto mb-6"
                    >

                    <h1 class="text-4xl font-bold text-blue-900">
                        Selamat Datang
                    </h1>
                </div>

                {{-- 🌟 LOGIC INDIKATOR ALERT: Muncul di atas form jika ada kesalahan login --}}
                @if ($errors->any())
                    <div style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; font-weight: bold; text-align: center;">
                        ⚠️ Login Gagal! Email belum terdaftar atau Password Anda salah.
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}">
                @csrf
                    {{-- INPUT EMAIL --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Email
                        </label>
                        {{-- 🌟 LOGIC INDIKATOR INPUT: Border otomatis merah & bg merah tipis jika salah --}}
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 border rounded-md focus:outline-none focus:ring-2 {{ $errors->has('email') ? 'border-red-500 bg-red-50/30 focus:ring-red-400' : 'border-slate-300 focus:ring-blue-400' }}"
                        >
                        @error('email')
                            <span style="color: #ef4444; font-size: 12px; font-weight: bold; margin-top: 4px; display: block;">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- INPUT PASSWORD --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Password
                        </label>
                        {{-- 🌟 LOGIC INDIKATOR INPUT: Ikut memerah jika login gagal --}}
                        <input
                            type="password"
                            name="password"
                            class="w-full px-4 py-3 border rounded-md focus:outline-none focus:ring-2 {{ $errors->has('email') || $errors->has('password') ? 'border-red-500 bg-red-50/30 focus:ring-red-400' : 'border-slate-300 focus:ring-blue-400' }}"
                        >
                        @error('password')
                            <span style="color: #ef4444; font-size: 12px; font-weight: bold; margin-top: 4px; display: block;">
                                {{ $message }}
                            </span>
                        @enderror
                        
                        @if (Route::has('password.request'))
                            <div class="mt-2">
                                <a href="{{ route('password.request') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                                    Lupa Sandi?
                                </a>
                            </div>
                        @endif
                    </div>

                    <button
                        type="submit"
                        class="w-full py-3 rounded-lg bg-indigo-900 text-white font-medium hover:bg-indigo-800 transition"
                    >
                        Login
                    </button>
                </form>
            </div>

        </div>

    </div>
@endsection