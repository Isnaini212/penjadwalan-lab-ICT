@extends('layouts.spv')

@section('title', 'Persetujuan Booking')

@section('content')
<div class="mb-6">
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Persetujuan Booking Ruangan</h1>
    <p class="text-slate-500 font-medium mt-1 text-sm">Kelola permintaan peminjaman lab dari Ormawa dan Dosen/Staf.</p>
</div>

{{-- KARTU STATISTIK --}}
<div class="grid grid-cols-3 gap-3 sm:gap-6 mb-6">
    <div class="bg-white p-3 sm:p-6 rounded-2xl shadow-xl shadow-slate-200/40 border-l-4 border-amber-500 flex flex-col">
        <span class="text-[10px] sm:text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pending</span>
        <span class="text-2xl sm:text-3xl font-black text-slate-800">{{ count($bookings) }}</span>
    </div>
    <div class="bg-white p-3 sm:p-6 rounded-2xl shadow-xl shadow-slate-200/40 border-l-4 border-indigo-500 flex flex-col">
        <span class="text-[10px] sm:text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Ormawa</span>
        <span class="text-2xl sm:text-3xl font-black text-indigo-600">{{ $totalOrmawa }}</span>
    </div>
    <div class="bg-white p-3 sm:p-6 rounded-2xl shadow-xl shadow-slate-200/40 border-l-4 border-emerald-500 flex flex-col">
        <span class="text-[10px] sm:text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Dosen</span>
        <span class="text-2xl sm:text-3xl font-black text-emerald-600">{{ $totalDosen }}</span>
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

{{-- TABEL DATA PENDING --}}
<div class="rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-200/40 overflow-hidden">
    <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-4">
        <h2 class="text-sm font-black uppercase tracking-wide text-slate-800">Pengajuan Menunggu Persetujuan</h2>
        <p class="mt-1 text-xs font-semibold text-slate-500">Klik detail untuk melihat kapasitas, keperluan, penempatan lab, dokumen, dan validasi.</p>
    </div>

    <div class="divide-y divide-slate-100 md:hidden">
        @forelse($bookings as $b)
            <article class="p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-black text-slate-800">{{ $b->nama_pengaju }}</p>
                        <p class="mt-1 text-xs font-semibold text-slate-500">
                            <i class="fab fa-whatsapp text-emerald-500 mr-1"></i>{{ $b->kontak }}
                        </p>
                    </div>
                    @if($b->type === 'ormawa')
                        <span class="shrink-0 rounded-lg bg-indigo-100 px-2.5 py-1 text-[10px] font-black uppercase text-indigo-700 border border-indigo-200">
                            {{ $b->identitas }}
                        </span>
                    @else
                        <span class="shrink-0 rounded-lg bg-slate-100 px-2.5 py-1 text-[10px] font-black uppercase text-slate-600 border border-slate-200">
                            Dosen
                        </span>
                    @endif
                </div>

                <div class="mt-3 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($b->tanggal)->translatedFormat('d M Y') }}</p>
                        <p class="mt-0.5 font-mono text-[11px] font-bold text-indigo-600">
                            {{ substr($b->jam_mulai, 0, 5) }} - {{ substr($b->jam_selesai, 0, 5) }}
                        </p>
                    </div>
                    <button type="button"
                            onclick="openDetailModal('pending-detail-{{ $b->type }}-{{ $b->id_booking }}')"
                            class="inline-flex h-9 items-center justify-center gap-1.5 rounded-xl bg-blue-700 px-4 text-xs font-black uppercase text-white shadow-md shadow-blue-700/20 transition hover:bg-blue-800">
                        <i class="fas fa-eye"></i> Detail
                    </button>
                </div>
            </article>
        @empty
            <div class="px-6 py-12 text-center text-slate-400">
                <i class="fas fa-coffee text-4xl mb-3 text-slate-300"></i>
                <p class="font-bold text-base">Semua Bersih!</p>
                <p class="mt-1 text-sm font-medium">Belum ada pengajuan booking yang perlu di-review.</p>
            </div>
        @endforelse
    </div>

    <div class="hidden md:block">
        <table class="w-full table-fixed text-left text-sm">
            <thead class="bg-blue-900 text-white font-extrabold uppercase tracking-wider text-xs">
                <tr>
                    <th class="w-[34%] px-6 py-4">Pengaju & Kontak</th>
                    <th class="w-[18%] px-4 py-4 text-center">Tipe Identitas</th>
                    <th class="w-[22%] px-4 py-4">Waktu Pelaksanaan</th>
                    <th class="w-[14%] px-4 py-4">Kebutuhan</th>
                    <th class="w-[12%] px-4 py-4 text-center">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($bookings as $b)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="truncate font-bold text-slate-800 text-base">{{ $b->nama_pengaju }}</div>
                            <div class="text-xs font-semibold text-slate-500 mt-1">
                                <i class="fab fa-whatsapp text-emerald-500 mr-1"></i> {{ $b->kontak }}
                            </div>
                        </td>
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
                        <td class="px-4 py-4">
                            <div class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($b->tanggal)->translatedFormat('d M Y') }}</div>
                            <div class="text-xs font-mono font-bold text-indigo-600 mt-1 tracking-widest">
                                {{ substr($b->jam_mulai, 0, 5) }} - {{ substr($b->jam_selesai, 0, 5) }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="font-bold text-slate-800 text-xs">
                                <i class="fas fa-users text-slate-400 mr-1.5"></i>{{ $b->kapasitas }} Orang
                            </div>
                            <div class="text-[11px] font-bold {{ $b->type === 'ormawa' ? 'text-indigo-600' : 'text-slate-400' }} mt-1">
                                <i class="fas fa-door-open mr-1.5"></i>{{ $b->type === 'ormawa' ? $b->jumlah_lab . ' Lab' : '1 Lab' }}
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <button type="button"
                                    onclick="openDetailModal('pending-detail-{{ $b->type }}-{{ $b->id_booking }}')"
                                    class="inline-flex h-9 w-full max-w-[7rem] items-center justify-center gap-1.5 rounded-xl bg-blue-700 px-3 text-xs font-black uppercase text-white shadow-md shadow-blue-700/20 transition hover:bg-blue-800">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-slate-400">
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

{{-- MODAL DETAIL PENDING --}}
@foreach($bookings as $b)
    @php
        $availableCount = collect($b->lab_options)->where('is_colliding', false)->count();
        $savedCount = count($b->current_lab_ids ?? []);
        $maxLabs = max(1, $availableCount, $savedCount, (int)$b->jumlah_lab);
        $initialSelectedLabs = array_map('strval', $b->current_lab_ids ?? []);
        for ($slot = count($initialSelectedLabs); $slot < $maxLabs; $slot++) {
            $initialSelectedLabs[] = '';
        }
    @endphp
    <div id="pending-detail-{{ $b->type }}-{{ $b->id_booking }}" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
        <div class="w-full max-w-5xl overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 bg-slate-50 px-5 py-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-wider text-slate-400">Detail Pengajuan</p>
                    <h3 class="mt-1 text-lg font-black text-slate-900">{{ $b->nama_pengaju }}</h3>
                    <p class="mt-1 text-xs font-semibold text-slate-500">
                        {{ \Carbon\Carbon::parse($b->tanggal)->translatedFormat('d M Y') }} · {{ substr($b->jam_mulai, 0, 5) }} - {{ substr($b->jam_selesai, 0, 5) }}
                    </p>
                </div>
                <button type="button" onclick="closeDetailModal('pending-detail-{{ $b->type }}-{{ $b->id_booking }}')" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-500 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="max-h-[72vh] overflow-y-auto p-5">
                <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(20rem,0.9fr)]">
                    <div class="space-y-4">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Kapasitas</p>
                                <p class="mt-1 text-sm font-black text-slate-800">{{ $b->kapasitas }} Orang</p>
                                <p class="mt-1 text-xs font-bold text-indigo-600">{{ $b->type === 'ormawa' ? $b->jumlah_lab . ' Lab diminta' : '1 Lab' }}</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Dokumen</p>
                                @if($b->file_surat)
                                    <a href="{{ asset('surat_ormawa/' . $b->file_surat) }}" target="_blank" class="mt-2 inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-500 hover:text-white border border-red-200">
                                        <i class="fas fa-file-pdf"></i> Buka PDF
                                    </a>
                                @else
                                    <p class="mt-2 text-xs font-bold text-slate-400">Tidak ada dokumen.</p>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Keperluan</p>
                            <p class="mt-2 whitespace-normal text-sm font-semibold leading-relaxed text-slate-700">{{ $b->keperluan }}</p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <h4 class="text-sm font-black text-slate-900">Penempatan Lab</h4>
                        <p class="mt-1 text-xs font-semibold text-slate-500">Pilih lab yang tersedia sesuai kebutuhan dan kapasitas.</p>

                        @if($b->type === 'ormawa')
                            <form action="{{ route('spv.booking.update_lab', ['type' => $b->type, 'id' => $b->id_booking]) }}"
                                  method="POST"
                                  x-data="{ 
                                      selectedLabs: @js($initialSelectedLabs), 
                                      requiredLabs: {{ count($b->current_lab_ids ?? []) ?: (int) $b->jumlah_lab }}, 
                                      originalLabs: {{ (int) $b->jumlah_lab }},
                                      kapasitasTotal: {{ (int) $b->kapasitas }},
                                      isOptionDisabled(idLab, kapasitasLab, isColliding, index) {
                                          if (isColliding) return true;
                                          let isAlreadySelected = this.selectedLabs.slice(0, parseInt(this.requiredLabs)).includes(idLab.toString()) && this.selectedLabs[index] !== idLab.toString();
                                          if (isAlreadySelected) return true;
                                          let kapasitasPerLabRequired = Math.ceil(this.kapasitasTotal / parseInt(this.requiredLabs));
                                          if (kapasitasLab < kapasitasPerLabRequired) return true;
                                          return false;
                                      },
                                      getLabStatusText(namaLab, kapasitasLab, isColliding) {
                                          if (isColliding) return namaLab + ' sudah dipakai/penuh';
                                          let kapasitasPerLabRequired = Math.ceil(this.kapasitasTotal / parseInt(this.requiredLabs));
                                          if (kapasitasLab < kapasitasPerLabRequired) return namaLab + ' (Kapasitas Kurang: ' + kapasitasLab + ' < ' + kapasitasPerLabRequired + ')';
                                          return namaLab + ' (Tersedia)';
                                      }
                                  }"
                                  class="mt-4 space-y-3">
                                @csrf @method('PATCH')

                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Jumlah lab disetujui</span>
                                    <select name="jumlah_lab_disetujui" x-model="requiredLabs"
                                            class="h-10 rounded-xl border border-slate-300 bg-white px-3 pr-8 text-xs font-black text-indigo-700 outline-none transition focus:border-indigo-500 cursor-pointer">
                                        @for($l = 1; $l <= $maxLabs; $l++)
                                            <option value="{{ $l }}">{{ $l }} Lab</option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="grid gap-2">
                                    @for($i = 0; $i < $maxLabs; $i++)
                                        @php $selectedLabId = $b->current_lab_ids[$i] ?? null; @endphp
                                        <div class="relative" x-show="{{ $i }} < parseInt(requiredLabs)">
                                            <select name="lab_ids[]"
                                                    x-model="selectedLabs[{{ $i }}]"
                                                    :disabled="{{ $i }} >= parseInt(requiredLabs)"
                                                    class="h-11 w-full appearance-none rounded-xl border border-slate-300 bg-white px-3 pr-9 text-xs font-bold text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 cursor-pointer">
                                                <option value="" {{ $selectedLabId ? '' : 'selected' }} class="font-bold text-slate-400">Pilih Lab</option>
                                                @foreach($b->lab_options as $opt)
                                                    @continue(! str_contains(strtoupper($opt['nama_lab']), 'LAB'))
                                                    @php $isSelected = (int) $selectedLabId === (int) $opt['id_lab']; @endphp
                                                    <option value="{{ $opt['id_lab'] }}"
                                                            {{ $isSelected ? 'selected' : '' }}
                                                            :disabled="isOptionDisabled('{{ $opt['id_lab'] }}', {{ $opt['kapasitas'] }}, {{ $opt['is_colliding'] ? 'true' : 'false' }}, {{ $i }})"
                                                            :class="isOptionDisabled('{{ $opt['id_lab'] }}', {{ $opt['kapasitas'] }}, {{ $opt['is_colliding'] ? 'true' : 'false' }}, {{ $i }}) ? 'font-semibold text-slate-400 bg-red-50' : 'font-bold text-emerald-700'"
                                                            x-text="getLabStatusText('{{ $opt['nama_lab'] }}', {{ $opt['kapasitas'] }}, {{ $opt['is_colliding'] ? 'true' : 'false' }})">
                                                        {{ $opt['nama_lab'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <i class="fas fa-chevron-down pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-slate-400"></i>
                                        </div>
                                    @endfor
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-wider text-indigo-600 mb-1">
                                        Alasan Perubahan Lab <span class="text-red-500" x-show="parseInt(requiredLabs) < parseInt(originalLabs)">*</span>
                                    </label>
                                    <input type="text" name="alasan_perubahan" :required="parseInt(requiredLabs) < parseInt(originalLabs)"
                                           value="{{ $b->alasan_perubahan }}"
                                           placeholder="Contoh: Kapasitas lab lain tidak cukup / Lab 3 sedang maintenance"
                                           class="h-10 w-full rounded-xl border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 outline-none focus:border-indigo-500">
                                </div>

                                <button type="submit"
                                        :disabled="selectedLabs.slice(0, parseInt(requiredLabs)).filter(Boolean).length !== parseInt(requiredLabs)"
                                        class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 text-xs font-black uppercase tracking-wide text-white shadow-md shadow-indigo-600/20 transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:shadow-none">
                                    <i class="fas fa-save"></i> Simpan Penempatan Lab
                                </button>
                            </form>
                        @else
                            <form action="{{ route('spv.booking.update_lab', ['type' => $b->type, 'id' => $b->id_booking]) }}" method="POST" class="mt-4">
                                @csrf @method('PATCH')
                                <select name="lab_id" onchange="this.form.submit()" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-xs font-bold text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 cursor-pointer">
                                    @if($b->current_lab === 'TBD' || empty($b->current_id_lab))
                                        <option value="" selected class="font-bold text-slate-400">Pilih Lab</option>
                                    @endif
                                    @foreach($b->lab_options as $opt)
                                        @continue(! str_contains(strtoupper($opt['nama_lab']), 'LAB'))
                                        @php $isSelected = ($b->current_id_lab == $opt['id_lab'] || $b->current_lab == $opt['nama_lab']); @endphp
                                        @if($opt['is_busy'])
                                            <option value="" disabled class="bg-red-50 font-bold text-red-500">{{ $opt['nama_lab'] }} sudah dipakai/penuh</option>
                                        @else
                                            <option value="{{ $opt['id_lab'] }}" {{ $isSelected ? 'selected' : '' }} class="font-bold text-emerald-700">{{ $opt['nama_lab'] }} (Tersedia)</option>
                                        @endif
                                    @endforeach
                                </select>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-end">
                <button type="button" onclick="closeDetailModal('pending-detail-{{ $b->type }}-{{ $b->id_booking }}')" class="inline-flex h-10 items-center justify-center rounded-xl bg-white px-5 text-sm font-bold text-slate-600 ring-1 ring-slate-200 transition hover:bg-slate-100">
                    Cancel
                </button>
                <button type="button"
                        onclick="closeDetailModal('pending-detail-{{ $b->type }}-{{ $b->id_booking }}'); openRejectModal('{{ $b->type }}', '{{ $b->id_booking }}', '{{ addslashes($b->nama_pengaju) }}')"
                        class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-red-100 px-5 text-sm font-black text-red-700 ring-1 ring-red-200 transition hover:bg-red-600 hover:text-white">
                    <i class="fas fa-times"></i> Tolak
                </button>
                <form method="POST" action="{{ route('spv.booking.approve', ['type' => $b->type, 'id' => $b->id_booking]) }}" class="m-0">
                    @csrf
                    <button type="submit" class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-emerald-500 px-5 text-sm font-black text-white shadow-md shadow-emerald-500/30 transition hover:bg-emerald-600 sm:w-auto">
                        <i class="fas fa-check"></i> Setujui
                    </button>
                </form>
            </div>
        </div>
    </div>
@endforeach

{{-- TABEL RIWAYAT HISTORY --}}
<div class="mt-12 mb-4 border-t border-slate-200 pt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
            <i class="fas fa-history text-indigo-500"></i> Riwayat Keputusan
        </h2>
        <p class="text-slate-500 font-medium mt-1 text-sm">Daftar peminjaman yang sebelumnya sudah disetujui atau ditolak.</p>
    </div>
    <div id="bulk-action-bar" class="hidden items-center gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-2.5 shadow-sm">
        <span class="text-sm font-bold text-red-700"><span id="selected-count">0</span> item dipilih</span>
        <button type="button" onclick="submitBulkDelete()" class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-4 py-2 text-xs font-black uppercase text-white shadow-md transition hover:bg-red-700">
            <i class="fas fa-trash-alt"></i> Hapus Terpilih
        </button>
    </div>
</div>

<form id="bulk-delete-form" action="{{ route('spv.booking.history.bulk_delete') }}" method="POST">
    @csrf
    @method('DELETE')
    <div id="hidden-ids-container"></div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-200/40 overflow-hidden mb-10">
        <div class="divide-y divide-slate-100 md:hidden">
            @forelse($historyBookings as $h)
                <article class="p-4">
                    <div class="min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-black text-slate-800">{{ $h->nama_pengaju }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ \Carbon\Carbon::parse($h->tanggal)->translatedFormat('d M Y') }}</p>
                            </div>
                            @if($h->status === 'approved')
                                <span class="shrink-0 rounded-lg bg-emerald-100 px-2.5 py-1 text-[10px] font-black uppercase text-emerald-700 border border-emerald-200">Disetujui</span>
                            @else
                                <span class="shrink-0 rounded-lg bg-red-100 px-2.5 py-1 text-[10px] font-black uppercase text-red-700 border border-red-200">Ditolak</span>
                            @endif
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-3">
                            <p class="font-mono text-[11px] font-bold text-slate-500">{{ substr($h->jam_mulai, 0, 5) }} - {{ substr($h->jam_selesai, 0, 5) }}</p>
                            <button type="button"
                                    onclick="openDetailModal('history-detail-{{ $h->type }}-{{ $h->id_booking }}')"
                                    class="inline-flex h-9 items-center justify-center gap-1.5 rounded-xl bg-slate-800 px-4 text-xs font-black uppercase text-white shadow-md shadow-slate-800/15 transition hover:bg-slate-900">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                        </div>
                    </div>
                </article>
            @empty
                <div class="px-6 py-12 text-center text-slate-400">
                    <i class="fas fa-history text-4xl mb-3 text-slate-300 block"></i>
                    <span class="text-sm font-bold">Belum ada riwayat persetujuan.</span>
                </div>
            @endforelse
        </div>

        <div class="hidden md:block">
            <table class="w-full table-fixed text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-extrabold uppercase tracking-wider text-xs border-b border-slate-200">
                    <tr>
                        <th class="w-[18%] px-6 py-4">Status</th>
                        <th class="w-[34%] px-6 py-4">Pengaju & Kontak</th>
                        <th class="w-[18%] px-4 py-4 text-center">Tipe</th>
                        <th class="w-[18%] px-4 py-4">Waktu</th>
                        <th class="w-[12%] px-4 py-4 text-center">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($historyBookings as $h)
                        <tr class="hover:bg-slate-50 transition history-row" data-id="{{ $h->id_booking }}" data-type="{{ $h->type }}">
                            <td class="px-6 py-4">
                                @if($h->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 px-3 py-1.5 text-xs font-black uppercase text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-check-circle"></i> Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-red-100 px-3 py-1.5 text-xs font-black uppercase text-red-700 border border-red-200">
                                        <i class="fas fa-times-circle"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="truncate font-bold text-slate-800 text-base">{{ $h->nama_pengaju }}</div>
                                <div class="text-xs font-semibold text-slate-500 mt-1">
                                    <i class="fab fa-whatsapp text-emerald-500 mr-1"></i> {{ $h->kontak }}
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($h->type === 'ormawa')
                                    <span class="inline-flex rounded-lg bg-indigo-50 px-3 py-1 text-[11px] font-black uppercase text-indigo-600 border border-indigo-100">{{ $h->identitas }}</span>
                                @else
                                    <span class="inline-flex rounded-lg bg-slate-50 px-3 py-1 text-[11px] font-black uppercase text-slate-500 border border-slate-200">Dosen / Staf</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($h->tanggal)->translatedFormat('d M Y') }}</div>
                                <div class="text-xs font-mono font-bold text-slate-500 mt-1 tracking-widest">
                                    {{ substr($h->jam_mulai, 0, 5) }} - {{ substr($h->jam_selesai, 0, 5) }}
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <button type="button"
                                        onclick="openDetailModal('history-detail-{{ $h->type }}-{{ $h->id_booking }}')"
                                        class="inline-flex h-9 w-full max-w-[7rem] items-center justify-center gap-1.5 rounded-xl bg-slate-800 px-3 text-xs font-black uppercase text-white shadow-md shadow-slate-800/15 transition hover:bg-slate-900">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <i class="fas fa-history text-4xl mb-3 text-slate-300 block"></i>
                                <span class="text-sm font-bold">Belum ada riwayat persetujuan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</form>

{{-- MODAL DETAIL RIWAYAT --}}
@foreach($historyBookings as $h)
    <div id="history-detail-{{ $h->type }}-{{ $h->id_booking }}" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
        <div class="w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 bg-slate-50 px-5 py-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-wider text-slate-400">Detail Riwayat</p>
                    <h3 class="mt-1 text-lg font-black text-slate-900">{{ $h->nama_pengaju }}</h3>
                    <p class="mt-1 text-xs font-semibold text-slate-500">
                        {{ \Carbon\Carbon::parse($h->tanggal)->translatedFormat('d M Y') }} · {{ substr($h->jam_mulai, 0, 5) }} - {{ substr($h->jam_selesai, 0, 5) }}
                    </p>
                </div>
                <button type="button" onclick="closeDetailModal('history-detail-{{ $h->type }}-{{ $h->id_booking }}')" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-500 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="max-h-[72vh] overflow-y-auto p-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Kapasitas</p>
                        <p class="mt-1 text-sm font-black text-slate-800">{{ $h->kapasitas }} Orang</p>
                        <p class="mt-1 text-xs font-bold text-indigo-600">{{ $h->type === 'ormawa' ? $h->jumlah_lab . ' Lab' : '1 Lab' }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Penempatan Lab</p>
                        <p class="mt-2 text-sm font-bold leading-relaxed text-slate-700">{{ $h->current_lab ?: '-' }}</p>
                    </div>
                </div>

                <div class="mt-4 rounded-xl border border-slate-200 bg-white p-4">
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Keperluan</p>
                    <p class="mt-2 whitespace-normal text-sm font-semibold leading-relaxed text-slate-700">{{ $h->keperluan }}</p>
                    @if($h->type === 'ormawa' && $h->alasan_perubahan)
                        <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700">
                            <i class="fas fa-info-circle mr-1"></i> Perubahan: {{ $h->alasan_perubahan }}
                        </div>
                    @endif
                </div>

                <div class="mt-4 rounded-xl border border-slate-200 bg-white p-4">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Alasan Penolakan</p>
                        @if($h->status === 'rejected')
                            <button type="button"
                                    onclick="closeDetailModal('history-detail-{{ $h->type }}-{{ $h->id_booking }}'); openEditReasonModal('{{ $h->type }}', '{{ $h->id_booking }}', '{{ addslashes($h->nama_pengaju) }}', '{{ addslashes($h->alasan_penolakan ?? '') }}')"
                                    class="inline-flex h-8 items-center gap-1.5 rounded-lg bg-indigo-50 px-3 text-xs font-bold text-indigo-600 ring-1 ring-indigo-100 transition hover:bg-indigo-600 hover:text-white">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        @endif
                    </div>
                    @if($h->status === 'rejected')
                        <div class="mt-2 max-h-44 overflow-y-auto rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-xs font-semibold leading-relaxed text-red-700">
                            {{ $h->alasan_penolakan ?: 'Tidak ada keterangan.' }}
                        </div>
                    @else
                        <p class="mt-2 text-sm font-semibold text-slate-400">-</p>
                    @endif
                </div>
            </div>

            <div class="flex justify-end border-t border-slate-100 bg-slate-50 px-5 py-4">
                <button type="button" onclick="closeDetailModal('history-detail-{{ $h->type }}-{{ $h->id_booking }}')" class="inline-flex h-10 items-center justify-center rounded-xl bg-white px-5 text-sm font-bold text-slate-600 ring-1 ring-slate-200 transition hover:bg-slate-100">
                    Tutup
                </button>
            </div>
        </div>
    </div>
@endforeach

{{-- REJECT REASON MODAL --}}
<div id="reject-reason-modal" class="fixed inset-0 z-[90] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" style="transition: opacity 0.3s ease;">
    <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl transform transition-transform scale-95" id="reject-reason-box" style="transition: transform 0.3s ease;">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-red-500">
            <i class="fas fa-times-circle text-2xl"></i>
        </div>
        <h3 class="mb-1 text-lg font-extrabold text-slate-800 text-center">Tolak Pengajuan</h3>
        <p class="mb-4 text-sm font-medium text-slate-500 text-center">Pengaju: <span id="reject-pengaju-name" class="font-bold text-slate-700"></span></p>

        <form id="reject-form" method="POST" action="">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
                    Alasan Penolakan <span class="text-slate-400 font-normal normal-case">(opsional, maks 500 karakter)</span>
                </label>
                <textarea name="alasan_penolakan" id="reject-alasan-input"
                          rows="3"
                          maxlength="500"
                          placeholder="Contoh: Jadwal lab sudah penuh pada waktu tersebut..."
                          class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-medium text-slate-800 outline-none transition focus:border-red-400 focus:bg-white focus:ring-4 focus:ring-red-400/10 resize-none"></textarea>
                <p class="text-xs text-slate-400 mt-1 text-right"><span id="reject-char-count">0</span>/500</p>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()"
                        class="w-full rounded-xl bg-slate-100 py-3 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit"
                        class="w-full rounded-xl bg-red-600 py-3 text-sm font-black uppercase tracking-wider text-white shadow-md shadow-red-600/20 transition hover:bg-red-700">
                    <i class="fas fa-times mr-1"></i> Tolak Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT REASON MODAL --}}
<div id="edit-reason-modal" class="fixed inset-0 z-[90] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" style="transition: opacity 0.3s ease;">
    <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl transform transition-transform scale-95" id="edit-reason-box" style="transition: transform 0.3s ease;">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-indigo-100 text-indigo-500">
            <i class="fas fa-edit text-2xl"></i>
        </div>
        <h3 class="mb-1 text-lg font-extrabold text-slate-800 text-center">Edit Alasan Penolakan</h3>
        <p class="mb-4 text-sm font-medium text-slate-500 text-center">Pengaju: <span id="edit-pengaju-name" class="font-bold text-slate-700"></span></p>

        <form id="edit-reason-form" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
                    Alasan Penolakan <span class="text-slate-400 font-normal normal-case">(opsional, maks 500 karakter)</span>
                </label>
                <textarea name="alasan_penolakan" id="edit-alasan-input"
                          rows="3"
                          maxlength="500"
                          placeholder="Contoh: Jadwal lab sudah penuh pada waktu tersebut..."
                          class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-medium text-slate-800 outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-400/10 resize-none"></textarea>
                <p class="text-xs text-slate-400 mt-1 text-right"><span id="edit-char-count">0</span>/500</p>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeEditReasonModal()"
                        class="w-full rounded-xl bg-slate-100 py-3 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit"
                        class="w-full rounded-xl bg-indigo-600 py-3 text-sm font-black uppercase tracking-wider text-white shadow-md shadow-indigo-600/20 transition hover:bg-indigo-700">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
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

<script>
let currentConfirmCallback = null;

function openDetailModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDetailModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

document.addEventListener('click', function (event) {
    if (event.target.classList && event.target.classList.contains('fixed') && event.target.id.includes('-detail-')) {
        closeDetailModal(event.target.id);
    }
});

// ---- REJECT MODAL ----
function openRejectModal(type, id, pengajuName) {
    document.getElementById('reject-pengaju-name').textContent = pengajuName;
    document.getElementById('reject-alasan-input').value = '';
    document.getElementById('reject-char-count').textContent = '0';

    const baseUrl = '{{ url("spv/booking/reject") }}';
    document.getElementById('reject-form').action = `${baseUrl}/${type}/${id}`;

    const modal = document.getElementById('reject-reason-modal');
    const box = document.getElementById('reject-reason-box');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        box.classList.remove('scale-95');
        box.classList.add('scale-100');
    }, 10);

    document.getElementById('reject-alasan-input').focus();
}

function closeRejectModal() {
    const modal = document.getElementById('reject-reason-modal');
    const box = document.getElementById('reject-reason-box');
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    box.classList.remove('scale-100');
    box.classList.add('scale-95');
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 300);
}

document.getElementById('reject-alasan-input').addEventListener('input', function() {
    document.getElementById('reject-char-count').textContent = this.value.length;
});

document.getElementById('reject-reason-modal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});

// ---- EDIT REASON MODAL ----
function openEditReasonModal(type, id, pengajuName, currentAlasan) {
    document.getElementById('edit-pengaju-name').textContent = pengajuName;
    document.getElementById('edit-alasan-input').value = currentAlasan;
    document.getElementById('edit-char-count').textContent = currentAlasan.length;

    const baseUrl = '{{ url("spv/booking/update-rejection") }}';
    document.getElementById('edit-reason-form').action = `${baseUrl}/${type}/${id}`;

    const modal = document.getElementById('edit-reason-modal');
    const box = document.getElementById('edit-reason-box');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        box.classList.remove('scale-95');
        box.classList.add('scale-100');
    }, 10);

    document.getElementById('edit-alasan-input').focus();
}

function closeEditReasonModal() {
    const modal = document.getElementById('edit-reason-modal');
    const box = document.getElementById('edit-reason-box');
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    box.classList.remove('scale-100');
    box.classList.add('scale-95');
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 300);
}

document.getElementById('edit-alasan-input').addEventListener('input', function() {
    document.getElementById('edit-char-count').textContent = this.value.length;
});

document.getElementById('edit-reason-modal').addEventListener('click', function(e) {
    if (e.target === this) closeEditReasonModal();
});

// ---- BULK DELETE ----
function toggleSelectAll(masterCheckbox) {
    const checkboxes = document.querySelectorAll('.history-checkbox');
    checkboxes.forEach(cb => { cb.checked = masterCheckbox.checked; });
    updateSelection();
}

function updateSelection() {
    const checkboxes = document.querySelectorAll('.history-checkbox:checked');
    const count = checkboxes.length;
    const bar = document.getElementById('bulk-action-bar');
    document.getElementById('selected-count').textContent = count;

    if (count > 0) {
        bar.classList.remove('hidden');
        bar.classList.add('flex');
    } else {
        bar.classList.add('hidden');
        bar.classList.remove('flex');
    }

    const allCheckboxes = document.querySelectorAll('.history-checkbox');
    const selectAll = document.getElementById('select-all-checkbox');
    if (selectAll) {
        selectAll.indeterminate = count > 0 && count < allCheckboxes.length;
        selectAll.checked = count === allCheckboxes.length && allCheckboxes.length > 0;
    }
}

function submitBulkDelete() {
    const checkboxes = document.querySelectorAll('.history-checkbox:checked');
    if (checkboxes.length === 0) return;

    showCustomConfirm(
        `Anda akan menghapus ${checkboxes.length} riwayat booking. Tindakan ini tidak dapat dibatalkan!`,
        'Konfirmasi Hapus Riwayat',
        function() {
            const container = document.getElementById('hidden-ids-container');
            container.innerHTML = '';
            checkboxes.forEach(cb => {
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'selected_ids[]';
                idInput.value = cb.value;
                container.appendChild(idInput);

                const typeInput = document.createElement('input');
                typeInput.type = 'hidden';
                typeInput.name = 'selected_types[]';
                typeInput.value = cb.getAttribute('data-type');
                container.appendChild(typeInput);
            });
            document.getElementById('bulk-delete-form').submit();
        }
    );
}

// ---- CUSTOM CONFIRM ----
function handleCustomConfirmSubmit(event, message, title = 'Konfirmasi') {
    event.preventDefault();
    showCustomConfirm(message, title, function () {
        event.target.submit();
    });
    return false;
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
</script>
@endsection
