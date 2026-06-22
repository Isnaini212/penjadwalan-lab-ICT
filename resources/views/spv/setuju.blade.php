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

{{-- TABEL DATA PENDING --}}
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
                                        $isSelected = ($b->current_id_lab == $opt['id_lab'] || $b->current_lab == $opt['nama_lab']);
                                    @endphp

                                    @if($opt['is_busy'])
                                        <option value="" disabled class="bg-red-50 font-bold text-red-500">
                                            {{ $opt['nama_lab'] }} (Penuh / Kapasitas Kurang)
                                        </option>
                                    @else
                                        <option value="{{ $opt['id_lab'] }}" {{ $isSelected ? 'selected' : '' }} class="font-bold text-emerald-700">
                                            ✅ {{ $opt['nama_lab'] }} (Tersedia)
                                        </option>
                                    @endif
                                @endforeach
                                
                            </select>
                        </form>
                    </td>

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

                            {{-- Tombol Reject: buka modal alasan --}}
                            <button type="button"
                                    onclick="openRejectModal('{{ $b->type }}', '{{ $b->id_booking }}', '{{ addslashes($b->nama_pengaju) }}')"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-100 text-red-600 transition hover:bg-red-500 hover:text-white border border-red-200"
                                    title="Tolak dengan alasan">
                                <i class="fas fa-times"></i>
                            </button>
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
<div class="mt-12 mb-4 border-t border-slate-200 pt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
            <i class="fas fa-history text-indigo-500"></i> Riwayat Keputusan
        </h2>
        <p class="text-slate-500 font-medium mt-1 text-sm">Daftar peminjaman yang sebelumnya sudah disetujui atau ditolak.</p>
    </div>
    {{-- Bulk action bar (shown when checkboxes are selected) --}}
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
        <p class="md:hidden text-xs font-bold text-slate-400 px-4 py-2 bg-slate-50 border-b border-slate-100"><i class="fas fa-arrows-left-right mr-1"></i> Geser kiri/kanan untuk lihat semua kolom</p>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-500 font-extrabold uppercase tracking-wider text-xs border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-4 text-center">
                            <input type="checkbox" id="select-all-checkbox" onchange="toggleSelectAll(this)"
                                   class="w-4 h-4 rounded border-slate-300 text-red-600 cursor-pointer accent-red-600">
                        </th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-6 py-4">Pengaju & Kontak</th>
                        <th class="px-4 py-4 text-center">Tipe Identitas</th>
                        <th class="px-4 py-4">Waktu Pelaksanaan</th>
                        <th class="px-4 py-4">Keperluan</th>
                        <th class="px-4 py-4">Penempatan Lab</th>
                        <th class="px-4 py-4 min-w-[200px]">Alasan Penolakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($historyBookings as $h)
                    <tr class="hover:bg-slate-50 transition history-row" data-id="{{ $h->id_booking }}" data-type="{{ $h->type }}">
                        {{-- Checkbox --}}
                        <td class="px-4 py-4 text-center">
                            <input type="checkbox" name="row_checkbox" value="{{ $h->id_booking }}"
                                   data-type="{{ $h->type }}"
                                   onchange="updateSelection()"
                                   class="history-checkbox w-4 h-4 rounded border-slate-300 text-red-600 cursor-pointer accent-red-600">
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-4">
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
                        <td class="px-4 py-4 whitespace-normal min-w-[180px]">
                            <div class="font-bold text-slate-500 text-xs mb-1">
                                <i class="fas fa-users text-slate-400 mr-1"></i> {{ $h->kapasitas }} Orang
                            </div>
                            <div class="text-sm font-semibold text-slate-600">{{ $h->keperluan }}</div>
                        </td>

                        {{-- Lab --}}
                        <td class="px-4 py-4">
                            <span class="font-bold text-slate-700">{{ $h->current_lab ?: '-' }}</span>
                        </td>

                        {{-- Alasan Penolakan --}}
                        <td class="px-4 py-4 whitespace-normal min-w-[220px]">
                            @if($h->status === 'rejected')
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-xs font-semibold text-red-700">
                                        <i class="fas fa-comment-slash mr-1 text-red-400"></i>
                                        <span>{{ $h->alasan_penolakan ?: 'Tidak ada keterangan.' }}</span>
                                    </div>
                                    <button type="button" 
                                            onclick="openEditReasonModal('{{ $h->type }}', '{{ $h->id_booking }}', '{{ addslashes($h->nama_pengaju) }}', '{{ addslashes($h->alasan_penolakan ?? '') }}')"
                                            class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-indigo-500 hover:text-white border border-slate-200 shadow-sm animate-pulse-subtle"
                                            title="Edit alasan penolakan">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                </div>
                            @else
                                <span class="text-xs text-slate-300">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400">
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

// Close reject modal on backdrop click
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

// Close edit modal on backdrop click
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

    // Update select-all state
    const allCheckboxes = document.querySelectorAll('.history-checkbox');
    document.getElementById('select-all-checkbox').indeterminate = count > 0 && count < allCheckboxes.length;
    document.getElementById('select-all-checkbox').checked = count === allCheckboxes.length && allCheckboxes.length > 0;
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
