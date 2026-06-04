@extends('layouts.spv')

@section('title', 'Manajemen Jadwal')

@section('content')
<script defer src="{{ asset('js/spv-table.js') }}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
                                    <span class="text-[10px] bg-red-100 text-red-700 rounded p-0.5 px-1.5 ml-1">{{ strtoupper($c->hari) }}</span>
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
            
            <button onclick="toggleTambahJadwal()" class="inline-flex h-11 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-md shadow-blue-600/10 transition hover:bg-blue-700">
                <i class="fas fa-plus-circle"></i> Tambah Jadwal Manual
            </button>

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
            
            <form action="{{ route('bersih') }}" method="POST" class="m-0 inline-flex"
                  onsubmit="return confirm('PERINGATAN KERAS!\n\nApakah Anda yakin ingin MENGHAPUS SEMUA JADWAL yang ada di dalam database?\nTindakan ini permanen dan data tidak dapat dikembalikan.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex h-11 items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 text-sm font-bold text-red-600 shadow-sm transition hover:bg-red-600 hover:text-white hover:border-red-600">
                    <i class="fas fa-trash-alt"></i> Kosongkan Jadwal
                </button>
            </form>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <select id="filterType" class="h-11 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 cursor-pointer">
                <option value="all">Semua Jadwal (MIX)</option>
                <option value="praktikum">Jadwal Matkul Saja</option>
                <option value="ra">Jaga RA Saja</option>
            </select>

            <select id="filterDay" class="h-11 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 cursor-pointer">
                <option value="">Semua Hari</option>
                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                    <option value="{{ $h }}">{{ $h }}</option>
                @endforeach
            </select>

            <select id="filterLab" class="h-11 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 cursor-pointer">
                <option value="">Semua Lab / Ruangan</option>
                <option value="RA">RUANG RA</option>
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
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Jam Mulai (Format 24 Jam)</label>
                    <input type="time" name="jam_mulai" required class="h-11 w-full rounded-xl border border-slate-200 px-4 text-sm font-medium text-slate-700 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Jumlah SKS</label>
                    <input type="number" name="sks" required min="1" max="6" placeholder="Contoh: 2" class="h-11 w-full rounded-xl border border-slate-200 px-4 text-sm font-medium text-slate-700 outline-none focus:border-blue-500">
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
                <a href="{{ route('spv.jadwal') }}" class="text-xs font-bold text-red-500 hover:underline">[Reset Filter Hari]</a>
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
                    <tr data-hari="{{ $s->hari }}" class="transition hover:bg-slate-50/60">
                        <td class="px-6 py-3.5">
                            <form action="{{ route('spv.update', $s->id_jadwal) }}" method="POST" id="update-form-{{ $s->id_jadwal }}" class="hidden">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="scope" id="scope-field-{{ $s->id_jadwal }}" value="single">
                            </form>
                            <div class="text-[10px] font-extrabold tracking-wider text-blue-600 uppercase mb-1">
                                {{ $s->hari }}
                            </div>
                            <input type="date" name="tanggal" value="{{ \Carbon\Carbon::parse($s->tanggal)->format('Y-m-d') }}" class="h-9 rounded-lg border border-slate-200 px-2.5 text-xs font-medium text-slate-700 outline-none focus:border-blue-500 focus:bg-blue-50/20" form="update-form-{{ $s->id_jadwal }}" onchange="document.getElementById('scope-field-{{ $s->id_jadwal }}').value='single'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
                        </td>

                        <td class="px-4 py-3.5">
                            <select name="id_lab" class="h-9 w-full rounded-lg border border-slate-200 px-2 text-xs font-bold text-slate-700 outline-none focus:border-blue-500 cursor-pointer" form="update-form-{{ $s->id_jadwal }}" onchange="document.getElementById('scope-field-{{ $s->id_jadwal }}').value='single'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
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
                        </td>

                        <td class="px-4 py-3.5">
    <div class="flex items-center gap-1.5 font-mono">
        <!-- Input Jam Mulai -->
        <input type="text" name="jam_mulai" value="{{ date('H:i', strtotime($s->jam_mulai)) }}" placeholder="00:00" maxlength="5" class="time-formatter h-9 w-20 rounded-lg border border-slate-200 px-2 text-center text-xs font-bold text-slate-700 tracking-widest outline-none focus:border-blue-500" form="update-form-{{ $s->id_jadwal }}" onchange="document.getElementById('scope-field-{{ $s->id_jadwal }}').value='single'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
        
        <span class="text-slate-400 font-bold text-xs">-</span>
        
        <!-- Input Jam Selesai -->
        <input type="text" name="jam_selesai" value="{{ date('H:i', strtotime($s->jam_selesai)) }}" placeholder="00:00" maxlength="5" class="time-formatter h-9 w-20 rounded-lg border border-slate-200 px-2 text-center text-xs font-bold text-slate-700 tracking-widest outline-none focus:border-blue-500" form="update-form-{{ $s->id_jadwal }}" onchange="document.getElementById('scope-field-{{ $s->id_jadwal }}').value='single'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
    </div>
</td>

                        <td class="px-4 py-3.5">
                            <input type="text" name="matkul" value="{{ $s->matkul }}" class="h-9 w-full rounded-lg border border-slate-200 px-3 text-xs font-bold text-slate-700 outline-none focus:border-blue-500 focus:bg-blue-50/20" form="update-form-{{ $s->id_jadwal }}" onchange="document.getElementById('scope-field-{{ $s->id_jadwal }}').value='single'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
                        </td>

                        <td class="px-4 py-3.5">
                            <input type="text" name="dosen" value="{{ $s->dosen }}" class="h-9 w-full rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-600 outline-none focus:border-blue-500 focus:bg-blue-50/20" form="update-form-{{ $s->id_jadwal }}" onchange="document.getElementById('scope-field-{{ $s->id_jadwal }}').value='single'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
                        </td>

                        <td class="px-4 py-3.5">
                            <select name="id_asisten" class="h-9 w-full rounded-lg border border-slate-200 px-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500 cursor-pointer" form="update-form-{{ $s->id_jadwal }}" onchange="document.getElementById('scope-field-{{ $s->id_jadwal }}').value='single'; document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
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
                                <button type="button" title="Simpan Perubahan Seterusnya (Minggu-Minggu Berikutnya)" 
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 shadow-sm transition hover:bg-blue-100" 
                                        onclick="
                                            if (confirm('Apakah Anda ingin menerapkan perubahan baris ini ke semua jadwal yang sama di minggu-minggu berikutnya?')) {
                                                document.getElementById('scope-field-{{ $s->id_jadwal }}').value = 'all';
                                                document.getElementById('update-form-{{ $s->id_jadwal }}').submit();
                                            }
                                         Sunda">
                                    <i class="fas fa-layer-group text-xs"></i>
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
document.addEventListener("DOMContentLoaded", function () {
    const filterDay = document.getElementById('filterDay');
    const filterLab = document.getElementById('filterLab');
    const filterType = document.getElementById('filterType');
    const limitSelect = document.querySelector('.limitSelect');
    const tableBody = document.querySelector('#scheduleTable tbody');
    const rows = Array.from(tableBody.querySelectorAll('tr'));
    const noDataMessage = document.getElementById('noDataMessage');

    let currentPage = 1;

    // Fungsi Konversi Waktu Menit Strict 24 Jam
    function hitungTotalMenit(timeStr) {
        if (!timeStr) return 0;
        // Bersihkan spasi atau embel-embel AM/PM liar jika tidak sengaja ter-render oleh browser device tertentu
        let cleanTime = timeStr.replace(/[^\d:]/g, '').trim();
        const parts = cleanTime.split(':');
        return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
    }

    function jalankanLiveFiltering() {
        const selectedDay = filterDay.value;
        const selectedLab = filterLab.value;
        const selectedType = filterType.value;
        const limit = parseInt(limitSelect.value, 10);

        let filteredRows = rows.filter(tr => {
            const hariAttribute = tr.getAttribute('data-hari');
            const labSelect = tr.cells[1].querySelector('select');
            let labName = labSelect ? labSelect.options[labSelect.selectedIndex].text : '';
            labName = labName.replace(/[🟢|🔒]/g, '').replace('(Aktif)', '').replace('(Dipakai)', '').trim();

            const matchDay = !selectedDay || hariAttribute === selectedDay;
            const matchLab = !selectedLab || labName.toUpperCase().includes(selectedLab.toUpperCase());
            
            let matchType = true;
            if (selectedType === 'praktikum') {
                matchType = !labName.toUpperCase().includes('RA');
            } else if (selectedType === 'ra') {
                matchType = labName.toUpperCase().includes('RA');
            }

            return matchDay && matchLab && matchType;
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

        rows.forEach(tr => tr.style.display = 'none');
        filteredRows.slice(startIdx, endIdx).forEach(tr => tr.style.display = '');

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

    filterDay.addEventListener('change', () => { currentPage = 1; jalankanLiveFiltering(); });
    filterLab.addEventListener('change', () => { currentPage = 1; jalankanLiveFiltering(); });
    filterType.addEventListener('change', () => { currentPage = 1; jalankanLiveFiltering(); });
    limitSelect.addEventListener('change', () => { currentPage = 1; jalankanLiveFiltering(); });

    jalankanLiveFiltering();

    // SAKLAR CETAK PDF DENGAN FORMAT AMAN 24 JAM
    document.getElementById('btnCetakPDF')?.addEventListener('click', function() {
        const activeType = filterType.value;
        const selectedDay = filterDay.value;
        const selectedLab = filterLab.value;

        const rawEntries = [];
        rows.forEach(tr => {
            const hariAttribute = tr.getAttribute('data-hari');
            const inputTgl = tr.cells[0].querySelector('input[type="date"]');
            if (!inputTgl) return;

            const labSelect = tr.cells[1].querySelector('select');
            let labName = labSelect ? labSelect.options[labSelect.selectedIndex].text : '';
            labName = labName.replace(/[🟢|🔒]/g, '').replace('(Aktif)', '').replace('(Dipakai)', '').trim();

            const matchDay = !selectedDay || hariAttribute === selectedDay;
            const matchLab = !selectedLab || labName.toUpperCase().includes(selectedLab.toUpperCase());
            let matchType = true;
            if (activeType === 'praktikum') matchType = !labName.toUpperCase().includes('RA');
            if (activeType === 'ra') matchType = labName.toUpperCase().includes('RA');

            if (matchDay && matchLab && matchType) {
                const jamInputs = tr.cells[2].querySelectorAll('input[type="time"]');
                const asistenSelect = tr.cells[5].querySelector('select');
                let asistenName = asistenSelect ? asistenSelect.options[asistenSelect.selectedIndex].text : '-';
                asistenName = asistenName.replace(/[⚠️|🔒]/g, '').replace('-- Pilih Asisten --', '-').trim();

                // Bersihkan teks string jam agar konsisten 5 karakter HH:MM format 24 jam
                let jMulaiClean = jamInputs[0].value.substring(0, 5);
                let jSelesaiClean = jamInputs[1].value.substring(0, 5);

                rawEntries.push({
                    hariTanggal: hariAttribute + ', ' + inputTgl.value,
                    labName: labName,
                    jamMulaiStr: jMulaiClean,
                    jamSelesaiStr: jSelesaiClean,
                    menitMulai: hitungTotalMenit(jMulaiClean),
                    menitSelesai: hitungTotalMenit(jSelesaiClean),
                    matkul: tr.cells[3].querySelector('input[type="text"]').value,
                    dosen: tr.cells[4].querySelector('input[type="text"]').value,
                    asistenName: asistenName
                });
            }
        });

        if (rawEntries.length === 0) {
            alert("⚠️ Tidak ada data aktif sesuai filter saat ini untuk dicetak!");
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
            pdfHeaders = [['Hari & Tanggal', 'Ruang Lab', 'Jam Praktikum (24 Jam)', 'Mata Kuliah', 'Dosen Pengampu', 'Asisten Jaga']];

            rawEntries.forEach(e => {
                pdfBody.push([e.hariTanggal, e.labName, `${e.jamMulaiStr} - ${e.jamSelesaiStr}`, e.matkul, e.dosen, e.asistenName]);
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
                0: { cellWidth: 35 }, 1: { cellWidth: 25 }, 2: { cellWidth: 30 }, 3: { cellWidth: 65 }, 4: { cellWidth: 55 }, 5: { cellWidth: 40 }
            }
        });

        const filename = activeType === 'ra' ? 'Jadwal_Jaga_Ruang_RA.pdf' : (activeType === 'praktikum' ? 'Jadwal_Praktikum_Mata_Kuliah.pdf' : 'Jadwal_Master_Mix.pdf');
        doc.save(filename);
    });
});
</script>

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
<script>
    // 🔥 MESIN AUTO-FORMAT JAM (ANTI AM/PM DEVICE LIAR)
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('time-formatter')) {
        // 1. Buang semua huruf/simbol, cuma sisakan angka
        let inputVal = e.target.value.replace(/\D/g, ''); 
        if (inputVal.length > 4) inputVal = inputVal.substring(0, 4);
        
        // 2. Otomatis sisipkan titik dua (:) kalau digit sudah lebih dari 2
        let formatted = inputVal;
        if (inputVal.length > 2) {
            formatted = inputVal.substring(0, 2) + ':' + inputVal.substring(2, 4);
        }
        
        e.target.value = formatted;
    }
});

// 🔥 VALIDASI MAX 23:59 SAAT PINDAH KOLOM (BLUR)
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('time-formatter')) {
        let val = e.target.value;
        
        // Cek jika panjangnya pas 5 karakter (HH:MM)
        if (val.length === 5) {
            let parts = val.split(':');
            let hours = parseInt(parts[0], 10);
            let mins = parseInt(parts[1], 10);
            
            // Koreksi otomatis jika ngawur (misal ngetik 25:80 -> jadi 23:59)
            if (hours > 23) hours = 23;
            if (mins > 59) mins = 59;
            if (isNaN(hours)) hours = 0;
            if (isNaN(mins)) mins = 0;
            
            // Format ulang jadi dua digit
            let hrStr = hours < 10 ? '0' + hours : hours;
            let mnStr = mins < 10 ? '0' + mins : mins;
            
            e.target.value = hrStr + ':' + mnStr;
        } else if (val.length > 0 && val.length < 5) {
            // Kalau ngetiknya nanggung, reset/kosongin aja
            alert('Format jam tidak lengkap! Harap masukkan 4 digit angka (contoh: 0800).');
            e.target.value = '';
        }
    }
});
</script>

@endsection