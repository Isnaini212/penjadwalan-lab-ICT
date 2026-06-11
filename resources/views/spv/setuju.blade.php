@extends('layouts.spv') {{-- Pastikan layout lu udah support Tailwind --}}

@section('title', 'Persetujuan Booking')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Persetujuan Booking Ruangan</h1>
    <p class="text-slate-500 font-medium mt-1">Kelola permintaan peminjaman lab dari Ormawa dan Dosen/Staf.</p>
</div>

{{-- KARTU STATISTIK --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-xl shadow-slate-200/40 border-l-4 border-amber-500 flex flex-col">
        <span class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pending</span>
        <span class="text-3xl font-black text-slate-800">{{ count($bookings) }}</span>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-xl shadow-slate-200/40 border-l-4 border-indigo-500 flex flex-col">
        <span class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Menunggu (Ormawa)</span>
        <span class="text-3xl font-black text-indigo-600">{{ $totalOrmawa }}</span>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-xl shadow-slate-200/40 border-l-4 border-emerald-500 flex flex-col">
        <span class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Menunggu (Dosen)</span>
        <span class="text-3xl font-black text-emerald-600">{{ $totalDosen }}</span>
    </div>
</div>

{{-- ALERT --}}
@if(session('success'))
    <div class="mb-6 rounded-xl bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 border border-emerald-200 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-500 text-lg"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-6 rounded-xl bg-red-50 px-5 py-4 text-sm font-bold text-red-700 border border-red-200 flex items-center gap-3">
        <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i> {{ session('error') }}
    </div>
@endif

{{-- TABEL DATA --}}
<div class="rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-200/40 overflow-hidden">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-blue-900 text-white font-extrabold uppercase tracking-wider text-xs">
                <tr>
                    <th class="px-6 py-4">Pengaju & Kontak</th>
                    <th class="px-4 py-4 text-center">Tipe Identitas</th>
                    <th class="px-4 py-4">Waktu Pelaksanaan</th>
                    <th class="px-4 py-4">Kapasitas & Keperluan</th>
                    <th class="px-4 py-4">Penempatan Lab</th>
                    <th class="px-4 py-4 text-center">Dokumen</th>
                    <th class="px-6 py-4 text-center">Aksi (Validasi)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($bookings as $b)
                <tr class="hover:bg-slate-50 transition">

                    {{-- Pengaju --}}
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800 text-base">{{ $b->nama_pengaju }}</div>
                        <div class="text-xs font-semibold text-slate-500 mt-1">
                            <i class="fab fa-whatsapp text-emerald-500 mr-1"></i> {{ $b->kontak }}
                        </div>
                    </td>

                    {{-- Tipe Identitas --}}
                    <td class="px-4 py-4 text-center">
                        @if($b->type === 'ormawa')
                            <span class="inline-flex rounded-lg bg-indigo-100 px-3 py-1 text-[11px] font-black uppercase text-indigo-700 border border-indigo-200">
                                {{ $b->identitas }}
                            </span>
                        @else
                            <span class="inline-flex rounded-lg bg-slate-100 px-3 py-1 text-[11px] font-black uppercase text-slate-600 border border-slate-200">
                                <i class="fas fa-chalkboard-teacher mr-1"></i> {{ $b->identitas }}
                            </span>
                        @endif
                    </td>

                    {{-- Waktu --}}
                    <td class="px-4 py-4">
                        <div class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($b->tanggal)->translatedFormat('d M Y') }}</div>
                        <div class="text-xs font-mono font-bold text-indigo-600 mt-1 tracking-widest">
                            {{ substr($b->jam_mulai, 0, 5) }} - {{ substr($b->jam_selesai, 0, 5) }}
                        </div>
                    </td>

                    {{-- Keperluan --}}
                    <td class="px-4 py-4 whitespace-normal min-w-[200px]">
                        <div class="font-bold text-slate-700 text-xs mb-1">
                            <i class="fas fa-users text-slate-400 mr-1"></i> {{ $b->kapasitas }} Orang
                        </div>
                        <div class="text-sm font-semibold text-slate-600">{{ $b->keperluan }}</div>
                    </td>

                    {{-- Dropdown Pilih LAB --}}
                    <td class="px-4 py-4">
                        <form action="{{ route('spv.booking.update_lab', ['type' => $b->type, 'id' => $b->id_booking]) }}" method="POST">
                            @csrf @method('PATCH')
                            <select name="lab_id" onchange="this.form.submit()" class="h-10 w-48 rounded-xl border border-slate-300 bg-white px-2 text-xs font-bold text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 cursor-pointer">

                                @if($b->current_lab === 'TBD' || empty($b->current_id_lab))
                                    <option value="" selected disabled class="font-bold text-red-500">
                                        -- Pilih Lab (Wajib) --
                                    </option>
                                @endif

                                @foreach($b->lab_options as $opt)
                                    @continue(! str_contains(strtoupper($opt['nama_lab']), 'LAB'))

                                    @php
                                        // Pengecekan agar lab yang sedang dipilih saat ini langsung aktif
                                        $isSelected = ($b->current_id_lab == $opt['id_lab'] || $b->current_lab == $opt['nama_lab']);
                                    @endphp

                                    @if($opt['is_busy'])
                                        {{-- JIKA LAB KEPAKE: Kunci dan warnai merah --}}
                                        <option value="" disabled class="bg-red-50 font-bold text-red-500">
                                            {{ $opt['nama_lab'] }} (Kepakai)
                                        </option>
                                    @else
                                        {{-- JIKA LAB KOSONG: Biarkan bisa dipilih dan warnai hijau --}}
                                        <option value="{{ $opt['id_lab'] }}" {{ $isSelected ? 'selected' : '' }} class="font-bold text-emerald-700">
                                            ✅ {{ $opt['nama_lab'] }} (Tersedia)
                                        </option>
                                    @endif
                                @endforeach
                                
                            </select>
                        </form>
                    </td>
                    {{-- Dokumen PDF --}}
                    {{-- Dokumen PDF --}}
{{-- Dokumen PDF --}}
<td class="px-4 py-4 text-center">
    @if($b->file_surat)
        <a href="{{ asset('surat_ormawa/' . $b->file_surat) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-500 hover:text-white border border-red-200">
            <i class="fas fa-file-pdf"></i> Buka PDF
        </a>
    @else
        <span class="text-xs font-bold text-slate-300">- N/A -</span>
    @endif
</td>

                    {{-- Aksi --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            {{-- Tombol Approve --}}
                            <form method="POST" action="{{ route('spv.booking.approve', ['type' => $b->type, 'id' => $b->id_booking]) }}">
                                @csrf
                                <button type="submit" class="inline-flex h-9 items-center justify-center rounded-xl bg-emerald-500 px-4 text-xs font-black uppercase text-white shadow-md shadow-emerald-500/30 transition hover:bg-emerald-600 hover:-translate-y-0.5">
                                    <i class="fas fa-check mr-1.5"></i> Setujui
                                </button>
                            </form>

                            {{-- Tombol Reject --}}
                            <form method="POST" action="{{ route('spv.booking.reject', ['type' => $b->type, 'id' => $b->id_booking]) }}" onsubmit="return confirm('Tolak pengajuan peminjaman ini?')">
                                @csrf
                                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-100 text-red-600 transition hover:bg-red-500 hover:text-white border border-red-200">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-slate-400">
                        <i class="fas fa-coffee text-5xl mb-4 text-slate-300"></i><br>
                        <span class="font-bold text-lg">Semua Bersih!</span><br>
                        <span class="text-sm font-medium mt-1">Belum ada pengajuan booking yang perlu di-review.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
