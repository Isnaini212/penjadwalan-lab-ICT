@extends('layouts.spv') {{-- Pastikan layout lu udah support Tailwind --}}

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

{{-- TABEL DATA --}}
<div class="rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-200/40 overflow-hidden">
    <p class="md:hidden text-xs font-bold text-slate-400 px-4 py-2 bg-slate-50 border-b border-slate-100"><i class="fas fa-arrows-left-right mr-1"></i> Geser kiri/kanan untuk lihat semua kolom</p>
    <div class="overflow-x-auto">
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
                        @if($b->type === 'ormawa')
                            <div class="mb-1 text-xs font-bold text-indigo-600">
                                <i class="fas fa-door-open text-indigo-400 mr-1"></i> Butuh {{ $b->jumlah_lab }} Lab
                            </div>
                        @endif
                        <div class="text-sm font-semibold text-slate-600">{{ $b->keperluan }}</div>
                    </td>

                    {{-- Dropdown Pilih LAB --}}
                    <td class="px-4 py-4">
                        @if($b->type === 'ormawa')
                            <form action="{{ route('spv.booking.update_lab', ['type' => $b->type, 'id' => $b->id_booking]) }}"
                                  method="POST"
                                  x-data="{ selectedLabs: @js(array_map('strval', $b->current_lab_ids ?? [])), requiredLabs: {{ (int) $b->jumlah_lab }} }"
                                  class="min-w-[420px] max-w-[560px] rounded-2xl border border-slate-200 bg-slate-50/70 p-3 shadow-sm">
                                @csrf @method('PATCH')

                                <div class="mb-2 flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-[11px] font-black uppercase tracking-wide text-slate-500">Pilih Lab Ormawa</p>
                                        <p class="text-[10px] font-bold text-slate-400">Wajib {{ $b->jumlah_lab }} lab berbeda.</p>
                                    </div>
                                    <span class="rounded-full bg-indigo-100 px-3 py-1 text-[10px] font-black text-indigo-700">
                                        {{ $b->jumlah_lab }} Lab
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-start gap-2">
                                    @for($i = 0; $i < $b->jumlah_lab; $i++)
                                        @php
                                            $selectedLabId = $b->current_lab_ids[$i] ?? null;
                                        @endphp

                                        <div class="relative">
                                            <select name="lab_ids[]"
                                                    x-model="selectedLabs[{{ $i }}]"
                                                    class="h-11 w-48 appearance-none rounded-xl border border-slate-300 bg-white px-3 pr-9 text-xs font-bold text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 cursor-pointer">
                                                <option value="" {{ $selectedLabId ? '' : 'selected' }} disabled class="font-bold text-red-500">
                                                    -- Pilih Lab {{ $i + 1 }} --
                                                </option>

                                                @foreach($b->lab_options as $opt)
                                                    @continue(! str_contains(strtoupper($opt['nama_lab']), 'LAB'))

                                                    @php
                                                        $isSelected = (int) $selectedLabId === (int) $opt['id_lab'];
                                                        $labIdString = (string) $opt['id_lab'];
                                                    @endphp

                                                    @if($opt['is_busy'] && ! $isSelected)
                                                        <option value="" disabled class="bg-red-50 font-bold text-red-500">
                                                            {{ $opt['nama_lab'] }} (Penuh / Kapasitas Kurang)
                                                        </option>
                                                    @else
                                                        <option value="{{ $opt['id_lab'] }}"
                                                                {{ $isSelected ? 'selected' : '' }}
                                                                :disabled="selectedLabs.includes('{{ $labIdString }}') && selectedLabs[{{ $i }}] !== '{{ $labIdString }}'"
                                                                class="font-bold text-emerald-700">
                                                            {{ $opt['nama_lab'] }} (Tersedia)
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <i class="fas fa-chevron-down pointer-events-none absolute right-3 top-4 text-[10px] text-slate-400"></i>
                                        </div>
                                    @endfor

                                    <button type="submit"
                                            :disabled="selectedLabs.filter(Boolean).length !== requiredLabs"
                                            class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 text-xs font-black uppercase tracking-wide text-white shadow-md shadow-indigo-600/20 transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:shadow-none">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </div>

                                <p class="mt-2 text-[10px] font-bold text-slate-400">
                                    Lab yang sudah dipilih akan otomatis terkunci di pilihan lain.
                                </p>
                            </form>
                        @else
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
                                            {{ $opt['nama_lab'] }} (Penuh / Kapasitas Kurang)
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
                        @endif
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
                            <form method="POST" action="{{ route('spv.booking.reject', ['type' => $b->type, 'id' => $b->id_booking]) }}" onsubmit="return handleCustomConfirmSubmit(event, 'Tolak pengajuan peminjaman ini?', 'Konfirmasi Tolak')">
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

{{-- TABEL RIWAYAT HISTORY --}}
<div class="mt-12 mb-6 border-t border-slate-200 pt-8">
    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
        <i class="fas fa-history text-indigo-500"></i> Riwayat Keputusan
    </h2>
    <p class="text-slate-500 font-medium mt-1 text-sm">Daftar peminjaman yang sebelumnya sudah disetujui atau ditolak.</p>
</div>

<div class="rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-200/40 overflow-hidden mb-10">
    <p class="md:hidden text-xs font-bold text-slate-400 px-4 py-2 bg-slate-50 border-b border-slate-100"><i class="fas fa-arrows-left-right mr-1"></i> Geser kiri/kanan untuk lihat semua kolom</p>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-slate-50 text-slate-500 font-extrabold uppercase tracking-wider text-xs border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Pengaju & Kontak</th>
                    <th class="px-4 py-4 text-center">Tipe Identitas</th>
                    <th class="px-4 py-4">Waktu Pelaksanaan</th>
                    <th class="px-4 py-4">Keperluan</th>
                    <th class="px-4 py-4">Penempatan Lab</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($historyBookings as $h)
                <tr class="hover:bg-slate-50 transition">
                    {{-- Status --}}
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

                    {{-- Pengaju --}}
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800 text-base">{{ $h->nama_pengaju }}</div>
                        <div class="text-xs font-semibold text-slate-500 mt-1">
                            <i class="fab fa-whatsapp text-emerald-500 mr-1"></i> {{ $h->kontak }}
                        </div>
                    </td>

                    {{-- Tipe Identitas --}}
                    <td class="px-4 py-4 text-center">
                        @if($h->type === 'ormawa')
                            <span class="inline-flex rounded-lg bg-indigo-50 px-3 py-1 text-[11px] font-black uppercase text-indigo-600 border border-indigo-100">
                                {{ $h->identitas }}
                            </span>
                        @else
                            <span class="inline-flex rounded-lg bg-slate-50 px-3 py-1 text-[11px] font-black uppercase text-slate-500 border border-slate-200">
                                <i class="fas fa-chalkboard-teacher mr-1"></i> {{ $h->identitas }}
                            </span>
                        @endif
                    </td>

                    {{-- Waktu --}}
                    <td class="px-4 py-4">
                        <div class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($h->tanggal)->translatedFormat('d M Y') }}</div>
                        <div class="text-xs font-mono font-bold text-slate-500 mt-1 tracking-widest">
                            {{ substr($h->jam_mulai, 0, 5) }} - {{ substr($h->jam_selesai, 0, 5) }}
                        </div>
                    </td>

                    {{-- Keperluan --}}
                    <td class="px-4 py-4 whitespace-normal min-w-[200px]">
                        <div class="font-bold text-slate-500 text-xs mb-1">
                            <i class="fas fa-users text-slate-400 mr-1"></i> {{ $h->kapasitas }} Orang
                        </div>
                        <div class="text-sm font-semibold text-slate-600">{{ $h->keperluan }}</div>
                    </td>

                    {{-- Lab --}}
                    <td class="px-4 py-4">
                        <span class="font-bold text-slate-700">{{ $h->current_lab ?: '-' }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                        <i class="fas fa-history text-4xl mb-3 text-slate-300 block"></i>
                        <span class="text-sm font-bold">Belum ada riwayat persetujuan.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
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
