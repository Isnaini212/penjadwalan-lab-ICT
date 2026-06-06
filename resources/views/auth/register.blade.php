@extends('layouts.auth')
@section('title', 'Register SPV')
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

                    <h1 class="text-3xl font-bold text-blue-900">
                        Buat Akun SPV
                    </h1>
                </div>

                <form method="POST" action="{{ route('register') }}">
                @csrf
                    
                    {{-- INPUT TERSEMBUNYI: Memaksa Role menjadi SPV --}}
                    <input type="hidden" name="role" value="spv">

                    {{-- INPUT NAMA LENGKAP --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Nama Lengkap
                        </label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required autofocus
                            class="w-full px-4 py-3 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                        >
                        @error('name')
                            <span style="color: #ef4444; font-size: 12px; font-weight: bold; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- INPUT EMAIL --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Email
                        </label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full px-4 py-3 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                        >
                        @error('email')
                            <span style="color: #ef4444; font-size: 12px; font-weight: bold; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- INPUT PASSWORD --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Password
                        </label>
                        <input
                            type="password"
                            name="password"
                            required
                            class="w-full px-4 py-3 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                        >
                        @error('password')
                            <span style="color: #ef4444; font-size: 12px; font-weight: bold; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- INPUT KONFIRMASI PASSWORD --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Konfirmasi Password
                        </label>
                        <input
                            type="password"
                            name="password_confirmation"
                            required
                            class="w-full px-4 py-3 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full py-3 rounded-lg bg-indigo-900 text-white font-medium hover:bg-indigo-800 transition"
                    >
                        Register SPV
                    </button>
                    
                    <div class="mt-4 text-center">
                        <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline font-semibold">Sudah punya akun? Login di sini</a>
                    </div>
                </form>
            </div>

        </div>

    </div>
@endsection