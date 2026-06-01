<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
   
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="{{ asset('js/spv-table.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Manajemen Jadwal - Lab ICT</title>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-blue-100 font-sans text-slate-800 antialiased">

@extends('layouts.spv')
@section('title', 'Manajemen Jadwal')

@section('content')
<div class="space-y-6">
   
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Manajemen Jadwal</h1>
        <p class="mt-1 text-sm font-medium text-slate-500">Kelola jadwal praktikum dan persetujuan peminjaman lab.</p>
    </div>

    
    @if(isset($conflicts) && $conflicts->count() > 0)
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5 shadow-xl shadow-red-950/5 space-y-4">
            <div class="flex items-center gap-3 text-red-800">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-100 text-red-600 animate-pulse">
                    <i class="fas fa-exclamation-triangle text-sm"></i>
                </span>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider">
                    <p class="text-xs font-semibold text-red-600">Ada beberapa kelas praktikum yang menempati ruang Lab yang sama pada jam operasional yang sama.</p>
                </div>
            </div>
            
            {{-- Tabel Isian Jadwal yang Tabrakan --}}
            <div class="overflow-x-auto rounded-xl border border-red-200 bg-white">
                <table class="w-full min-w-[800px] text-left text-xs">
                    <thead class="bg-red-600 text-white font-extrabold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Tanggal / Hari</th>
                            <th class="px-5 py-3">Ruang Lab</th>
                            <th class="px-5 py-3">Jam Praktikum</th>
                            <th class="px-5 py-3">Mata Kuliah</th>
                            <th class="px-5 py-3">Dosen Pengampu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-red-100 text-red-950 bg-red-50/30">
                        @foreach($conflicts as $c)
                            <tr class="hover:bg-red-100/40 transition font-bold">
                                <td class="px-5 py-3 text-slate-700">
                                    {{ \Carbon\Carbon::parse($c->tanggal)->format('d M Y') }} 
                                    <span class="text-[10px] bg-red-100 text-red-700 rounded p-0.5 px-1.5 ml-1">{{ strtoupper($c->hari) }}</span>
                                </td>
                                <td class="px-5 py-3 text-red-700 font-extrabold">
                                    🔥 {{ $c->lab->nama_lab ?? 'LAB TANPA NAMA' }}
                                </td>
                                <td class="px-5 py-3 tracking-wide text-amber-800">
                                    {{ substr($c->jam_mulai, 0, 5) }} - {{ substr($c->jam_selesai, 0, 5) }}
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
        
        <div class="flex flex-wrap items-center gap-3">
         
            <button id="btnCetakPDF" class="inline-flex h-11 items-center gap-2 rounded-xl bg-red-600 px-5 text-sm font-bold text-white shadow-md shadow-red-600/10 transition hover:bg-red-700">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </button>

            <button onclick="toggleTambahJadwal()" class="inline-flex h-11 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-md shadow-blue-600/10 transition hover:bg-blue-700">
                <i class="fas fa-plus-circle"></i> Tambah Jadwal Manual
            </button>

            <form action="{{ route('schedule.import') }}" method="POST" enctype="multipart/form-data" id="form-import-cepat" 
                  class="m-0 flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/60 p-1 px-3">
                @csrf
                <span class="text-[10px] font-extrabold tracking-wider text-slate-400 uppercase">Periode:</span>
                
                <input type="date" name="start_date" id="import_start_date" required 
                       class="rounded-lg border border-slate-200 bg-white p-1.5 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500">
                <span class="text-xs font-bold text-slate-400">s/d</span>
                <input type="date" name="end_date" id="import_end_date" required 
                       class="rounded-lg border border-slate-200 bg-white p-1.5 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500">
                
                <input type="file" name="file_excel" id="file_excel_cepat" accept=".xlsx, .xls, .csv" class="hidden" onchange="triggerAutoSubmit()">
                
                <button type="button" id="btn-import-xlsx" onclick="cekTanggalSebelumPilihFile()" 
                        class="inline-flex h-8 items-center rounded-lg bg-emerald-600 px-3 text-xs font-extrabold text-white shadow-sm transition hover:bg-emerald-700">
                    <i class="fas fa-file-excel mr-1.5"></i> IMPORT XLSX
                </button>
            </form>
            
            <select id="filterDay" class="h-11 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 cursor-pointer">
                <option value="">All Days</option>
                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                    <option value="{{ $h }}">{{ $h }}</option>
                @endforeach
            </select>

            <select id="filterLab" class="h-11 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 cursor-pointer">
                <option value="">All Labs</option>
                @for($i=1; $i<=11; $i++)
                    @php $formatLab = 'LAB ' . sprintf('%02d', $i); @endphp
                    <option value="{{ $formatLab }}">{{ $formatLab }}</option>
                @endfor
            </select>
        </div>
    </div>
                    
 
    <div id="form-tambah-jadwal" class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-blue-950/5 transition-all">
        <div class="bg-slate-50 border-b border-slate-100 px-6 py-4">
            <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Form Tambah Jadwal Manual</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('spv.store') }}" method="POST" class="grid gap-4 sm:grid-cols-2">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Tanggal</label>
                    <input type="date" name="tanggal" required class="h-11 w-full rounded-xl border border-slate-200 px-4 text-sm font-medium text-slate-700 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Ruang Lab</label>
                    <select name="id_lab" required class="h-11 w-full rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700 outline-none focus:border-blue-500 cursor-pointer">
                        <option value="">-- Pilih Lab --</option>
                        @foreach($labs as $lab)
                            <option value="{{ $lab->id_lab }}">{{ $lab->nama_lab }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Jam Mulai</label>
                    <input type="time" name="jam_mulai" required class="h-11 w-full rounded-xl border border-slate-200 px-4 text-sm font-medium text-slate-700 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Jumlah SKS</label>
                    <input type="number" name="sks" required min="1" max="6" placeholder="Contoh: 2"
                           class="h-11 w-full rounded-xl border border-slate-200 px-4 text-sm font-medium text-slate-700 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Mata Kuliah</label>
                    <input type="text" name="matkul" required placeholder="Nama Matkul"
                           class="h-11 w-full rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Nama Dosen</label>
                    <input type="text" name="dosen" required placeholder="Nama Dosen"
                           class="h-11 w-full rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700 outline-none focus:border-blue-500">
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
            <input type="date" name="filter_date" value="{{ request('filter_date', now()->toDateString()) }}" onchange="this.form.submit()" 
                   class="h-9 rounded-lg border border-slate-200 px-3 text-xs font-bold text-slate-700 outline-none focus:border-blue-500">
            @if(request('filter_date'))
                <a href="{{ route('spv.jadwal') }}" class="text-xs font-bold text-red-500 hover:underline">[Reset Filter]</a>
            @endif
        </form>
    </div>
                    
  
    <div class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-wide">
        <span>Tampilkan</span>
        <select class="limitSelect h-8 rounded-lg border border-slate-200 bg-white px-2 text-xs font-extrabold text-slate-700 outline-none cursor-pointer">
            <option value="5" selected>5</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
        <span>Data</span>
    </div>

    <div class="overflow-hidden rounded-2xl border border-white bg-white shadow-2xl shadow-blue-950/5">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px] border-collapse text-left text-sm" id="scheduleTable">
                <thead class="bg-slate-900 text-white text-xs font-extrabold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4 w-52">Lab</th>
                        <th class="px-6 py-4 w-60">Jam (Mulai - Selesai)</th>
                        <th class="px-6 py-4">Mata Kuliah</th>
                        <th class="px-6 py-4">Dosen</th>
                        <th class="px-6 py-4 w-56">Asisten</th>
                        <th class="px-6 py-4 text-right w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($schedules as $s)
                    <tr data-hari="{{ $s->hari }}" data-lab="{{ $s->lab->nama_lab ?? '' }}" class="transition hover:bg-slate-50/60">
                    
                        <td class="px-6 py-3.5">
                            <form action="{{ route('spv.update', $s->id_jadwal) }}" method="POST" id="update-form-{{ $s->id_jadwal }}" class="hidden">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="update_scope" id="scope-{{ $s->id_jadwal }}" value="today_only">
                            </form>

                            <div class="text-[10px] font-extrabold tracking-wider text-blue-600 uppercase mb-1">
                                {{ $s->hari }}
                            </div>
                            <input type="date" name="tanggal" value="{{ \Carbon\Carbon::parse($s->tanggal)->format('Y-m-d') }}" 
                                   class="h-9 rounded-lg border border-slate-200 px-2.5 text-xs font-medium text-slate-700 outline-none focus:border-blue-500 focus:bg-blue-50/20" 
                                   form="update-form-{{ $s->id_jadwal }}" 
                                   onchange="document.getElementById('scope-{{ $s->id_jadwal }}').value='today_only'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
                        </td>

                   
                        <td class="px-4 py-3.5">
                            <select name="id_lab" class="h-9 w-full rounded-lg border border-slate-200 px-2 text-xs font-bold text-slate-700 outline-none focus:border-blue-500 cursor-pointer" 
                                    form="update-form-{{ $s->id_jadwal }}" 
                                    onchange="document.getElementById('scope-{{ $s->id_jadwal }}').value='today_only'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
                                @foreach($s->getLabStatuses() as $lab)
                                    @php
                                        $labJadwalSaatIni = $s->id_lab;
                                        $labDalamLoop = $lab['id_lab'];
                                        $apakahLabSendiri = ($labJadwalSaatIni == $labDalamLoop);
                                    @endphp

                                    @if($apakahLabSendiri)
                                        <option value="{{ $lab['id_lab'] }}" selected class="font-extrabold text-emerald-600 bg-emerald-50">
                                            {{ $lab['nama_lab'] }} (Aktif)
                                        </option>
                                    @elseif($lab['status'] === 'busy')
                                        <option value="" disabled class="text-red-500 bg-red-50 cursor-not-allowed">
                                            {{ $lab['nama_lab'] }} (Dipakai)
                                        </option>
                                    @else
                                        <option value="{{ $lab['id_lab'] }}" class="text-slate-700">
                                            {{ $lab['nama_lab'] }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </td>

                        {{-- Kolom Jam --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1.5">
                                <input type="time" name="jam_mulai" value="{{ $s->jam_mulai }}" 
                                       class="h-9 w-24 rounded-lg border border-slate-200 px-2 text-center text-xs font-semibold text-slate-700 outline-none focus:border-blue-500" 
                                       form="update-form-{{ $s->id_jadwal }}">
                                <span class="text-slate-400 font-bold text-xs">-</span>
                                <input type="time" name="jam_selesai" value="{{ $s->jam_selesai }}" 
                                       class="h-9 w-24 rounded-lg border border-slate-200 px-2 text-center text-xs font-semibold text-slate-700 outline-none focus:border-blue-500" 
                                       form="update-form-{{ $s->id_jadwal }}">
                            </div>
                        </td>

                        <td class="px-4 py-3.5">
                            <input type="text" name="matkul" value="{{ $s->matkul }}" 
                                   class="h-9 w-full rounded-lg border border-slate-200 px-3 text-xs font-bold text-slate-700 outline-none focus:border-blue-500 focus:bg-blue-50/20" 
                                   form="update-form-{{ $s->id_jadwal }}">
                        </td>

                   
                        <td class="px-4 py-3.5">
                            <input type="text" name="dosen" value="{{ $s->dosen }}" 
                                   class="h-9 w-full rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-600 outline-none focus:border-blue-500 focus:bg-blue-50/20" 
                                   form="update-form-{{ $s->id_jadwal }}">
                        </td>

                     
                        <td class="px-4 py-3.5">
                            <select name="id_asisten" class="h-9 w-full rounded-lg border border-slate-200 px-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500 cursor-pointer" 
                                    form="update-form-{{ $s->id_jadwal }}" 
                                    onchange="document.getElementById('scope-{{ $s->id_jadwal }}').value='today_only'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
                                <option value="">-- Pilih Asisten --</option>
                                @foreach($s->getAssistantStatuses() as $asisten)
                                    @if($asisten->is_busy)
                                        @if($s->id_asisten == $asisten->id_asisten)
                                            <option value="{{ $asisten->id_asisten }}" selected class="font-bold text-red-600 bg-red-50">
                                                {{ $asisten->nama }} {{ $asisten->label }}
                                            </option>
                                        @else
                                            <option value="" disabled class="text-red-400 bg-red-50/50 cursor-not-allowed">
                                                {{ $asisten->nama }} {{ $asisten->label }}
                                            </option>
                                        @endif
                                    @else
                                        <option value="{{ $asisten->id_asisten }}" {{ $s->id_asisten == $asisten->id_asisten ? 'selected' : '' }}>
                                            {{ $asisten->nama }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </td>

                      
                        <td class="px-6 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                              
                                <button type="button" title="Simpan Perubahan Seterusnya" 
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 shadow-sm transition hover:bg-blue-100"
                                        onclick="document.getElementById('scope-{{ $s->id_jadwal }}').value='all'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
                                    <i class="fas fa-save text-xs"></i>
                                </button>

                              
                                <form method="POST" action="{{ route('spv.delete', $s->id_jadwal) }}" class="inline m-0" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex h-8 items-center rounded-lg bg-red-50 px-3 text-xs font-bold text-red-600 shadow-sm transition hover:bg-red-100">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="noDataMessage" class="hidden flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">
        <i class="fas fa-calendar-times text-3xl mb-2 text-slate-300"></i>
        <p class="text-sm font-semibold">Tidak ada jadwal yang cocok.</p>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script>
function toggleTambahJadwal() {
    const form = document.getElementById('form-tambah-jadwal');
    form.classList.toggle('hidden');
}

function cekTanggalSebelumPilihFile() {
    const tglMulai = document.getElementById('import_start_date').value;
    const tglSelesai = document.getElementById('import_end_date').value;
    if (!tglMulai || !tglSelesai) {
        alert('⚠️ Tolong tentukan tanggal Periode Generate (Mulai s/d Selesai) di sebelah kiri tombol dulu, Pak/Bu!');
        return;
    }
    document.getElementById('file_excel_cepat').click();
}

function triggerAutoSubmit() {
    const fileInput = document.getElementById('file_excel_cepat');
    const btnImport = document.getElementById('btn-import-xlsx');
    const form = document.getElementById('form-import-cepat');

    if (fileInput.files.length > 0) {
        btnImport.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Men-generate...';
        btnImport.classList.add('opacity-60', 'pointer-events-none');
        form.submit();
    }
}
</script>
@endsection
</body>
</html>