@extends('layouts.spv')

@section('title', 'Dashboard')
@vite(['resources/css/app.css', 'resources/js/app.js'])
@section('content')

    <div class="space-y-5 w-full min-w-0">
        <section>
            <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900">Dashboard</h1>
            <p class="mt-1 text-sm font-medium text-slate-500">Memantau indikator kinerja utama Anda</p>
        </section>

        <section class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_14rem] 2xl:grid-cols-[minmax(0,1fr)_15rem]">
            
           
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-md shadow-slate-900/5 h-fit min-w-0 overflow-hidden sm:p-5">
                
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
                    <div class="space-y-3 lg:hidden">
                        @forelse($schedules as $s)
                            @php
                                $namaLab = $s->lab->nama_lab ?? $s->id_lab;
                                $namaAsisten = $s->assistant_names;
                            @endphp
                            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm shadow-slate-900/5">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <span class="block text-[11px] font-extrabold uppercase tracking-wide text-sky-700">{{ $s->hari }}{{ strtolower($s->hari) === 'sabtu' ? ' (Kelas Karyawan)' : '' }}</span>
                                        <span class="text-sm font-bold text-slate-700">{{ \Carbon\Carbon::parse($s->tanggal)->format('d M Y') }}</span>
                                    </div>
                                    <span class="shrink-0 rounded-lg bg-sky-50 px-3 py-1 text-xs font-black text-sky-700 ring-1 ring-sky-100">
                                        {{ $namaLab }}
                                    </span>
                                </div>

                                <div class="mt-4 grid gap-3 text-sm">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Jam</p>
                                        <p class="mt-0.5 font-mono font-bold text-slate-700">
                                            {{ date('H:i', strtotime($s->jam_mulai)) }} - {{ date('H:i', strtotime($s->jam_selesai)) }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Mata Kuliah</p>
                                        <p class="mt-0.5 font-bold leading-snug text-slate-800">{{ $s->matkul }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Dosen</p>
                                        <p class="mt-0.5 leading-snug text-slate-600">{{ $s->dosen }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Asisten</p>
                                        <p class="mt-0.5 leading-snug text-slate-600">{{ $namaAsisten ?: '-' }}</p>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 p-6 text-center text-sm font-bold text-slate-400">
                                Tidak ada kelas praktikum yang cocok dengan kriteria pencarian.
                            </div>
                        @endforelse
                    </div>

                    <div class="hidden overflow-hidden rounded-xl border border-slate-100 lg:block">
                        <table class="w-full table-fixed text-left text-[11px] xl:text-xs">
                            <thead class="border-b border-slate-200 bg-slate-100/80 text-[10px] font-extrabold uppercase tracking-[0.08em] text-slate-500 xl:text-xs">
                                <tr class="divide-x divide-slate-200/70">
                                    <th class="sticky top-0 z-10 w-[13%] bg-slate-100/90 px-3 py-3 text-left align-middle">Tanggal</th>
                                    <th class="sticky top-0 z-10 w-[9%] bg-slate-100/90 px-3 py-3 text-left align-middle">Lab</th>
                                    <th class="sticky top-0 z-10 w-[16%] bg-slate-100/90 px-3 py-3 text-left align-middle">Jam</th>
                                    <th class="sticky top-0 z-10 w-[25%] bg-slate-100/90 px-3 py-3 text-left align-middle">Mata Kuliah</th>
                                    <th class="sticky top-0 z-10 w-[24%] bg-slate-100/90 px-3 py-3 text-left align-middle">Dosen</th>
                                    <th class="sticky top-0 z-10 w-[13%] bg-slate-100/90 px-3 py-3 text-left align-middle">Asisten</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($schedules as $s)
                                    @php
                                        $namaLab = $s->lab->nama_lab ?? $s->id_lab;
                                        $namaAsisten = $s->assistant_names;
                                    @endphp
                                    <tr class="align-top hover:bg-slate-50">
                                        <td class="px-2 py-3">
                                            <span class="block text-[10px] font-extrabold uppercase text-sky-700">{{ $s->hari }}{{ strtolower($s->hari) === 'sabtu' ? ' (Kelas Karyawan)' : '' }}</span>
                                            <span class="text-slate-600">{{ \Carbon\Carbon::parse($s->tanggal)->format('d M Y') }}</span>
                                        </td>
                                        <td class="px-2 py-3 font-bold leading-snug text-slate-700">{{ $namaLab }}</td>
                                        <td class="px-2 py-3 font-mono leading-snug text-slate-700">
                                            {{ date('H:i', strtotime($s->jam_mulai)) }} - {{ date('H:i', strtotime($s->jam_selesai)) }}
                                        </td>
                                        <td class="break-words px-2 py-3 font-semibold leading-snug text-slate-700">{{ $s->matkul }}</td>
                                        <td class="break-words px-2 py-3 leading-snug text-slate-600">{{ $s->dosen }}</td>
                                        <td class="break-words px-2 py-3 leading-snug text-slate-600">{{ $namaAsisten ?: '-' }}</td>
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

                @if(method_exists($schedules, 'links') && $schedules->hasPages())
                    <div class="mt-4 flex flex-col gap-3 border-t border-slate-100 pt-4 text-xs font-bold text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                        <p>
                            Menampilkan
                            <span class="text-slate-800">{{ $schedules->firstItem() }}</span>
                            -
                            <span class="text-slate-800">{{ $schedules->lastItem() }}</span>
                            dari
                            <span class="text-slate-800">{{ $schedules->total() }}</span>
                            jadwal
                        </p>

                        <div class="flex flex-wrap items-center gap-1.5">
                            @if($schedules->onFirstPage())
                                <span class="inline-flex h-9 items-center rounded-lg border border-slate-200 bg-slate-50 px-3 text-slate-300">Sebelumnya</span>
                            @else
                                <a href="{{ $schedules->appends(request()->query())->previousPageUrl() }}" class="inline-flex h-9 items-center rounded-lg border border-slate-200 bg-white px-3 text-slate-600 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700">Sebelumnya</a>
                            @endif

                            @foreach($schedules->appends(request()->query())->getUrlRange(1, $schedules->lastPage()) as $page => $url)
                                @if($page === $schedules->currentPage())
                                    <span class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg bg-sky-700 px-3 text-white shadow-sm shadow-sky-700/20">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-slate-600 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if($schedules->hasMorePages())
                                <a href="{{ $schedules->appends(request()->query())->nextPageUrl() }}" class="inline-flex h-9 items-center rounded-lg border border-slate-200 bg-white px-3 text-slate-600 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700">Selanjutnya</a>
                            @else
                                <span class="inline-flex h-9 items-center rounded-lg border border-slate-200 bg-slate-50 px-3 text-slate-300">Selanjutnya</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

           
            <div class="grid gap-4 grid-cols-1 sm:grid-cols-3 xl:grid-cols-1 h-fit">
                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-md shadow-slate-900/5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-slate-500">Total Matkul Hari Ini</p>
                            <p class="mt-2 text-2xl font-black text-slate-900">{{ $daySchedules->count() }}</p>
                        </div>
                        <i class="fa-solid fa-building text-xl text-sky-700/80"></i>
                    </div>
                </article>
                
                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-md shadow-slate-900/5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-slate-500">Jumlah Laboratorium</p>
                            <p class="mt-2 text-2xl font-black text-slate-900">{{ isset($labs) ? $labs->count() : 0 }}</p>
                        </div>
                        <i class="fa-solid fa-desktop text-xl text-sky-700/80"></i>
                    </div>
                </article>
                
                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-md shadow-slate-900/5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-slate-500">Asisten Bertugas</p>
                            <p class="mt-2 text-2xl font-black text-slate-900">
                                {{ $daySchedules->flatMap(fn($s) => $s->assistants->pluck('nama_asisten'))->unique()->count() }}
                            </p>
                        </div>
                        <i class="fa-solid fa-users text-xl text-sky-700/80"></i>
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

    <div class="mt-4">
        <div class="space-y-2 lg:hidden">
            @forelse($finalSchedules as $s)
                @php
                    $namaAsisten = $s->nama_asisten ?? 'Asisten';
                    $inisial     = strtoupper(substr(trim($namaAsisten), 0, 1));
                    $namaLab     = implode(', ', $s->labs_list);
                    $daftarMatkul = implode(', ', $s->matkul_list);
                @endphp
                <article class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm shadow-slate-900/5">
                    <div class="flex items-start gap-2.5">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-700 text-[11px] font-extrabold text-white">
                            {{ $inisial }}
                        </span>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="truncate text-sm font-extrabold text-slate-800">
                                    {{ $namaAsisten }}
                                </h3>
                                <span class="shrink-0 rounded-md bg-sky-50 px-2 py-0.5 text-[10px] font-black text-sky-700 ring-1 ring-sky-100">
                                    {{ $namaLab }}
                                </span>
                            </div>

                            <p class="mt-1 font-mono text-[11px] font-bold text-slate-500">
                                {{ date('H:i', strtotime($s->jam_mulai)) }} - {{ date('H:i', strtotime($s->jam_selesai)) }}
                            </p>

                            <p class="mt-1.5 text-xs font-medium leading-snug text-slate-600">
                                {{ $daftarMatkul }}
                            </p>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-5 text-center text-sm font-bold text-slate-400">
                    Tidak ada asisten yang terjadwal bertugas hari ini.
                </div>
            @endforelse
        </div>

        <div class="hidden overflow-hidden rounded-xl border border-slate-100 lg:block">
            <table class="w-full table-fixed text-left text-xs">
                <thead class="border-b border-slate-200 bg-slate-100/80 text-xs font-extrabold uppercase tracking-[0.08em] text-slate-500">
                    <tr class="divide-x divide-slate-200/70">
                        <th class="w-[26%] px-5 py-4 text-left align-middle">Nama Asisten</th>
                        <th class="w-[18%] px-5 py-4 text-left align-middle">Menjaga Lab</th>
                        <th class="w-[20%] px-5 py-4 text-left align-middle">Waktu Tugas</th>
                        <th class="w-[36%] px-5 py-4 text-left align-middle">Mata Kuliah Kelolaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($finalSchedules as $s)
                        @php
                            $namaAsisten = $s->nama_asisten ?? 'Asisten';
                            $inisial     = strtoupper(substr(trim($namaAsisten), 0, 1));
                            $namaLab     = implode(', ', $s->labs_list);
                            $daftarMatkul = implode(', ', $s->matkul_list);
                        @endphp
                        <tr class="align-top hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-sky-700 text-xs font-extrabold text-white">
                                        {{ $inisial }}
                                    </span>
                                    <span class="break-words font-semibold leading-snug text-slate-700">{{ $namaAsisten }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-md bg-sky-100 px-3 py-1 text-xs font-extrabold text-sky-700">
                                    {{ $namaLab }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-mono leading-snug text-slate-700">
                                {{ date('H:i', strtotime($s->jam_mulai)) }} - {{ date('H:i', strtotime($s->jam_selesai)) }}
                            </td>
                            <td class="break-words px-5 py-4 italic leading-snug text-slate-600">
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
