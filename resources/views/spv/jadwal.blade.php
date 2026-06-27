<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- Menggunakan aset Tailwind & JS bawaan proyek --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
     <link rel="icon" type="image/LogoICT.png" href="{{ asset('images/LogoICT.png') }}">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Manajemen Jadwal - Lab ICT</title>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-blue-100 font-sans text-slate-800 antialiased">

@extends('layouts.spv')

@section('title', 'Manajemen Jadwal')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Manajemen Jadwal</h1>
        <p class="mt-1 text-sm font-medium text-slate-500">Kelola jadwal praktikum dan persetujuan peminjaman lab.</p>
    </div>

    {{-- Alert Notifikasi Session (Pesan Berhasil/Gagal dari Controller) --}}
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm flex items-start gap-3">
            <i class="fas fa-check-circle text-emerald-500 text-xl mt-0.5"></i>
            <div>
                <h3 class="text-sm font-bold text-emerald-800">Berhasil</h3>
                <p class="text-sm font-medium text-emerald-700 mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-red-500 text-xl mt-0.5"></i>
            <div>
                <h3 class="text-sm font-bold text-red-800">Gagal / Perhatian</h3>
                <p class="text-sm font-medium text-red-700 mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-red-500 text-xl mt-0.5"></i>
            <div>
                <h3 class="text-sm font-bold text-red-800">Gagal Memproses Data</h3>
                <ul class="list-disc pl-5 mt-1 text-sm font-medium text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Munculkan alert pop-up khusus agar lebih terlihat --}}
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                showCustomAlert("{{ $errors->first() }}", "Gagal Memproses");
            });
        </script>
    @endif

    @if(isset($conflicts) && $conflicts->count() > 0)
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5 shadow-xl shadow-red-950/5 space-y-4">
            <div class="flex items-center gap-3 text-red-800">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-100 text-red-600 animate-pulse">
                    <i class="fas fa-exclamation-triangle text-sm"></i>
                </span>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider">Terdeteksi Jadwal Bentrok / Tabrakan!</h3>
                    <p class="text-xs font-semibold text-red-600">Ada beberapa kelas praktikum yang menempati ruang Lab yang sama pada jam operasional yang sama.</p>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-red-200 bg-white">
                <table class="w-full min-w-[800px] text-left text-xs">
                    <thead class="bg-red-600 text-white font-extrabold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Tanggal / Hari</th>
                            <th class="px-5 py-3">Ruang Lab</th>
                            <th class="px-5 py-3">Jam Praktikum (24 Jam)</th>
                            <th class="px-5 py-3">Mata Kuliah</th>
                            <th class="px-5 py-3">Dosen Pengampu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-red-100 text-red-950 bg-red-50/30">
                        @foreach($conflicts as $c)
                            <tr class="hover:bg-red-100/40 transition font-bold">
                                <td class="px-5 py-3 text-slate-700">
                                    {{ \Carbon\Carbon::parse($c->tanggal)->format('d M Y') }}
                                    <span class="text-[10px] bg-red-100 text-red-700 rounded p-0.5 px-1.5 ml-1">{{ strtoupper($c->hari) }}{{ strtolower($c->hari) === 'sabtu' ? ' (KELAS KARYAWAN)' : '' }}</span>
                                </td>
                                <td class="px-5 py-3 text-red-700 font-extrabold">
                                    {{ $c->lab->nama_lab ?? 'LAB TANPA NAMA' }}
                                </td>
                                <td class="px-5 py-3 tracking-wide text-amber-800 font-mono">
                                    {{ date('H:i', strtotime($c->jam_mulai)) }} - {{ date('H:i', strtotime($c->jam_selesai)) }}
                                </td>
                                <td class="px-5 py-3 text-slate-900">{{ $c->matkul }}</td>
                                <td class="px-5 py-3 text-slate-500 font-medium italic">{{ $c->dosen }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-white/80 bg-white/80 p-5 shadow-xl shadow-blue-950/5 backdrop-blur">
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <button id="btnCetakPDF" class="inline-flex h-11 items-center gap-2 rounded-xl bg-red-600 px-5 text-sm font-bold text-white shadow-md shadow-red-600/10 transition hover:bg-red-700">
                <i class="fas fa-file-pdf"></i> Cetak PDF Sesuai Filter
            </button>

            {{-- Tombol Tambah Jadwal Manual --}}
            <button onclick="toggleTambahJadwal()" class="inline-flex h-11 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-md shadow-blue-600/10 transition hover:bg-blue-700">
                <i class="fas fa-plus-circle"></i> Tambah Jadwal Manual
            </button>

            {{-- Form Import XLSX --}}
            <form action="{{ route('schedule.import') }}" method="POST" enctype="multipart/form-data" id="form-import-cepat"
                  class="m-0 flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/60 p-1 px-3">
                @csrf
                <span class="text-[10px] font-extrabold tracking-wider text-slate-400 uppercase">Periode:</span>
                <input type="date" name="start_date" id="import_start_date" required class="rounded-lg border border-slate-200 bg-white p-1.5 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500">
                <span class="text-xs font-bold text-slate-400">s/d</span>
                <input type="date" name="end_date" id="import_end_date" required class="rounded-lg border border-slate-200 bg-white p-1.5 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500">
                <input type="file" name="file_excel" id="file_excel_cepat" accept=".xlsx, .xls, .csv" class="hidden" onchange="triggerAutoSubmit()">
                <button type="button" id="btn-import-xlsx" onclick="cekTanggalSebelumPilihFile()" class="inline-flex h-8 items-center rounded-lg bg-emerald-600 px-3 text-xs font-extrabold text-white shadow-sm transition hover:bg-emerald-700">
                    <i class="fas fa-file-excel mr-1.5"></i> IMPORT XLSX
                </button>
            </form>

            <form action="{{ route('bersih') }}" method="POST" class="m-0 inline-flex" id="form-bersih-jadwal">
                @csrf
                @method('DELETE')
                <button type="button" onclick="showCustomConfirm('Apakah Anda yakin ingin MENGHAPUS SEMUA JADWAL yang ada di dalam database? Tindakan ini permanen dan data tidak dapat dikembalikan.', 'Peringatan Keras!', () => document.getElementById('form-bersih-jadwal').submit())" class="inline-flex h-11 items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 text-sm font-bold text-red-600 shadow-sm transition hover:bg-red-600 hover:text-white hover:border-red-600">
                    <i class="fas fa-trash-alt"></i> Kosongkan Jadwal
                </button>
            </form>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <select id="filterType" class="h-11 rounded-xl border border-slate-200 bg-white px-4 pr-8 text-sm font-bold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 cursor-pointer">
                <option value="all">Semua Jadwal (MIX)</option>
                <option value="praktikum">Jadwal Matkul Saja</option>
                <option value="ra">Jaga RA Saja</option>
            </select>

            <select id="filterLab" class="h-11 rounded-xl border border-slate-200 bg-white px-4 pr-8 text-sm font-bold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 cursor-pointer">
                <option value="">Semua Lab / Ruangan</option>
                @foreach($labs->sortBy('nama_lab') as $lab)
                    @if(strtoupper($lab->nama_lab) !== 'RUANG ASISTEN')
                        <option value="{{ $lab->nama_lab }}">{{ strtoupper($lab->nama_lab) }}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>

    {{-- FORM TAMBAH MANUAL --}}
    <div id="form-tambah-jadwal" class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-blue-950/5 transition-all">
        <div class="bg-slate-50 border-b border-slate-100 px-6 py-4">
            <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Form Tambah Jadwal Manual</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('spv.store') }}" method="POST" class="grid gap-4 sm:grid-cols-2">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Tanggal Mulai / Utama</label>
                    <input type="date" name="tanggal" id="input_tanggal" required class="trigger-ajax h-11 w-full rounded-xl border border-slate-200 px-4 text-sm font-medium text-slate-700 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Tipe Peminjaman</label>
                    <select name="repeat_type" id="repeat_type" onchange="toggleRepeatDate()" class="trigger-ajax h-11 w-full rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700 outline-none focus:border-blue-500 cursor-pointer">
                        <option value="single">Hanya Sekali (1 Hari)</option>
                        <option value="weekly">Berulang Setiap Minggu</option>
                        <option value="daily">Setiap Hari (Senin - Minggu)</option>
                        <option value="weekdays">Setiap Hari Kerja (Senin - Jumat)</option>
                    </select>
                </div>
                <div id="end_date_container" class="hidden">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Sampai Tanggal (Selesai)</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="trigger-ajax h-11 w-full rounded-xl border border-slate-200 px-4 text-sm font-medium text-slate-700 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Ruang Lab</label>
                    <select name="id_lab" id="select_lab" required disabled class="h-11 w-full rounded-xl border border-slate-200 bg-slate-100 px-4 text-sm font-semibold text-slate-500 outline-none focus:border-blue-500 cursor-not-allowed transition">
                        <option value="">Isi Info Tanggal, Waktu & SKS Dahulu</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Jam Mulai (Format 24 Jam)</label>
                    <div class="flex gap-2">
                        <input type="text" name="jam_mulai" id="input_jam" class="time-formatter trigger-ajax h-11 w-full rounded-xl border border-slate-200 px-4 text-sm font-semibold font-mono text-slate-700 outline-none focus:border-blue-500 text-center tracking-widest" placeholder="08:00" maxlength="5" required>
                        <select id="select_jam_template" class="h-11 rounded-xl border border-slate-200 bg-white px-3 text-xs font-bold text-slate-500 outline-none focus:border-blue-500 cursor-pointer">
                            <option value="">-- Template (Pilih Tanggal Dahulu) --</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Jumlah SKS</label>
                    <input type="number" name="sks" id="input_sks" required min="1" max="6" placeholder="Contoh: 2" class="trigger-ajax h-11 w-full rounded-xl border border-slate-200 px-4 text-sm font-medium text-slate-700 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Mata Kuliah</label>
                    <input type="text" name="matkul" required placeholder="Nama Matkul" class="h-11 w-full rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Nama Dosen</label>
                    <input type="text" name="dosen" required placeholder="Nama Dosen" class="h-11 w-full rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700 outline-none focus:border-blue-500">
                </div>
                <div class="sm:col-span-2 text-right mt-2">
                    <button type="submit" class="inline-flex h-11 items-center gap-2 rounded-xl bg-emerald-600 px-6 text-sm font-bold text-white shadow-lg shadow-emerald-600/10 transition hover:bg-emerald-700">
                        <i class="fas fa-save"></i> Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="inline-flex items-center gap-3 rounded-xl border border-slate-200 bg-white/60 p-3 px-4 shadow-sm backdrop-blur">
        <form action="{{ route('spv.jadwal') }}" method="GET" id="form-filter" class="m-0 flex items-center gap-3">
            <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Cek Jadwal Tanggal:</label>
            <input type="date" name="filter_date" value="{{ request('filter_date', now()->toDateString()) }}" onchange="this.form.submit()" class="h-9 rounded-lg border border-slate-200 px-3 text-xs font-bold text-slate-700 outline-none focus:border-blue-500">
            @if(request('filter_date'))
                <a href="{{ route('spv.jadwal') }}" class="text-xs font-bold text-red-500 hover:underline">[Reset Filter Tanggal]</a>
            @endif
        </form>
    </div>

    {{-- DATA LIMIT DISPLAY CONTROL --}}
    <div class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-wide">
        <span>Tampilkan</span>
        <select class="limitSelect h-8 rounded-lg border border-slate-200 bg-white px-2 pr-8 text-xs font-extrabold text-slate-700 outline-none cursor-pointer">
            <option value="5" selected>5</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
        <span>Data</span>
    </div>

    <div class="overflow-hidden rounded-2xl border border-white bg-white shadow-2xl shadow-blue-950/5">
        <div class="overflow-visible">
            <table class="w-full table-fixed border-collapse text-left text-[13px] 2xl:text-sm" id="scheduleTable">
                <thead class="hidden bg-blue-900 text-white text-xs font-extrabold uppercase tracking-wider lg:table-header-group">
                    <tr>
                        <th class="w-[11%] px-5 py-4">Lab</th>
                        <th class="w-[15%] px-4 py-4">Jam</th>
                        <th class="w-[24%] px-4 py-4">Mata Kuliah</th>
                        <th class="w-[7%] px-3 py-4 text-center">Kode</th>
                        <th class="w-[18%] px-4 py-4">Dosen</th>
                        <th class="w-[14%] px-4 py-4">Asisten</th>
                        <th class="w-[11%] px-5 py-4 text-center">Detail</th>
                    </tr>
                </thead>
                <tbody class="block space-y-3 bg-slate-50 p-3 lg:table-row-group lg:space-y-0 lg:bg-white lg:p-0 lg:divide-y lg:divide-slate-100">
                    @php $lastScheduleDate = null; @endphp
                    @foreach($schedules as $s)
                    @php $currentScheduleDate = \Carbon\Carbon::parse($s->tanggal)->format('Y-m-d'); @endphp
                    @if($lastScheduleDate !== $currentScheduleDate)
                        <tr class="schedule-date-row block lg:table-row lg:bg-slate-50/90" data-date="{{ $currentScheduleDate }}">
                            <td colspan="7" class="block px-1 py-1 lg:table-cell lg:px-4 lg:py-3">
                                <div class="flex flex-wrap items-center gap-2 text-xs font-black uppercase tracking-wide text-blue-700">
                                    <i class="fas fa-calendar-day text-blue-500"></i>
                                    <span>{{ $s->hari }}{{ strtolower($s->hari) === 'sabtu' ? ' (Kelas Karyawan)' : '' }}</span>
                                    <span class="text-slate-300">/</span>
                                    <span class="text-slate-600">{{ \Carbon\Carbon::parse($s->tanggal)->translatedFormat('d M Y') }}</span>
                                </div>
                            </td>
                        </tr>
                        @php $lastScheduleDate = $currentScheduleDate; @endphp
                    @endif

                    <tr data-hari="{{ $s->hari }}"
                        data-tanggal="{{ $currentScheduleDate }}"
                        data-lab="{{ $s->lab->nama_lab ?? '' }}"
                        data-jam-mulai="{{ date('H:i', strtotime($s->jam_mulai)) }}"
                        data-jam-selesai="{{ date('H:i', strtotime($s->jam_selesai)) }}"
                        data-matkul="{{ $s->matkul }}"
                        data-kode="{{ \Illuminate\Support\Str::substr($s->matkul, -4) }}"
                        data-dosen="{{ $s->dosen }}"
                        data-asisten="{{ $s->assistant_names }}"
                        class="schedule-row block rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition lg:table-row lg:rounded-none lg:border-0 lg:bg-transparent lg:p-0 lg:shadow-none lg:hover:bg-slate-50/60">
                        <td class="block align-middle sm:inline-block sm:w-[48%] lg:table-cell lg:w-auto lg:px-5 lg:py-4">
                            <form action="{{ route('spv.update', $s->id_jadwal) }}" method="POST" id="update-form-{{ $s->id_jadwal }}" class="hidden">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="scope" id="scope-field-{{ $s->id_jadwal }}" value="single">
                                <input type="hidden" name="tanggal" id="tanggal-field-{{ $s->id_jadwal }}" value="{{ \Carbon\Carbon::parse($s->tanggal)->format('Y-m-d') }}">
                            </form>
                            <span class="mb-1 block text-[10px] font-black uppercase tracking-wider text-slate-400 lg:hidden">Lab</span>
                            <span class="inline-flex min-h-10 w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-3 text-xs font-black text-slate-800">
                                {{ $s->lab->nama_lab ?? '-' }}
                            </span>
                        </td>

                        <td class="mt-3 block align-middle sm:mt-0 sm:inline-block sm:w-[50%] sm:pl-2 lg:table-cell lg:w-auto lg:px-4 lg:py-4">
                            <span class="mb-1 block text-[10px] font-black uppercase tracking-wider text-slate-400 lg:hidden">Jam</span>
                            <span class="inline-flex min-h-10 w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-3 font-mono text-xs font-black tracking-widest text-slate-800">
                                {{ date('H:i', strtotime($s->jam_mulai)) }} - {{ date('H:i', strtotime($s->jam_selesai)) }}
                            </span>
                        </td>

                        <td class="mt-3 block align-middle lg:table-cell lg:px-4 lg:py-4">
                            <span class="mb-1 block text-[10px] font-black uppercase tracking-wider text-slate-400 lg:hidden">Mata Kuliah</span>
                            <span class="line-clamp-2 block rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-black leading-relaxed text-slate-800" title="{{ $s->matkul }}">
                                {{ $s->matkul }}
                            </span>
                        </td>

                        <td class="mt-3 block text-left text-xs font-black uppercase tracking-wide text-blue-700 align-middle sm:inline-block sm:w-[34%] lg:table-cell lg:w-auto lg:px-3 lg:py-4 lg:text-center">
                            <span class="mb-1 block text-[10px] font-black uppercase tracking-wider text-slate-400 lg:hidden">Kode</span>
                            <span class="inline-flex min-h-10 w-full items-center justify-center rounded-xl border border-blue-100 bg-blue-50 px-3 text-xs font-black text-blue-700 lg:min-h-0 lg:border-0 lg:bg-transparent lg:p-0">
                                {{ \Illuminate\Support\Str::substr($s->matkul, -4) }}
                            </span>
                        </td>

                        <td class="mt-3 block align-middle sm:inline-block sm:w-[64%] sm:pl-2 lg:table-cell lg:w-auto lg:px-4 lg:py-4">
                            <span class="mb-1 block text-[10px] font-black uppercase tracking-wider text-slate-400 lg:hidden">Dosen</span>
                            <span class="line-clamp-2 block rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold leading-relaxed text-slate-700" title="{{ $s->dosen }}">
                                {{ $s->dosen }}
                            </span>
                        </td>

                        <td class="mt-3 block align-middle lg:table-cell lg:px-4 lg:py-4">
                            <span class="mb-1 block text-[10px] font-black uppercase tracking-wider text-slate-400 lg:hidden">Asisten</span>
                            <span class="line-clamp-2 block rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold leading-relaxed text-slate-700" title="{{ $s->assistant_names }}">
                                {{ $s->assistant_names ?: '-' }}
                            </span>
                        </td>

                        <td class="mt-4 block border-t border-slate-100 pt-3 text-center align-middle lg:table-cell lg:mt-0 lg:border-t-0 lg:px-5 lg:py-4 lg:pt-0">
                            <button type="button" onclick="openScheduleDetailModal('schedule-detail-{{ $s->id_jadwal }}')" class="inline-flex h-10 w-full items-center justify-center gap-1.5 rounded-xl bg-blue-700 px-3 text-xs font-black uppercase text-white shadow-md shadow-blue-700/20 transition hover:bg-blue-800 lg:max-w-[7rem]">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @foreach($schedules as $s)
        <div id="schedule-detail-{{ $s->id_jadwal }}" class="fixed inset-0 z-[90] hidden items-start justify-center overflow-y-auto bg-slate-900/50 p-4 backdrop-blur-sm sm:items-center">
            <div class="my-6 w-full max-w-4xl overflow-visible rounded-2xl bg-white shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 bg-slate-50 px-5 py-4">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-slate-400">Detail Jadwal</p>
                        <h3 class="mt-1 text-lg font-black text-slate-900">{{ $s->matkul }}</h3>
                        <p class="mt-1 text-xs font-semibold text-slate-500">
                            {{ $s->hari }}{{ strtolower($s->hari) === 'sabtu' ? ' (Kelas Karyawan)' : '' }} - {{ \Carbon\Carbon::parse($s->tanggal)->translatedFormat('d M Y') }}
                        </p>
                    </div>
                    <button type="button" onclick="closeScheduleDetailModal('schedule-detail-{{ $s->id_jadwal }}')" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-500 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-100">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="grid gap-3 p-5 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Tanggal</p>
                        <input type="date" value="{{ \Carbon\Carbon::parse($s->tanggal)->format('Y-m-d') }}"
                               class="mt-2 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 outline-none focus:border-blue-500"
                               onchange="document.getElementById('tanggal-field-{{ $s->id_jadwal }}').value=this.value; document.getElementById('scope-field-{{ $s->id_jadwal }}').value='single'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Lab</p>
                        <select name="id_lab" class="mt-2 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 pr-8 text-sm font-bold text-slate-700 outline-none focus:border-blue-500 cursor-pointer" form="update-form-{{ $s->id_jadwal }}" onchange="document.getElementById('scope-field-{{ $s->id_jadwal }}').value='today_only'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
                            @foreach($s->getLabStatuses() as $lab)
                                @php
                                    $labJadwalSaatIni = $s->id_lab;
                                    $labDalamLoop = $lab['id_lab'];
                                    $apakahLabSendiri = ($labJadwalSaatIni == $labDalamLoop);
                                    $isRuangRa = str_contains(strtoupper($lab['nama_lab']), 'RA');
                                @endphp

                                @if($apakahLabSendiri)
                                    <option value="{{ $lab['id_lab'] }}" selected class="font-extrabold text-blue-600 bg-blue-50">
                                        {{ $lab['nama_lab'] }} (Aktif)
                                    </option>
                                @elseif($lab['status'] === 'busy' && !$isRuangRa)
                                    <option value="" disabled class="text-red-500 bg-red-50 cursor-not-allowed">
                                        {{ $lab['nama_lab'] }} (Dipakai)
                                    </option>
                                @else
                                    <option value="{{ $lab['id_lab'] }}" class="text-slate-700">
                                        {{ $lab['nama_lab'] }} {{ $isRuangRa && $lab['status'] === 'busy' ? '(Tersedia)' : '' }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Jam</p>
                        <div class="mt-2 grid grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)] items-center gap-2 font-mono">
                            <input type="text" name="jam_mulai" value="{{ date('H:i', strtotime($s->jam_mulai)) }}" placeholder="00:00" maxlength="5" class="time-formatter h-10 w-full rounded-xl border border-slate-200 bg-white px-2 text-center text-xs font-bold text-slate-700 tracking-widest outline-none focus:border-blue-500" form="update-form-{{ $s->id_jadwal }}" onchange="document.getElementById('scope-field-{{ $s->id_jadwal }}').value='single'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
                            <span class="text-sm font-bold text-slate-400">-</span>
                            <input type="text" name="jam_selesai" value="{{ date('H:i', strtotime($s->jam_selesai)) }}" placeholder="00:00" maxlength="5" class="time-formatter h-10 w-full rounded-xl border border-slate-200 bg-white px-2 text-center text-xs font-bold text-slate-700 tracking-widest outline-none focus:border-blue-500" form="update-form-{{ $s->id_jadwal }}" onchange="document.getElementById('scope-field-{{ $s->id_jadwal }}').value='single'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
                        </div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Kode Matkul</p>
                        <p class="mt-2 text-sm font-black uppercase tracking-wide text-blue-700">{{ \Illuminate\Support\Str::substr($s->matkul, -4) }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 sm:col-span-2 lg:col-span-3">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Mata Kuliah</p>
                        <input type="text" name="matkul" value="{{ $s->matkul }}" class="mt-2 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-bold text-slate-700 outline-none focus:border-blue-500 focus:bg-blue-50/20" form="update-form-{{ $s->id_jadwal }}" onchange="document.getElementById('scope-field-{{ $s->id_jadwal }}').value='single'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 lg:col-span-1">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Dosen</p>
                        <input type="text" name="dosen" value="{{ $s->dosen }}" class="mt-2 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 outline-none focus:border-blue-500 focus:bg-blue-50/20" form="update-form-{{ $s->id_jadwal }}" onchange="document.getElementById('scope-field-{{ $s->id_jadwal }}').value='single'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 sm:col-span-2 lg:col-span-3">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Asisten</p>
                        @php $modalAssignedIds = $s->assistant_ids; @endphp
                        <div class="multi-asisten-wrapper relative mt-2" data-form="update-form-{{ $s->id_jadwal }}" data-scope="scope-field-{{ $s->id_jadwal }}">
                            <button type="button" onclick="toggleMultiAsisten(this)" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 pr-10 text-sm font-semibold text-slate-700 outline-none focus:border-blue-500 cursor-pointer text-left truncate relative">
                                @if(count($modalAssignedIds) > 0)
                                    <span class="text-blue-600 font-bold">{{ count($modalAssignedIds) }} asisten dipilih</span>
                                @else
                                    <span class="text-slate-400">-- Pilih Asisten --</span>
                                @endif
                                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            </button>

                            <div class="multi-asisten-dropdown hidden absolute left-0 right-0 z-[120] mt-2 rounded-xl border border-slate-200 bg-white shadow-2xl shadow-slate-300/60 overflow-hidden">
                                <div class="overflow-y-auto" style="max-height: 280px;">
                                    @foreach($s->getAssistantStatuses() as $asisten)
                                        <label class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0 {{ $asisten->is_busy && !$asisten->is_assigned ? 'opacity-50' : '' }}">
                                            <input type="checkbox"
                                                   name="id_asisten[]"
                                                   value="{{ $asisten->id_asisten }}"
                                                   form="update-form-{{ $s->id_jadwal }}"
                                                   {{ $asisten->is_assigned ? 'checked' : '' }}
                                                   {{ $asisten->is_busy && !$asisten->is_assigned ? 'disabled' : '' }}
                                                   class="asisten-checkbox h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                            >
                                            <div class="flex-1 min-w-0">
                                                <span class="block truncate text-sm font-bold text-slate-700 {{ $asisten->is_assigned ? 'text-blue-600' : '' }}">{{ $asisten->nama }}</span>
                                                @if($asisten->is_busy)
                                                    <span class="text-[10px] font-bold text-red-500">{{ $asisten->label }}</span>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="flex gap-2 border-t border-slate-200 bg-slate-50 p-2">
                                    <button type="button" onclick="submitMultiAsisten(this)" class="flex-1 h-9 rounded-lg bg-blue-600 text-xs font-bold text-white hover:bg-blue-700 transition">
                                        <i class="fas fa-save mr-1"></i> Simpan
                                    </button>
                                    <button type="button" onclick="closeMultiAsisten(this)" class="h-9 px-4 rounded-lg bg-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-300 transition">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-end">
                    <button type="button" onclick="closeScheduleDetailModal('schedule-detail-{{ $s->id_jadwal }}')" class="inline-flex h-10 items-center justify-center rounded-xl bg-white px-5 text-sm font-bold text-slate-600 ring-1 ring-slate-200 transition hover:bg-slate-100">
                        Tutup
                    </button>
                    <button type="button"
                            class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-blue-50 px-5 text-sm font-black text-blue-700 ring-1 ring-blue-100 transition hover:bg-blue-700 hover:text-white"
                            onclick="
                                closeScheduleDetailModal('schedule-detail-{{ $s->id_jadwal }}');
                                showCustomConfirm('Menerapkan perubahan ini ke semua jadwal yang sama di minggu-minggu berikutnya?', 'Konfirmasi Perubahan Massal', () => {
                                    document.getElementById('scope-field-{{ $s->id_jadwal }}').value = 'all';
                                    document.getElementById('update-form-{{ $s->id_jadwal }}').submit();
                                });
                            ">
                        <i class="fas fa-layer-group text-xs"></i> Update
                    </button>
                    <form method="POST" action="{{ route('spv.delete', $s->id_jadwal) }}" class="m-0" id="form-delete-{{ $s->id_jadwal }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="closeScheduleDetailModal('schedule-detail-{{ $s->id_jadwal }}'); showCustomConfirm('Hapus jadwal ini?', 'Konfirmasi Hapus', () => document.getElementById('form-delete-{{ $s->id_jadwal }}').submit())" class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-red-50 px-5 text-sm font-black text-red-600 ring-1 ring-red-100 transition hover:bg-red-600 hover:text-white sm:w-auto">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <div id="noDataMessage" class="hidden flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">
        <i class="fas fa-calendar-times text-3xl mb-2 text-slate-300"></i>
        <p class="text-sm font-semibold">Tidak ada jadwal.</p>
    </div>
</div>

{{-- Custom Alert Modal --}}
<div id="custom-alert-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" style="transition: opacity 0.3s ease;">
    <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl text-center transform transition-transform scale-95" id="custom-alert-box" style="transition: transform 0.3s ease;">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 text-amber-500">
            <i class="fas fa-exclamation-triangle text-3xl"></i>
        </div>
        <h3 class="mb-2 text-lg font-extrabold text-slate-800" id="custom-alert-title">Peringatan</h3>
        <p class="mb-6 text-sm font-medium text-slate-600" id="custom-alert-message">Pesan peringatan akan muncul di sini.</p>
        <button type="button" onclick="closeCustomAlert()" class="w-full rounded-xl bg-slate-800 py-3 text-sm font-bold text-white shadow-md transition hover:bg-slate-700">
            Mengerti
        </button>
    </div>
</div>

{{-- Custom Confirm Modal --}}
<div id="custom-confirm-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" style="transition: opacity 0.3s ease;">
    <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl text-center transform transition-transform scale-95" id="custom-confirm-box" style="transition: transform 0.3s ease;">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-500">
            <i class="fas fa-question-circle text-3xl"></i>
        </div>
        <h3 class="mb-2 text-lg font-extrabold text-slate-800" id="custom-confirm-title">Konfirmasi</h3>
        <p class="mb-6 text-sm font-medium text-slate-600" id="custom-confirm-message">Apakah Anda yakin?</p>
        <div class="flex gap-3">
            <button type="button" onclick="closeCustomConfirm()" class="w-full rounded-xl bg-slate-100 py-3 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-200">
                Batal
            </button>
            <button type="button" id="custom-confirm-yes-btn" class="w-full rounded-xl bg-red-600 py-3 text-sm font-bold text-white shadow-md transition hover:bg-red-700">
                Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const filterLab = document.getElementById('filterLab');
    const filterType = document.getElementById('filterType');
    const limitSelect = document.querySelector('.limitSelect');
    const tableBody = document.querySelector('#scheduleTable tbody');
    const rows = Array.from(tableBody.querySelectorAll('tr.schedule-row'));
    const dateRows = Array.from(tableBody.querySelectorAll('tr.schedule-date-row'));
    const noDataMessage = document.getElementById('noDataMessage');

    let currentPage = 1;

    // Fungsi Konversi Waktu Menit Strict 24 Jam
    function hitungTotalMenit(timeStr) {
        if (!timeStr) return 0;
        let cleanTime = timeStr.replace(/[^\d:]/g, '').trim();
        const parts = cleanTime.split(':');
        return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
    }

    function jalankanLiveFiltering() {
        const selectedLab = filterLab.value;
        const selectedType = filterType.value;
        const limit = parseInt(limitSelect.value, 10);

        let filteredRows = rows.filter(tr => {
            let labName = tr.dataset.lab || '';
            labName = labName.replace(/[🟢|🔒]/g, '').replace('(Aktif)', '').replace('(Dipakai)', '').trim();

            const matchLab = !selectedLab || labName.toUpperCase().includes(selectedLab.toUpperCase());

            let matchType = true;
            if (selectedType === 'praktikum') {
                matchType = !labName.toUpperCase().includes('RUANG ASISTEN');
            } else if (selectedType === 'ra') {
                matchType = labName.toUpperCase().includes('RUANG ASISTEN');
            }

            return matchLab && matchType;
        });

        if (filteredRows.length === 0) {
            noDataMessage.classList.remove('hidden');
            noDataMessage.classList.add('flex');
        } else {
            noDataMessage.classList.add('hidden');
            noDataMessage.classList.remove('flex');
        }

        const totalPages = Math.ceil(filteredRows.length / limit) || 1;
        if (currentPage > totalPages) currentPage = totalPages;

        const startIdx = (currentPage - 1) * limit;
        const endIdx = startIdx + limit;

        const visibleDates = new Set();
        rows.forEach(tr => tr.style.display = 'none');
        dateRows.forEach(tr => tr.style.display = 'none');

        filteredRows.slice(startIdx, endIdx).forEach(tr => {
            tr.style.display = '';
            visibleDates.add(tr.dataset.tanggal);
        });

        dateRows.forEach(tr => {
            if (visibleDates.has(tr.dataset.date)) {
                tr.style.display = '';
            }
        });

        buatNavigasiPagination(totalPages);
    }

    function buatNavigasiPagination(totalPages) {
        let container = document.getElementById('clientPaginationControls');
        if (!container) {
            container = document.createElement('div');
            container.id = 'clientPaginationControls';
            container.className = 'mt-4 flex items-center justify-between border-t border-slate-100 pt-4 text-xs font-bold text-slate-500';
            tableBody.closest('.overflow-hidden').after(container);
        }

        container.innerHTML = `
            <div>Halaman <span class="text-slate-800 font-black">${currentPage}</span> dari <span class="text-slate-800 font-black">${totalPages}</span></div>
            <div class="flex items-center gap-2">
                <button type="button" id="btnPrevPage" ${currentPage === 1 ? 'disabled' : ''} class="px-3 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-40 disabled:pointer-events-none">Sebelumnya</button>
                <button type="button" id="btnNextPage" ${currentPage === totalPages ? 'disabled' : ''} class="px-3 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-40 disabled:pointer-events-none">Selanjutnya</button>
            </div>
        `;

        document.getElementById('btnPrevPage').addEventListener('click', () => { currentPage--; jalankanLiveFiltering(); });
        document.getElementById('btnNextPage').addEventListener('click', () => { currentPage++; jalankanLiveFiltering(); });
    }

    filterLab.addEventListener('change', () => { currentPage = 1; jalankanLiveFiltering(); });
    filterType.addEventListener('change', () => { currentPage = 1; jalankanLiveFiltering(); });
    limitSelect.addEventListener('change', () => { currentPage = 1; jalankanLiveFiltering(); });

    jalankanLiveFiltering();

    // SAKLAR CETAK PDF DENGAN FORMAT AMAN 24 JAM
    document.getElementById('btnCetakPDF')?.addEventListener('click', function() {
        const activeType = filterType.value;
        const selectedLab = filterLab.value;

        const rawEntries = [];
        rows.forEach(tr => {
            const hariAttribute = tr.getAttribute('data-hari');
            const tanggalValue = tr.getAttribute('data-tanggal');
            if (!tanggalValue) return;

            let labName = tr.dataset.lab || '';
            labName = labName.replace(/[🟢|🔒]/g, '').replace('(Aktif)', '').replace('(Dipakai)', '').trim();

            const matchLab = !selectedLab || labName.toUpperCase().includes(selectedLab.toUpperCase());
            let matchType = true;
            if (activeType === 'praktikum') matchType = !labName.toUpperCase().includes('RUANG ASISTEN');
            if (activeType === 'ra') matchType = labName.toUpperCase().includes('RUANG ASISTEN');

            if (matchLab && matchType) {
                // 🌟 FIX PDF: Cari class "time-formatter" karena tipe inputnya sekarang "text" bukan "time"
                let asistenName = (tr.dataset.asisten || '-').trim() || '-';

                let jMulaiClean = (tr.dataset.jamMulai || '').substring(0, 5);
                let jSelesaiClean = (tr.dataset.jamSelesai || '').substring(0, 5);

                let hariDisplay = hariAttribute;
                if (hariAttribute && hariAttribute.toLowerCase() === 'sabtu') {
                    hariDisplay = 'Sabtu (Kelas Karyawan)';
                }

                rawEntries.push({
                    hariTanggal: hariDisplay + ', ' + tanggalValue,
                    labName: labName,
                    jamMulaiStr: jMulaiClean,
                    jamSelesaiStr: jSelesaiClean,
                    menitMulai: hitungTotalMenit(jMulaiClean),
                    menitSelesai: hitungTotalMenit(jSelesaiClean),
                    matkul: tr.dataset.matkul || '',
                    kodeMatkul: tr.dataset.kode || tr.cells[3].innerText.trim(),
                    dosen: tr.dataset.dosen || '',
                    asistenName: asistenName
                });
            }
        });

        if (rawEntries.length === 0) {
            showCustomAlert("Tidak ada data aktif sesuai filter saat ini untuk dicetak!", "Data Kosong");
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4');
        doc.setFont("Helvetica", "bold"); doc.setFontSize(15);

        let pdfHeaders = [];
        let pdfBody = [];

        if (activeType === 'ra') {
            doc.text("LAPORAN JADWAL TUGAS JAGA RUANG ASISTEN (RA)", 14, 15);
            pdfHeaders = [['Hari & Tanggal', 'Ruangan', 'Waktu Tugas (24 Jam)', 'Nama Asisten']];

            const grouped = {};
            rawEntries.forEach(e => {
                const key = `${e.hariTanggal}_${e.labName}_${e.asistenName}`;
                if (!grouped[key]) grouped[key] = [];
                grouped[key].push(e);
            });

            Object.keys(grouped).forEach(k => {
                const list = grouped[k];
                list.sort((a, b) => a.menitMulai - b.menitMulai);
                let current = null;

                list.forEach(item => {
                    if (!current) {
                        current = { ...item };
                    } else {
                        const jeda = item.menitMulai - current.menitSelesai;
                        if (jeda >= 0 && jeda <= 15) {
                            current.menitSelesai = Math.max(current.menitSelesai, item.menitSelesai);
                            current.jamSelesaiStr = item.jamSelesaiStr;
                        } else {
                            pdfBody.push([current.hariTanggal, current.labName, `${current.jamMulaiStr} - ${current.jamSelesaiStr}`, current.asistenName]);
                            current = { ...item };
                        }
                    }
                });
                if (current) {
                    pdfBody.push([current.hariTanggal, current.labName, `${current.jamMulaiStr} - ${current.jamSelesaiStr}`, current.asistenName]);
                }
            });

        } else {
            const title = activeType === 'praktikum' ? "LAPORAN MASTER JADWAL PRAKTIKUM MATA KULIAH" : "LAPORAN MASTER DATA JADWAL (MIXED DISPLAY)";
            doc.text(title, 14, 15);
            pdfHeaders = [['Hari & Tanggal', 'Ruang Lab', 'Jam Praktikum (24 Jam)', 'Mata Kuliah', 'Kode Matkul', 'Dosen Pengampu', 'Asisten Jaga']];

            rawEntries.forEach(e => {
                pdfBody.push([e.hariTanggal, e.labName, `${e.jamMulaiStr} - ${e.jamSelesaiStr}`, e.matkul, e.kodeMatkul, e.dosen, e.asistenName]);
            });
        }

        doc.setFont("Helvetica", "normal"); doc.setFontSize(10);
        doc.text("Laboratorium Komputer Universitas Budi Luhur", 14, 21);

        doc.autoTable({
            head: pdfHeaders,
            body: pdfBody,
            startY: 26,
            theme: 'grid',
            headStyles: { fillColor: activeType === 'ra' ? [217, 119, 6] : [15, 23, 42], fontStyle: 'bold' },
            styles: { fontSize: 9, cellPadding: 3 },
            columnStyles: activeType === 'ra' ? {
                0: { cellWidth: 50 }, 1: { cellWidth: 45 }, 2: { cellWidth: 45 }, 3: { cellWidth: 130 }
            } : {
                0: { cellWidth: 32 }, 1: { cellWidth: 23 }, 2: { cellWidth: 28 }, 3: { cellWidth: 55 }, 4: { cellWidth: 22 }, 5: { cellWidth: 50 }, 6: { cellWidth: 35 }
            }
        });

        const filename = activeType === 'ra' ? 'Jadwal_Jaga_Ruang_RA.pdf' : (activeType === 'praktikum' ? 'Jadwal_Praktikum_Mata_Kuliah.pdf' : 'Jadwal_Master_Mix.pdf');
        doc.save(filename);
    });
});
</script>

<script>
let currentConfirmCallback = null;

function openScheduleDetailModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeScheduleDetailModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

document.addEventListener('click', function (event) {
    if (event.target.classList && event.target.classList.contains('fixed') && event.target.id.startsWith('schedule-detail-')) {
        closeScheduleDetailModal(event.target.id);
    }
});

function showCustomAlert(message, title = 'Perhatian!') {
    document.getElementById('custom-alert-title').innerText = title;
    document.getElementById('custom-alert-message').innerText = message;

    const modal = document.getElementById('custom-alert-modal');
    const box = document.getElementById('custom-alert-box');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        box.classList.remove('scale-95');
        box.classList.add('scale-100');
    }, 10);
}

function closeCustomAlert() {
    const modal = document.getElementById('custom-alert-modal');
    const box = document.getElementById('custom-alert-box');

    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    box.classList.remove('scale-100');
    box.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 300);
}

function showCustomConfirm(message, title, onConfirm) {
    document.getElementById('custom-confirm-title').innerText = title;
    document.getElementById('custom-confirm-message').innerText = message;
    currentConfirmCallback = onConfirm;

    const modal = document.getElementById('custom-confirm-modal');
    const box = document.getElementById('custom-confirm-box');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        box.classList.remove('scale-95');
        box.classList.add('scale-100');
    }, 10);

    document.getElementById('custom-confirm-yes-btn').onclick = function() {
        closeCustomConfirm();
        if (currentConfirmCallback) currentConfirmCallback();
    };
}

function closeCustomConfirm() {
    const modal = document.getElementById('custom-confirm-modal');
    const box = document.getElementById('custom-confirm-box');

    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    box.classList.remove('scale-100');
    box.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 300);
}

function toggleTambahJadwal() {
    const form = document.getElementById('form-tambah-jadwal');
    form.classList.toggle('hidden');
}

function cekTanggalSebelumPilihFile() {
    const tglMulai = document.getElementById('import_start_date').value;
    const tglSelesai = document.getElementById('import_end_date').value;
    if (!tglMulai || !tglSelesai) {
        showCustomAlert('Tolong tentukan tanggal Periode Generate (Mulai s/d Selesai) di sebelah kiri tombol dulu, Pak/Bu!', 'Pilih Periode');
        return;
    }
    document.getElementById('file_excel_cepat').click();
}

function triggerAutoSubmit() {
    const fileInput = document.getElementById('file_excel_cepat');
    const btnImport = document.getElementById('btn-import-xlsx');
    const form = document.getElementById('form-import-cepat');

    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const validExtensions = ['.xlsx', '.xls', '.csv'];
        const fileName = file.name.toLowerCase();

        const isValid = validExtensions.some(ext => fileName.endsWith(ext));

        if (!isValid) {
            showCustomAlert('File tidak valid! Tolong hanya masukkan file dengan format Excel (.xlsx, .xls, atau .csv).', 'Format Salah');
            fileInput.value = ''; // Kosongkan pilihan file agar user bisa memilih ulang
            return; // Batalkan proses submit
        }

        btnImport.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Men-generate...';
        btnImport.classList.add('opacity-60', 'pointer-events-none');
        form.submit();
    }
}

// Time Formatter logic
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('time-formatter')) {
        let inputVal = e.target.value.replace(/\D/g, '');
        if (inputVal.length > 4) inputVal = inputVal.substring(0, 4);
        let formatted = inputVal;
        if (inputVal.length > 2) {
            formatted = inputVal.substring(0, 2) + ':' + inputVal.substring(2, 4);
        }
        e.target.value = formatted;
    }
});

document.addEventListener('change', function (e) {
    if (e.target.classList.contains('time-formatter')) {
        let val = e.target.value;
        if (val.length === 5) {
            let parts = val.split(':');
            let hours = parseInt(parts[0], 10);
            let mins = parseInt(parts[1], 10);
            if (hours > 23) hours = 23;
            if (mins > 59) mins = 59;
            if (isNaN(hours)) hours = 0;
            if (isNaN(mins)) mins = 0;
            let hrStr = hours < 10 ? '0' + hours : hours;
            let mnStr = mins < 10 ? '0' + mins : mins;
            e.target.value = hrStr + ':' + mnStr;
            e.target.dispatchEvent(new Event('change'));
        } else if (val.length > 0 && val.length < 5) {
            showCustomAlert('Format jam tidak valid. Ketik 4 angka (contoh: 0800)', 'Format Jam');
            e.target.value = '';
        }
    }
});

function toggleRepeatDate() {
    const type = document.getElementById('repeat_type').value;
    const container = document.getElementById('end_date_container');
    const input = document.getElementById('tanggal_selesai');
    if (type !== 'single') {
        container.classList.remove('hidden');
        input.required = true;
    } else {
        container.classList.add('hidden');
        input.required = false;
        input.value = '';
    }
}

// AJAX Lab Availability Checker for SPV
document.addEventListener("DOMContentLoaded", function () {
    const inputTanggal = document.getElementById('input_tanggal');
    const repeatType = document.getElementById('repeat_type');
    const tanggalSelesai = document.getElementById('tanggal_selesai');
    const inputJam = document.getElementById('input_jam');
    const inputSks = document.getElementById('input_sks');
    const selectLab = document.getElementById('select_lab');
    const selectJamTemplate = document.getElementById('select_jam_template');

    const weekdaySlots = [
        { start: '07:10', label: '07:10' },
        { start: '08:00', label: '08:00' },
        { start: '08:55', label: '08:55' },
        { start: '09:45', label: '09:45' },
        { start: '10:40', label: '10:40' },
        { start: '11:35', label: '11:35' },
        { start: '12:30', label: '12:30' },
        { start: '13:25', label: '13:25' },
        { start: '14:20', label: '14:20' },
        { start: '15:15', label: '15:15' },
        { start: '16:10', label: '16:10' },
        { start: '17:05', label: '17:05' },
        { start: '18:00', label: '18:00' },
        { start: '18:55', label: '18:55' }
    ];

    const saturdaySlots = [
        { start: '08:00', label: '08:00' },
        { start: '10:00', label: '10:00' },
        { start: '13:00', label: '13:00' },
        { start: '15:00', label: '15:00' }
    ];

    function getDayOfWeek(dateStr) {
        if (!dateStr) return null;
        const parts = dateStr.split('-');
        if (parts.length !== 3) return null;
        const dateVal = new Date(parts[0], parts[1] - 1, parts[2]);
        return dateVal.getDay();
    }

    function updateJamTemplate() {
        if (!inputTanggal.value) {
            selectJamTemplate.innerHTML = '<option value="">-- Template (Pilih Tanggal Dahulu) --</option>';
            return;
        }
        const day = getDayOfWeek(inputTanggal.value);
        if (day === 0) { // Sunday
            selectJamTemplate.innerHTML = '<option value="">Minggu Libur</option>';
            showCustomAlert('Hari Minggu adalah hari libur. Tidak dapat melakukan penjadwalan.', 'Hari Libur');
            inputTanggal.value = '';
            return;
        }
        selectJamTemplate.innerHTML = '<option value="">-- Pilih Template --</option>';
        const slots = (day === 6) ? saturdaySlots : weekdaySlots;
        slots.forEach(slot => {
            const opt = document.createElement('option');
            opt.value = slot.start;
            opt.textContent = slot.label;
            selectJamTemplate.appendChild(opt);
        });
    }

    function updateSksLimits() {
        const dateVal = inputTanggal.value;
        const jamVal = inputJam.value;
        const day = getDayOfWeek(dateVal);

        if (!dateVal || !jamVal || jamVal.length !== 5) {
            inputSks.removeAttribute('max');
            inputSks.readOnly = false;
            return;
        }

        if (day === 0) { // Sunday
            inputSks.value = '';
            inputSks.readOnly = true;
            return;
        }

        if (day === 6) { // Saturday
            inputSks.readOnly = false;
            inputSks.min = 1;
            inputSks.max = 4;
            if (parseInt(inputSks.value) > 4) {
                inputSks.value = 4;
            }
            return;
        }

        // Jam reguler weekdays
        inputSks.readOnly = false;
        inputSks.min = 1;

        const weekdayStarts = ['07:10', '08:00', '08:55', '09:45', '10:40', '11:35', '12:30', '13:25', '14:20', '15:15', '16:10', '17:05', '18:00', '18:55', '19:50'];
        const idx = weekdayStarts.indexOf(jamVal);

        let maxSks = 4;
        if (idx !== -1) {
            maxSks = Math.min(4, 15 - idx);
        }
        inputSks.max = maxSks;
        if (parseInt(inputSks.value) > maxSks) {
            inputSks.value = maxSks;
        }
    }

    inputTanggal.addEventListener('change', function() {
        updateJamTemplate();
        updateSksLimits();
    });
    inputJam.addEventListener('change', updateSksLimits);
    inputJam.addEventListener('input', updateSksLimits);

    if (selectJamTemplate) {
        selectJamTemplate.addEventListener('change', function() {
            if (this.value) {
                inputJam.value = this.value;
                inputJam.dispatchEvent(new Event('input'));
                inputJam.dispatchEvent(new Event('change'));
            }
        });
    }

    const triggers = document.querySelectorAll('.trigger-ajax');
    let lastTriggerValues = '';

    triggers.forEach(el => {
        ['input', 'change'].forEach(evt => {
            el.addEventListener(evt, function() {
                const isWeeklyOrOther = repeatType.value !== 'single';
                const hasEndDateIfNeeded = !isWeeklyOrOther || tanggalSelesai.value;

                if (inputTanggal.value && inputJam.value.length === 5 && inputSks.value && hasEndDateIfNeeded) {
                    const currentValues = `${inputTanggal.value}-${repeatType.value}-${tanggalSelesai.value}-${inputJam.value}-${inputSks.value}`;
                    if (currentValues === lastTriggerValues) return;
                    lastTriggerValues = currentValues;

                    selectLab.disabled = true;
                    selectLab.innerHTML = '<option value="">⏳ Mencari Lab yang Sesuai...</option>';
                    selectLab.classList.replace('bg-white', 'bg-slate-200');

                    fetch('{{ route('spv.jadwal.check_labs_range') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            tanggal: inputTanggal.value,
                            repeat_type: repeatType.value,
                            tanggal_selesai: tanggalSelesai.value,
                            jam_mulai: inputJam.value,
                            sks: inputSks.value
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response error');
                        }
                        return response.json();
                    })
                    .then(data => {
                        selectLab.innerHTML = '<option value="">-- Pilih Laboratorium --</option>';
                        let adaKosong = false;

                        data.labs.forEach(lab => {
                            let option = document.createElement('option');
                            option.value = lab.id_lab;

                            if (lab.is_busy) {
                                option.disabled = true;
                                const datesStr = lab.conflict_dates.join(', ');
                                option.textContent = `🔴 ${lab.nama_lab} (Bentrok: ${datesStr})`;
                                option.style.color = '#ef4444';
                                option.style.fontWeight = 'bold';
                            } else {
                                option.textContent = `🟢 ${lab.nama_lab} (Tersedia)`;
                                option.style.color = '#10b981';
                                option.style.fontWeight = 'bold';
                                adaKosong = true;
                            }
                            selectLab.appendChild(option);
                        });

                        selectLab.disabled = false;
                        selectLab.classList.replace('bg-slate-200', 'bg-white');
                        selectLab.classList.remove('cursor-not-allowed');

                        if (!adaKosong) {
                            selectLab.innerHTML = '<option value="">⚠️ Semua Lab Penuh/Bentrok</option>';
                            selectLab.disabled = true;
                            selectLab.classList.replace('bg-white', 'bg-red-100');
                        }
                    })
                    .catch(error => {
                        console.error('AJAX Error:', error);
                        selectLab.innerHTML = '<option value="">❌ Terjadi Kesalahan (Cek Console)</option>';
                    });
                } else {
                    selectLab.disabled = true;
                    selectLab.innerHTML = '<option value="">Isi Info Tanggal, Waktu & SKS Dahulu</option>';
                    selectLab.classList.replace('bg-white', 'bg-slate-200');
                    selectLab.classList.remove('bg-red-100');
                }
            });
        });
    });
});
</script>

<script>
// ===== Multi-Asisten Dropdown Functions =====
function toggleMultiAsisten(btn) {
    // Tutup semua dropdown lain dulu
    document.querySelectorAll('.multi-asisten-dropdown').forEach(d => {
        if (d !== btn.nextElementSibling) {
            d.classList.add('hidden');
        }
    });
    const dropdown = btn.closest('.multi-asisten-wrapper').querySelector('.multi-asisten-dropdown');
    dropdown.classList.toggle('hidden');
}

function submitMultiAsisten(btn) {
    const wrapper = btn.closest('.multi-asisten-wrapper');
    const formId = wrapper.dataset.form;
    const scopeId = wrapper.dataset.scope;

    if (scopeId) {
        document.getElementById(scopeId).value = 'single';
    }
    document.getElementById(formId).submit();
}

function closeMultiAsisten(btn) {
    btn.closest('.multi-asisten-dropdown').classList.add('hidden');
}

// Tutup dropdown jika klik di luar
document.addEventListener('click', function(e) {
    if (!e.target.closest('.multi-asisten-wrapper')) {
        document.querySelectorAll('.multi-asisten-dropdown').forEach(d => {
            d.classList.add('hidden');
        });
    }
});
</script>
@endsection
</body>
</html>
