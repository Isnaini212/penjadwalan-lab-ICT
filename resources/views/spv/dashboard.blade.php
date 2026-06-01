@extends('layouts.spv')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <section>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Dashboard</h1>
            <p class="mt-1.5 text-sm font-medium text-slate-500">Memantau indikator kinerja utama Anda</p>
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem] 2xl:grid-cols-[minmax(0,1fr)_22rem]">
            
            {{-- BLOK KIRI: JADWAL UTAMA (PAGINATION + SEARCH WORKING) --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-md shadow-slate-900/5 h-fit">
                
                <form method="GET" action="" class="mb-4 flex flex-col gap-4 border-b border-slate-100 pb-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Jadwal Hari Ini</h2>
                        <p class="mt-1 text-sm text-slate-500">Cari dan filter manajemen jadwal:</p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                        <div class="relative flex-1 min-w-[200px] sm:flex-none">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari matkul, dosen, lab..." 
                                   class="h-10 w-full sm:w-60 rounded-lg border border-slate-200 pl-9 pr-3 text-sm font-semibold text-slate-700 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-xs text-slate-400"></i>
                        </div>

                        <input type="date" name="filter_date" value="{{ request('filter_date', now()->toDateString()) }}" onchange="this.form.submit()" 
                               class="h-10 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-700 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100 cursor-pointer">

                        <select name="per_page" onchange="this.form.submit()" 
                                class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm font-bold text-slate-700 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100 cursor-pointer">
                            @foreach([5, 10, 20, 50, 100] as $limit)
                                <option value="{{ $limit }}" {{ request('per_page') == $limit ? 'selected' : '' }}>{{ $limit }} Baris</option>
                            @endforeach
                        </select>
                    </div>
                </form>

                @if(request('search') || request('filter_date') || request('per_page'))
                    <a href="{{ request()->url() }}" class="text-xs font-bold text-red-500 underline">[Reset Semua Filter]</a>
                @endif

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-[760px] w-full text-left text-xs">
                        <thead class="bg-slate-50 text-xs font-extrabold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-3 py-3 sticky top-0 z-10 bg-slate-50">Tanggal</th>
                                <th class="px-3 py-3 sticky top-0 z-10 bg-slate-50">Lab</th>
                                <th class="px-3 py-3 sticky top-0 z-10 bg-slate-50">Jam (Mulai - Selesai)</th>
                                <th class="px-3 py-3 sticky top-0 z-10 bg-slate-50">Mata Kuliah</th>
                                <th class="px-3 py-3 sticky top-0 z-10 bg-slate-50">Dosen</th>
                                <th class="px-3 py-3 sticky top-0 z-10 bg-slate-50">Asisten</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($schedules as $s)
                                @php
                                    $namaLab = $s->lab->nama_lab ?? $s->id_lab;
                                    $namaAsisten = $s->assistantSchedule->nama_asisten ?? $s->assistantSchedule->nama ?? '-';
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="px-3 py-3">
                                        <span class="block text-xs font-extrabold uppercase text-sky-700">{{ $s->hari }}</span>
                                        <span class="text-slate-600">{{ \Carbon\Carbon::parse($s->tanggal)->format('d M Y') }}</span>
                                    </td>
                                    <td class="px-3 py-3 font-bold text-slate-700">{{ $namaLab }}</td>
                                    <td class="px-3 py-3 font-mono text-slate-700">
                                        {{ date('H:i', strtotime($s->jam_mulai)) }} - {{ date('H:i', strtotime($s->jam_selesai)) }}
                                    </td>
                                    <td class="px-3 py-3 font-semibold text-slate-700">{{ $s->matkul }}</td>
                                    <td class="px-3 py-3 text-slate-600">{{ $s->dosen }}</td>
                                    <td class="px-3 py-3 text-slate-600">{{ $namaAsisten }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-8 text-center text-sm font-bold text-slate-400">
                                        🚫 Tidak ada kelas praktikum yang cocok dengan kriteria pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($schedules, 'links'))
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        {{ $schedules->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

            {{-- BLOK KANAN: COUNTER AKURAT AMBIL DARI DAY SCHEDULES (ANTI-MELAR) --}}
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1 h-fit">
                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-md shadow-slate-900/5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Total Matkul Hari Ini</p>
                            <p class="mt-2 text-3xl font-black text-slate-900">{{ $daySchedules->count() }}</p>
                        </div>
                        <i class="fa-solid fa-building text-2xl text-sky-700/80"></i>
                    </div>
                </article>
                
                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-md shadow-slate-900/5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Jumlah Laboratorium</p>
                            <p class="mt-2 text-3xl font-black text-slate-900">{{ isset($labs) ? $labs->count() : 0 }}</p>
                        </div>
                        <i class="fa-solid fa-desktop text-2xl text-sky-700/80"></i>
                    </div>
                </article>
                
                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-md shadow-slate-900/5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Asisten Bertugas</p>
                            <p class="mt-2 text-3xl font-black text-slate-900">
                                {{ $daySchedules->whereNotNull('id_asisten')->unique('id_asisten')->count() }}
                            </p>
                        </div>
                        <i class="fa-solid fa-users text-2xl text-sky-700/80"></i>
                    </div>
                </article>
            </div>
        </section>

        {{-- SEKSI STATUS PETUGAS JAGA (SEKARANG FIX TAMPIL FULL NON-PAGINATION) --}}
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-md shadow-slate-900/5">
            <div class="mb-5 flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                    <i class="fa-solid fa-user-group text-xl"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Status Petugas Asisten</h2>
                    <p class="text-sm text-slate-500">Daftar seluruh asisten yang menjaga laboratorium hari ini</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[760px] w-full text-left text-xs">
                    <thead class="bg-slate-50 text-xs font-extrabold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-4">Nama Asisten</th>
                            <th class="px-4 py-4">Menjaga Lab</th>
                            <th class="px-4 py-4">Waktu Tugas</th>
                            <th class="px-4 py-4">Mata Kuliah Kelolaan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $assignedSchedules = $daySchedules->filter(function($item) {
                                return !empty($item->id_asisten);
                            })->sortBy('jam_mulai');
                        @endphp
                        
                        @forelse($assignedSchedules as $s)
                            @php
                                $namaAsisten = $s->assistantSchedule->nama_asisten ?? $s->assistantSchedule->nama ?? 'Asisten';
                                $inisial = strtoupper(substr(trim($namaAsisten), 0, 1));
                                $namaLab = $s->lab->nama_lab ?? $s->id_lab;
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-sky-700 text-xs font-extrabold text-white">
                                            {{ $inisial }}
                                        </span>
                                        <span class="font-semibold text-slate-700">{{ $namaAsisten }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="rounded-md bg-sky-100 px-3 py-1 text-xs font-extrabold text-sky-700">
                                        {{ $namaLab }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 font-mono text-slate-700">
                                    {{ date('H:i', strtotime($s->jam_mulai)) }} - {{ date('H:i', strtotime($s->jam_selesai)) }}
                                </td>
                                <td class="px-4 py-4 italic text-slate-600">{{ $s->matkul }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-sm font-bold text-slate-400">
                                    🍃 Tidak ada asisten yang terjadwal bertugas hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection