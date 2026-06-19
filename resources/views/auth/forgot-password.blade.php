@extends('layouts.auth')
@section('title', 'Lupa Sandi')
@section('content')
    <div class="min-h-screen bg-gradient-to-b from-white to-blue-400 flex items-center justify-center px-4">

        <div class="w-full max-w-5xl bg-white rounded-lg shadow-xl overflow-hidden grid md:grid-cols-2">

            {{-- Sisi Kiri: Gambar --}}
            <div class="hidden md:block">
                <img
                    src="{{ asset('images/login/login.jpg') }}"
                    alt="Laboratorium ICT"
                    class="w-full h-full object-cover"
                >
            </div>

            {{-- Sisi Kanan: Form --}}
            <div class="px-10 py-12 flex flex-col justify-center">

                {{-- Logo & Judul --}}
                <div class="text-center mb-8">
                    <img
                        src="{{ asset('images/LogoICT.png') }}"
                        alt="Logo ICT"
                        class="w-20 h-20 object-contain mx-auto mb-4"
                    >
                    <h1 class="text-3xl font-bold text-blue-900">Lupa Sandi?</h1>
                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                        Masukkan email Anda dan kami akan mengirimkan<br>link untuk mereset password.
                    </p>
                </div>

                {{-- Status Sukses --}}
                @if (session('status'))
                    <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; font-weight: bold; text-align: center;">
                        ✅ {{ session('status') }}
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    {{-- Input Email --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="contoh@email.com"
                            class="w-full px-4 py-3 border rounded-md focus:outline-none focus:ring-2 {{ $errors->has('email') ? 'border-red-500 bg-red-50/30 focus:ring-red-400' : 'border-slate-300 focus:ring-blue-400' }}"
                        >
                        @error('email')
                            <span style="color: #ef4444; font-size: 12px; font-weight: bold; margin-top: 4px; display: block;">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Tombol Kirim --}}
                    <button
                        type="submit"
                        class="w-full py-3 rounded-lg bg-indigo-900 text-white font-medium hover:bg-indigo-800 transition"
                    >
                        Kirim Link Reset Sandi
                    </button>

                    {{-- Kembali ke Login --}}
                    <div class="text-center mt-5">
                        <a href="{{ route('login') }}" class="text-sm text-indigo-600 font-semibold hover:underline">
                            ← Kembali ke Login
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
