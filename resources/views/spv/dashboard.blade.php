@extends('layouts.spv')

@section('title', 'Dashboard')
@vite(['resources/css/app.css', 'resources/js/app.js'])
@section('content')

    <div class="space-y-5 w-full min-w-0">
        <section>
            <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900">Dashboard</h1>
            <p class="mt-1 text-sm font-medium text-slate-500">Memantau indikator kinerja utama Anda</p>
        </section>

        <section class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_20rem] 2xl:grid-cols-[minmax(0,1fr)_22rem]">
            
           
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-md shadow-slate-900/5 h-fit min-w-0 overflow-hidden">
                
                <form method="GET" action="" class="mb-4 flex flex-col gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Jadwal Hari Ini</h2>
                        <p class="mt-0.5 text-sm text-slate-500">Cari dan filter manajemen jadwal:</p>
                    </div>
                    
                    <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
                        <div class="relative w-full sm:w-auto sm:flex-1 sm:min-w-[180px]">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari matkul, dosen, lab..." 
                                   class="h-10 w-full rounded-lg border border-slate-200 pl-9 pr-3 text-sm font-semibold text-slate-700 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-xs text-slate-400"></i>
                        </div>

                        <div class="flex gap-2 w-full sm:w-auto">
                            <input type="date" name="filter_date" value="{{ request('filter_date', now()->toDateString()) }}" onchange="this.form.submit()" 
                                   class="h-10 flex-1 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-700 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100 cursor-pointer">

                            <select name="per_page" onchange="this.form.submit()" 
                                    class="h-10 w-24 sm:w-auto rounded-lg border border-slate-200 bg-white px-2 text-sm font-bold text-slate-700 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100 cursor-pointer">
                                @foreach([5, 10, 20, 50, 100] as $limit)
                                    <option value="{{ $limit }}" {{ request('per_page') == $limit ? 'selected' : '' }}>{{ $limit }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

                @if(request('search') || request('filter_date') || request('per_page'))
                    <a href="{{ request()->url() }}" class="text-xs font-bold text-red-500 underline">[Reset Semua Filter]</a>
                @endif

                <div class="mt-4">
                    <p class="md:hidden text-xs font-bold text-slate-400 mb-2 flex items-center gap-1.5"><i class="fas fa-arrows-left-right"></i> Geser tabel untuk melihat detail</p>
                    <div class="overflow-x-auto rounded-xl border border-slate-100">
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
                                    $namaAsisten = $s->assistant_names;
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
                                        Tidak ada kelas praktikum yang cocok dengan kriteria pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>

                @if(method_exists($schedules, 'links'))
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        {{ $schedules->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

           
            <div class="grid gap-4 grid-cols-1 sm:grid-cols-3 xl:grid-cols-1 h-fit">
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
                                {{ $daySchedules->flatMap(fn($s) => $s->assistants->pluck('nama_asisten'))->unique()->count() }}
                            </p>
                        </div>
                        <i class="fa-solid fa-users text-2xl text-sky-700/80"></i>
                    </div>
                </article>
            </div>
        </section>

       
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-md shadow-slate-900/5 min-w-0 overflow-hidden">
    <div class="mb-5 flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
            <i class="fa-solid fa-user-group text-xl"></i>
        </div>
        <div>
            <h2 class="text-base font-extrabold text-slate-900">Status Petugas Asisten</h2>
            <p class="text-sm text-slate-500">Daftar seluruh asisten yang menjaga laboratorium hari ini</p>
        </div>
    </div>

    <div class="mt-4">
        <p class="md:hidden text-xs font-bold text-slate-400 mb-2 flex items-center gap-1.5"><i class="fas fa-arrows-left-right"></i> Geser tabel untuk melihat detail</p>
        <div class="overflow-x-auto rounded-xl border border-slate-100">
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
                    // 1. Ambil semua jadwal hari ini yang sudah ada asistennya (via pivot)
                    $rawSchedules = $daySchedules->filter(function($item) {
                        return $item->assistants->isNotEmpty();
                    });

                    // 2. Grouping berdasarkan Nama Asisten (karena sekarang many-to-many)
                    $byAssistantName = collect();
                    foreach ($rawSchedules as $schedule) {
                        foreach ($schedule->assistants as $asisten) {
                            $nama = $asisten->nama_asisten;
                            if (!$byAssistantName->has($nama)) {
                                $byAssistantName[$nama] = collect();
                            }
                            $byAssistantName[$nama]->push($schedule);
                        }
                    }

                    $mergedSchedules = collect();

                    foreach ($byAssistantName as $namaAsisten => $slots) {
                        // Urutkan slot waktu milik asisten ini dari yang paling pagi
                        $sortedSlots = $slots->sortBy('jam_mulai')->values();
                        
                        if ($sortedSlots->isEmpty()) continue;

                        // Set cetakan blok sesi pertama untuk asisten ini
                        $currentBlock = [
                            'nama_asisten'      => $namaAsisten,
                            'jam_mulai'         => $sortedSlots[0]->jam_mulai,
                            'jam_selesai'       => $sortedSlots[0]->jam_selesai,
                            'matkul_list'       => [trim($sortedSlots[0]->matkul)],
                            'labs_list'         => [$sortedSlots[0]->lab->nama_lab ?? $sortedSlots[0]->id_lab]
                        ];

                        // Bandingkan sesi pertama dengan sesi-sesi berikutnya
                        for ($i = 1; $i < $sortedSlots->count(); $i++) {
                            $nextSlot = $sortedSlots[$i];
                            
                            $currentEnd = date('H:i', strtotime($currentBlock['jam_selesai']));
                            $nextStart  = date('H:i', strtotime($nextSlot->jam_mulai));

                            if ($currentEnd === $nextStart) {
                                $currentBlock['jam_selesai'] = $nextSlot->jam_selesai;
                                
                                $matkulTrimmed = trim($nextSlot->matkul);
                                if (!in_array($matkulTrimmed, $currentBlock['matkul_list'])) {
                                    $currentBlock['matkul_list'][] = $matkulTrimmed;
                                }
                                
                                $labName = $nextSlot->lab->nama_lab ?? $nextSlot->id_lab;
                                if (!in_array($labName, $currentBlock['labs_list'])) {
                                    $currentBlock['labs_list'][] = $labName;
                                }
                            } else {
                                $mergedSchedules->push((object)$currentBlock);
                                
                                $currentBlock = [
                                    'nama_asisten'      => $namaAsisten,
                                    'jam_mulai'         => $nextSlot->jam_mulai,
                                    'jam_selesai'       => $nextSlot->jam_selesai,
                                    'matkul_list'       => [trim($nextSlot->matkul)],
                                    'labs_list'         => [$nextSlot->lab->nama_lab ?? $nextSlot->id_lab]
                                ];
                            }
                        }
                        $mergedSchedules->push((object)$currentBlock);
                    }

                    // 3. Kembalikan urutan tampilan baris tabel secara kronologis
                    $finalSchedules = $mergedSchedules->sortBy('jam_mulai');
                @endphp
                
                @forelse($finalSchedules as $s)
                    @php
                        $namaAsisten = $s->nama_asisten ?? 'Asisten';
                        $inisial     = strtoupper(substr(trim($namaAsisten), 0, 1));
                        
                        // Satukan nama lab & matkul yang sudah berhasil dikompresi menggunakan koma
                        $namaLab     = implode(', ', $s->labs_list);
                        $daftarMatkul = implode(', ', $s->matkul_list);
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
                        <td class="px-4 py-4 italic text-slate-600">
                            {{ $daftarMatkul }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm font-bold text-slate-400">
                            Tidak ada asisten yang terjadwal bertugas hari ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</section>
    </div>
@endsection