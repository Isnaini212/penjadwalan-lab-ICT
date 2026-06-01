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

                <form method="POST" action="/login" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Email
                        </label>
                        <input
                            type="email"
                            name="email"
                            class="w-full px-4 py-3 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Password
                        </label>
                        <input
                            type="password"
                            name="password"
                            class="w-full px-4 py-3 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                        >
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