<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Ormawa - Booking Lab ICT</title>
<link rel="icon" type="image/LogoICT.png" href="{{ asset('images/LogoICT.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 8px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

    {{-- Navbar Terhubung dengan Session Auth --}}
    <nav class="sticky top-0 z-50 border-b border-slate-200 bg-white/80 py-4 backdrop-blur-md shadow-sm">
        <div class="container mx-auto px-6 flex items-center justify-between max-w-5xl">
            <div class="flex items-center gap-3 font-black text-indigo-600 text-xl tracking-tight">
                <i class="fas fa-rocket text-2xl"></i>
                <span>LabSystem <span class="text-slate-400 font-medium">| Ormawa Portal</span></span>
            </div>

            {{-- 🌟 LOGIC: Profil User Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2.5 text-sm font-bold text-slate-700 bg-slate-50 px-2 py-1.5 pr-3.5 sm:pr-4 rounded-full border border-slate-200 hover:bg-slate-100 transition focus:outline-none">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-100 text-xs font-black text-indigo-700">
                        {{ collect(explode(' ', auth()->user()->name))->map(fn($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('') }}
                    </span>
                    <span class="hidden sm:inline text-slate-600">{{ auth()->user()->name ?? 'Ormawa' }}</span>
                    <i class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                     style="display: none;">
                    <div class="px-4 py-2 border-b border-slate-100 text-left">
                        <p class="text-xs font-semibold text-slate-400">Masuk sebagai</p>
                        <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] font-semibold text-indigo-600 uppercase tracking-wider mt-0.5">{{ auth()->user()->role }}</p>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                        <i class="fas fa-user-cog text-slate-400 text-base"></i> Edit Profil
                    </a>

                    <div class="border-t border-slate-100"></div>

                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm font-semibold text-red-600 hover:bg-red-50 hover:text-red-700 transition">
                            <i class="fas fa-sign-out-alt text-red-400 text-base"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-10 max-w-5xl">

        {{-- CARD FORM BOOKING --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 md:p-8 shadow-xl shadow-slate-200/40">

            <div class="mb-8 border-b border-slate-100 pb-5">
                <h2 class="text-xl font-extrabold text-slate-900 md:text-2xl">
                    <i class="fas fa-file-signature mr-2 text-indigo-500"></i> Formulir Peminjaman
                </h2>
                <p class="mt-2 text-sm font-medium text-slate-500">
                    Lengkapi identitas organisasi, waktu penggunaan, dan lampirkan surat peminjaman resmi (.PDF).
                </p>
            </div>

            @if(session('success'))
                <div class="mb-6 rounded-xl bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 border border-emerald-200 flex items-center gap-3">
                    <i class="fas fa-check-circle text-emerald-500 text-xl"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 px-5 py-4 text-sm font-bold text-red-700 border border-red-200">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                        <span>Gagal Mengirim Pengajuan:</span>
                    </div>
                    <ul class="list-disc pl-8 text-xs font-medium text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('ormawa.booking.store') }}" method="POST" enctype="multipart/form-data" id="booking-form">
                @csrf

                {{-- 🌟 LOGIC: Default value Lab dikirim tersembunyi biar validasi tembus --}}
                <input type="hidden" name="lab" value="Belum ditentukan">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Nama Ormawa (Terkunci & Otomatis) --}}
                    <div>
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Nama Organisasi</label>
                        <div class="relative">
                            {{-- 🌟 LOGIC: Di-readonly dan diisi nama akunnya otomatis --}}
                            <input type="text" name="nama_ormawa" required readonly value="{{ auth()->user()->name }}"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-100 py-3 px-4 pl-11 text-sm font-bold text-slate-500 outline-none cursor-not-allowed uppercase">
                            <i class="fas fa-users absolute left-4 top-3.5 text-slate-400"></i>
                        </div>
                        <p class="text-[10px] font-bold text-indigo-500 mt-1">*Diambil otomatis dari akun Anda.</p>
                    </div>

                    {{-- Penanggung Jawab --}}
                    <div>
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Penanggung Jawab <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" name="penanggung_jawab" id="input_penanggung_jawab" required placeholder="Cth: Budi Santoso" value="{{ old('penanggung_jawab') }}"
                                   class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 pl-11 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                            <i class="fas fa-user-tie absolute left-4 top-3.5 text-slate-400"></i>
                        </div>
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Tanggal Peminjaman <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" id="input_tanggal" required value="{{ old('tanggal') }}" min="{{ date('Y-m-d') }}"
                               class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                    </div>

                    {{-- Lab (Disabled Visual) --}}
                    <div>
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Laboratorium</label>
                        <div class="w-full rounded-xl border border-slate-200 bg-slate-100 py-3 px-4 text-sm font-bold text-slate-400 cursor-not-allowed text-center tracking-wider">
                            <i class="fas fa-lock mr-1"></i> DITENTUKAN OLEH SPV
                        </div>
                    </div>

                    {{-- Jam Mulai & Jam Selesai --}}
                    <div>
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Jam Mulai <span class="text-red-500">*</span></label>
                        <input type="text" name="jam_mulai" id="input_jam_mulai" class="time-formatter w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-bold font-mono text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 text-center tracking-widest" placeholder="07:10" maxlength="5" required value="{{ old('jam_mulai') }}">
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Jam Selesai <span class="text-red-500">*</span></label>
                        <input type="text" name="jam_selesai" id="input_jam_selesai" class="time-formatter w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-bold font-mono text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 text-center tracking-widest" placeholder="10:30" maxlength="5" required value="{{ old('jam_selesai') }}">
                    </div>

                    {{-- Kapasitas --}}
                    <div>
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Jumlah Peserta<span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="kapasitas" id="input_kapasitas" required placeholder="Cth: 30" min=1 value="{{ old('kapasitas') }}"
                                   class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 pl-12 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                            <i class="fas fa-chair absolute left-4 top-3.5 text-slate-400"></i>
                        </div>
                    </div>

                    {{-- Jumlah Lab --}}
                    <div>
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Jumlah Lab Dibutuhkan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="jumlah_lab" id="select_jumlah_lab" required disabled
                                    class="w-full appearance-none rounded-xl border border-slate-300 bg-slate-100 py-3 px-4 pl-12 pr-10 text-sm font-bold text-slate-400 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 cursor-not-allowed">
                                <option value="">Isi Info Tanggal & Waktu Dahulu</option>
                            </select>
                            <i class="fas fa-door-open absolute left-4 top-3.5 text-slate-400"></i>
                            <i class="fas fa-chevron-down pointer-events-none absolute right-4 top-4 text-xs text-slate-400"></i>
                        </div>
                        <p class="mt-1 text-[10px] font-bold text-indigo-500">Standar maksimal 36 peserta per lab. Lab tetap ditentukan oleh SPV.</p>
                    </div>

                    {{-- Keperluan --}}
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Nama Acara & Kebutuhan Software <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" name="keperluan" id="input_keperluan" required placeholder="Cth: Pelatihan Desain (Butuh Photoshop)" value="{{ old('keperluan') }}"
                                   class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 pl-12 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                            <i class="fas fa-info-circle absolute left-4 top-3.5 text-slate-400"></i>
                        </div>
                    </div>

                    {{-- Upload Surat PDF --}}
                    <div class="md:col-span-2 rounded-xl border-2 border-dashed border-indigo-200 bg-indigo-50/50 p-6 text-center transition hover:bg-indigo-50">
                        <label class="mb-3 block text-sm font-extrabold text-indigo-700">
                            <i class="fas fa-file-pdf mr-2 text-red-500 text-lg"></i> Unggah Surat Peminjaman Resmi
                        </label>
                        <input type="file" name="file_surat" accept="application/pdf" required onchange="checkFileExtensionPdf(this)"
                               class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-indigo-100 file:px-4 file:py-2 file:text-sm file:font-bold file:text-indigo-700 hover:file:bg-indigo-200 mx-auto max-w-sm cursor-pointer">
                        <p class="mt-2 text-xs font-semibold text-slate-400">Format wajib .PDF (Maksimal 2MB)</p>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" id="btn-submit" class="inline-flex w-full md:w-auto justify-center items-center gap-2 rounded-xl bg-indigo-600 px-8 py-3.5 text-sm font-black text-white uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition hover:bg-indigo-700 hover:-translate-y-0.5 focus:ring-4 focus:ring-indigo-600/20">
                        <i class="fas fa-paper-plane"></i> Kirim Pengajuan Booking
                    </button>
                </div>
            </form>
        </div>

        {{-- CARD RIWAYAT BOOKING TERBARU --}}
        <div class="mt-10 rounded-2xl border border-slate-200 bg-white p-4 shadow-xl shadow-slate-200/40 sm:p-6 md:p-8">

            <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="flex items-center gap-2 text-lg font-extrabold text-slate-900">
                    <i class="fas fa-history text-indigo-500"></i> Riwayat Pengajuan Anda
                </h3>
                <p class="text-xs font-semibold text-slate-400">Pantau status peminjaman dan keputusan SPV.</p>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200">
                <table class="w-full table-fixed text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-extrabold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="w-[23%] px-4 py-4 sm:px-5">Tanggal & Hari</th>
                            <th class="w-[26%] px-4 py-4 sm:px-5">Lab & Kapasitas</th>
                            <th class="w-[18%] px-4 py-4 sm:px-5">Waktu</th>
                            <th class="w-[16%] px-4 py-4 text-center sm:px-5">Status</th>
                            <th class="w-[17%] px-4 py-4 text-center sm:px-5">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        {{-- 🌟 Pastikan MhsController mengirim data $myBookings --}}
                        @if(isset($myBookings) && $myBookings->count() > 0)
                            @foreach($myBookings as $book)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-4 align-top sm:px-5">
                                    <div class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($book->tanggal)->translatedFormat('d F Y') }}</div>
                                    <div class="text-xs font-semibold text-slate-400 mt-0.5">{{ $book->hari }}{{ strtolower($book->hari) === 'sabtu' ? ' (Kelas Karyawan)' : '' }}</div>
                                </td>
                                <td class="px-4 py-4 align-top sm:px-5">
                                    <span class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-extrabold text-slate-600 border border-slate-200 mb-1.5">
                                        {{ $book->lab }}
                                    </span>
                                    <div class="text-[11px] font-bold text-indigo-600 mb-0.5">
                                        <i class="fas fa-door-open text-indigo-400 mr-1"></i> {{ $book->jumlah_lab }} Lab
                                    </div>
                                    <div class="text-[11px] font-bold text-slate-500">
                                        <i class="fas fa-users text-slate-400 mr-1"></i> {{ $book->kapasitas }} Orang
                                    </div>
                                </td>
                                <td class="px-4 py-4 align-top sm:px-5">
                                    <div class="font-mono text-xs font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1 w-fit">
                                        {{ substr($book->jam_mulai, 0, 5) }} - {{ substr($book->jam_selesai, 0, 5) }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center align-top sm:px-5">
                                    @if($book->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-amber-800 border border-amber-300">
                                            <i class="fas fa-hourglass-half"></i> Menunggu
                                        </span>
                                    @elseif($book->status === 'approved')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-emerald-800 border border-emerald-300">
                                            <i class="fas fa-check"></i> Disetujui
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-red-800 border border-red-300">
                                            <i class="fas fa-times"></i> Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center align-top sm:px-5">
                                    <button type="button"
                                            onclick="openHistoryDetailModal('ormawa-history-detail-{{ $book->id_booking }}')"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-slate-800 px-3 py-2 text-xs font-bold text-white transition hover:bg-slate-900">
                                        <i class="fas fa-eye text-[10px]"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <i class="fas fa-clipboard-check text-4xl mb-3 text-slate-300"></i>
                                        <span class="font-bold">Anda belum pernah mengajukan peminjaman.</span>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if(isset($myBookings) && $myBookings->count() > 0)
            @foreach($myBookings as $book)
                <div id="ormawa-history-detail-{{ $book->id_booking }}" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
                    <div class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                        <div class="flex items-start justify-between gap-4 border-b border-slate-100 bg-slate-50 px-5 py-4">
                            <div>
                                <p class="text-xs font-black uppercase tracking-wider text-slate-400">Detail Riwayat Pengajuan</p>
                                <h3 class="mt-1 text-lg font-black text-slate-900">{{ \Carbon\Carbon::parse($book->tanggal)->translatedFormat('d F Y') }}</h3>
                                <p class="mt-1 text-xs font-semibold text-slate-500">
                                    {{ $book->hari }}{{ strtolower($book->hari) === 'sabtu' ? ' (Kelas Karyawan)' : '' }} · {{ substr($book->jam_mulai, 0, 5) }} - {{ substr($book->jam_selesai, 0, 5) }}
                                </p>
                            </div>
                            <button type="button" onclick="closeHistoryDetailModal('ormawa-history-detail-{{ $book->id_booking }}')" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-500 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-100">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4 p-5">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Keperluan</p>
                                <p class="mt-2 text-sm font-semibold leading-relaxed text-slate-700">{{ $book->keperluan }}</p>
                                @if($book->alasan_perubahan)
                                    <div class="mt-3 flex items-start gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold leading-relaxed text-amber-700">
                                        <i class="fas fa-info-circle mt-0.5 text-amber-500"></i>
                                        <span>Perubahan: {{ $book->alasan_perubahan }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Alasan Penolakan</p>
                                @if($book->status === 'rejected' && !empty($book->alasan_penolakan))
                                    <div class="mt-2 max-h-40 overflow-y-auto rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold leading-relaxed text-red-700">
                                        <i class="fas fa-comment-slash mr-1 text-red-400"></i>
                                        {{ $book->alasan_penolakan }}
                                    </div>
                                @elseif($book->status === 'rejected')
                                    <p class="mt-2 text-sm italic text-slate-400">Tidak ada keterangan.</p>
                                @else
                                    <p class="mt-2 text-sm text-slate-400">-</p>
                                @endif
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Aksi</p>
                                <div class="mt-3 flex flex-col gap-2 sm:flex-row">
                                    @if($book->status === 'pending')
                                        <button type="button"
                                                onclick="closeHistoryDetailModal('ormawa-history-detail-{{ $book->id_booking }}'); openOrmawaEditModal('ormawa-edit-modal-{{ $book->id_booking }}')"
                                                class="inline-flex h-10 flex-1 items-center justify-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-4 text-xs font-bold text-blue-600 transition hover:bg-blue-600 hover:text-white">
                                            <i class="fas fa-pen text-[10px]"></i> Edit Booking
                                        </button>
                                    @endif
                                    @if($book->status === 'rejected')
                                        <button type="button"
                                                onclick="closeHistoryDetailModal('ormawa-history-detail-{{ $book->id_booking }}'); reapplyBooking({{ json_encode([
                                                    'penanggung_jawab' => $book->penanggung_jawab,
                                                    'tanggal' => $book->tanggal,
                                                    'jam_mulai' => substr($book->jam_mulai, 0, 5),
                                                    'jam_selesai' => substr($book->jam_selesai, 0, 5),
                                                    'kapasitas' => $book->kapasitas,
                                                    'keperluan' => $book->keperluan
                                                ]) }})"
                                                class="inline-flex h-10 flex-1 items-center justify-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50 px-4 text-xs font-bold text-indigo-600 transition hover:bg-indigo-600 hover:text-white">
                                            <i class="fas fa-redo text-[10px]"></i> Ajukan Ulang
                                        </button>
                                    @endif
                                    @if($book->status !== 'approved')
                                        <form action="{{ route('ormawa.booking.delete', $book->id_booking) }}" method="POST" class="m-0 flex-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex h-10 w-full items-center justify-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-4 text-xs font-bold text-red-600 transition hover:bg-red-600 hover:text-white">
                                                <i class="fas fa-trash text-[10px]"></i> Hapus
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex h-10 flex-1 items-center justify-center gap-1 rounded-xl border border-slate-200 bg-slate-100 px-4 text-xs font-bold text-slate-400">
                                            <i class="fas fa-lock text-[10px]"></i> Terkunci
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end border-t border-slate-100 bg-slate-50 px-5 py-4">
                            <button type="button" onclick="closeHistoryDetailModal('ormawa-history-detail-{{ $book->id_booking }}')" class="inline-flex h-10 items-center justify-center rounded-xl bg-white px-5 text-sm font-bold text-slate-600 ring-1 ring-slate-200 transition hover:bg-slate-100">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>

                @if($book->status === 'pending')
                    <div id="ormawa-edit-modal-{{ $book->id_booking }}" class="fixed inset-0 z-[90] hidden items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
                        <div class="max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-2xl">
                            <div class="flex items-start justify-between gap-4 border-b border-slate-100 bg-slate-50 px-5 py-4">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-wider text-blue-500">Edit Booking Pending</p>
                                    <h3 class="mt-1 text-lg font-black text-slate-900">Ubah Pengajuan Ormawa</h3>
                                    <p class="mt-1 text-xs font-semibold text-slate-500">Perubahan akan langsung terlihat di halaman Approve Booking SPV.</p>
                                </div>
                                <button type="button" onclick="closeOrmawaEditModal('ormawa-edit-modal-{{ $book->id_booking }}')" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-500 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-100">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <form action="{{ route('ormawa.booking.update', $book->id_booking) }}" method="POST" enctype="multipart/form-data" class="ormawa-edit-form">
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Penanggung Jawab</label>
                                        <input type="text" name="penanggung_jawab" required value="{{ old('penanggung_jawab', $book->penanggung_jawab) }}"
                                               class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10">
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Tanggal Peminjaman</label>
                                        <input type="date" name="tanggal" required min="{{ date('Y-m-d') }}" value="{{ old('tanggal', $book->tanggal) }}"
                                               class="ormawa-edit-tanggal w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10">
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Jam Mulai</label>
                                        <input type="text" name="jam_mulai" maxlength="5" required value="{{ old('jam_mulai', substr($book->jam_mulai, 0, 5)) }}"
                                               class="ormawa-edit-jam-mulai time-formatter w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-center font-mono text-sm font-bold tracking-widest text-slate-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10">
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Jam Selesai</label>
                                        <input type="text" name="jam_selesai" maxlength="5" required value="{{ old('jam_selesai', substr($book->jam_selesai, 0, 5)) }}"
                                               class="ormawa-edit-jam-selesai time-formatter w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-center font-mono text-sm font-bold tracking-widest text-slate-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10">
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Jumlah Peserta</label>
                                        <input type="number" name="kapasitas" required min="1" value="{{ old('kapasitas', $book->kapasitas) }}"
                                               class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10">
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Jumlah Lab Dibutuhkan</label>
                                        <select name="jumlah_lab" required
                                                class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10">
                                            @for($i = 1; $i <= 11; $i++)
                                                <option value="{{ $i }}" {{ (int) old('jumlah_lab', $book->jumlah_lab) === $i ? 'selected' : '' }}>{{ $i }} Lab</option>
                                            @endfor
                                        </select>
                                        <p class="mt-1 text-[10px] font-bold text-blue-500">Sistem tetap memvalidasi kapasitas dan ketersediaan lab saat disimpan.</p>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Nama Acara & Kebutuhan Software</label>
                                        <input type="text" name="keperluan" required value="{{ old('keperluan', $book->keperluan) }}"
                                               class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10">
                                    </div>

                                    <div class="md:col-span-2 rounded-xl border-2 border-dashed border-blue-200 bg-blue-50/50 p-5">
                                        <label class="mb-2 block text-sm font-extrabold text-blue-700">
                                            <i class="fas fa-file-pdf mr-2 text-red-500"></i> Ganti Surat Peminjaman
                                        </label>
                                        <input type="file" name="file_surat" accept="application/pdf" onchange="checkFileExtensionPdf(this)"
                                               class="block w-full cursor-pointer text-sm text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-blue-100 file:px-4 file:py-2 file:text-sm file:font-bold file:text-blue-700 hover:file:bg-blue-200">
                                        <p class="mt-2 text-xs font-semibold text-slate-500">Opsional. Kosongkan jika tetap memakai surat PDF sebelumnya.</p>
                                    </div>
                                </div>

                                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end">
                                    <button type="button" onclick="closeOrmawaEditModal('ormawa-edit-modal-{{ $book->id_booking }}')" class="inline-flex h-11 items-center justify-center rounded-xl bg-white px-5 text-sm font-bold text-slate-600 ring-1 ring-slate-200 transition hover:bg-slate-100">
                                        Batal
                                    </button>
                                    <button type="submit" class="ormawa-edit-submit inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 text-sm font-black text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                                        <i class="fas fa-save"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif

        {{-- Footer --}}
        <div class="mt-8 text-center text-xs font-semibold text-slate-400">
            &copy; {{ date('Y') }} Laboratorium Komputer ICT. All rights reserved.
        </div>
    </main>

    {{-- Mesin Ketik JS --}}
    <script>
        // AJAX Lab Availability for Ormawa
        document.addEventListener("DOMContentLoaded", function () {
            const inputTanggal = document.getElementById('input_tanggal');
            const inputJamMulai = document.getElementById('input_jam_mulai');
            const inputJamSelesai = document.getElementById('input_jam_selesai');
            const selectJumlahLab = document.getElementById('select_jumlah_lab');

            let lastTriggerValues = '';

            function checkAvailableLabsCount() {
                const tanggal = inputTanggal.value;
                const jamMulai = inputJamMulai.value;
                const jamSelesai = inputJamSelesai.value;

                // Cek hari Minggu
                if (tanggal) {
                    const parts = tanggal.split('-');
                    if (parts.length === 3) {
                        const dateVal = new Date(parts[0], parts[1] - 1, parts[2]);
                        const day = dateVal.getDay();
                        if (day === 0) { // Sunday
                            showCustomAlert('Hari Minggu adalah hari libur. Tidak dapat melakukan reservasi.', 'Hari Libur');
                            inputTanggal.value = '';
                            selectJumlahLab.disabled = true;
                            selectJumlahLab.innerHTML = '<option value="">Isi Info Tanggal & Waktu Dahulu</option>';
                            selectJumlahLab.classList.replace('bg-slate-50', 'bg-slate-100');
                            selectJumlahLab.classList.remove('bg-red-100');
                            return;
                        }

                        // Validasi range waktu jika jamMulai dan jamSelesai diisi lengkap
                        if (jamMulai.length === 5 && jamSelesai.length === 5) {
                            if (jamMulai >= jamSelesai) {
                                showCustomAlert('Jam selesai harus lebih lambat dari jam mulai.', 'Waktu Tidak Valid');
                                selectJumlahLab.disabled = true;
                                selectJumlahLab.innerHTML = '<option value="">Jam Selesai Tidak Valid</option>';
                                return;
                            }

                            if (day === 6) { // Saturday
                                if (jamMulai < '07:10' || jamSelesai > '16:50') {
                                    showCustomAlert('Peminjaman hari Sabtu hanya diperbolehkan dari pukul 07:10 s/d 16:50.', 'Waktu Tidak Valid');
                                    selectJumlahLab.disabled = true;
                                    selectJumlahLab.innerHTML = '<option value="">Waktu Di Luar Batas Sabtu</option>';
                                    return;
                                }
                            } else { // Weekdays
                                if (jamMulai < '07:10' || jamSelesai > '18:55') {
                                    showCustomAlert('Peminjaman hari kerja hanya diperbolehkan dari pukul 07:10 s/d 18:55.', 'Waktu Tidak Valid');
                                    selectJumlahLab.disabled = true;
                                    selectJumlahLab.innerHTML = '<option value="">Waktu Di Luar Batas Hari Kerja</option>';
                                    return;
                                }
                            }
                        }
                    }
                }

                if (tanggal && jamMulai.length === 5 && jamSelesai.length === 5) {
                    const currentValues = `${tanggal}-${jamMulai}-${jamSelesai}`;
                    if (currentValues === lastTriggerValues) return;
                    lastTriggerValues = currentValues;

                    selectJumlahLab.disabled = true;
                    selectJumlahLab.innerHTML = '<option value="">⏳ Mencari Lab Tersedia...</option>';
                    selectJumlahLab.classList.replace('bg-slate-50', 'bg-slate-100');
                    selectJumlahLab.classList.add('cursor-not-allowed');

                    fetch('{{ route('ormawa.booking.check_available_labs_count') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            tanggal: tanggal,
                            jam_mulai: jamMulai,
                            jam_selesai: jamSelesai
                        })
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response error');
                        return response.json();
                    })
                    .then(data => {
                        selectJumlahLab.innerHTML = '';
                        const count = data.available_labs_count;

                        if (count > 0) {
                            for (let i = 1; i <= count; i++) {
                                let option = document.createElement('option');
                                option.value = i;
                                option.textContent = `${i} Lab`;
                                selectJumlahLab.appendChild(option);
                            }
                            selectJumlahLab.disabled = false;
                            selectJumlahLab.classList.replace('bg-slate-100', 'bg-slate-50');
                            selectJumlahLab.classList.remove('cursor-not-allowed', 'bg-red-100');
                        } else {
                            let option = document.createElement('option');
                            option.value = '';
                            option.textContent = '⚠️ Semua Lab Penuh/Bentrok';
                            selectJumlahLab.appendChild(option);
                            selectJumlahLab.disabled = true;
                            selectJumlahLab.classList.replace('bg-slate-50', 'bg-red-100');
                            selectJumlahLab.classList.replace('bg-slate-100', 'bg-red-100');
                        }
                    })
                    .catch(error => {
                        console.error('AJAX Error:', error);
                        selectJumlahLab.innerHTML = '<option value="">❌ Terjadi Kesalahan (Cek Console)</option>';
                    });
                } else {
                    selectJumlahLab.disabled = true;
                    selectJumlahLab.innerHTML = '<option value="">Isi Info Tanggal & Waktu Dahulu</option>';
                    selectJumlahLab.classList.replace('bg-slate-50', 'bg-slate-100');
                    selectJumlahLab.classList.remove('bg-red-100');
                }
            }

            [inputTanggal, inputJamMulai, inputJamSelesai].forEach(el => {
                ['input', 'change'].forEach(evt => {
                    el.addEventListener(evt, checkAvailableLabsCount);
                });
            });

            // Expose check function globally
            window.triggerCheckAvailableLabsCount = checkAvailableLabsCount;

            // Run check on load in case inputs are pre-filled
            checkAvailableLabsCount();
        });

        function reapplyBooking(data) {
            document.getElementById('input_penanggung_jawab').value = data.penanggung_jawab;
            document.getElementById('input_tanggal').value = data.tanggal;
            document.getElementById('input_jam_mulai').value = data.jam_mulai;
            document.getElementById('input_jam_selesai').value = data.jam_selesai;
            document.getElementById('input_kapasitas').value = data.kapasitas;
            document.getElementById('input_keperluan').value = data.keperluan;

            // Trigger check lab
            if (window.triggerCheckAvailableLabsCount) {
                window.triggerCheckAvailableLabsCount();
            }

            // Scroll smoothly to form container
            document.getElementById('booking-form').scrollIntoView({ behavior: 'smooth' });

            // Show a small notification or flash a banner to let user know
            showCustomAlert('Data pengajuan lama telah disalin ke formulir. Silakan sesuaikan tanggal/waktu dan unggah ulang surat resmi (.PDF) Anda.', 'Pengajuan Ulang');
        }

        function validateOrmawaBookingTime(tanggal, jamMulai, jamSelesai) {
            if (tanggal && jamMulai.length === 5 && jamSelesai.length === 5) {
                const parts = tanggal.split('-');
                if (parts.length === 3) {
                    const dateVal = new Date(parts[0], parts[1] - 1, parts[2]);
                    const day = dateVal.getDay();

                    if (jamMulai >= jamSelesai) {
                        showCustomAlert('Jam selesai harus lebih lambat dari jam mulai.', 'Waktu Tidak Valid');
                        return false;
                    }

                    if (day === 6) { // Saturday
                        if (jamMulai < '07:10' || jamSelesai > '16:50') {
                            showCustomAlert('Peminjaman hari Sabtu hanya diperbolehkan dari pukul 07:10 s/d 16:50.', 'Waktu Tidak Valid');
                            return false;
                        }
                    } else if (day === 0) { // Sunday
                        showCustomAlert('Hari Minggu libur.', 'Hari Libur');
                        return false;
                    } else { // Weekdays
                        if (jamMulai < '07:10' || jamSelesai > '18:55') {
                            showCustomAlert('Peminjaman hari kerja hanya diperbolehkan dari pukul 07:10 s/d 18:55.', 'Waktu Tidak Valid');
                            return false;
                        }
                    }
                }
            }

            return true;
        }

        document.getElementById('booking-form').addEventListener('submit', function(e) {
            const tanggal = document.getElementById('input_tanggal').value;
            const jamMulai = document.getElementById('input_jam_mulai').value;
            const jamSelesai = document.getElementById('input_jam_selesai').value;

            if (!validateOrmawaBookingTime(tanggal, jamMulai, jamSelesai)) {
                e.preventDefault();
                return;
            }

            const btn = document.getElementById('btn-submit');
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memproses Pengajuan...';
            btn.classList.add('opacity-70', 'pointer-events-none');
        });

        document.querySelectorAll('.ormawa-edit-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const tanggal = form.querySelector('.ormawa-edit-tanggal').value;
                const jamMulai = form.querySelector('.ormawa-edit-jam-mulai').value;
                const jamSelesai = form.querySelector('.ormawa-edit-jam-selesai').value;

                if (!validateOrmawaBookingTime(tanggal, jamMulai, jamSelesai)) {
                    e.preventDefault();
                    return;
                }

                const btn = form.querySelector('.ormawa-edit-submit');
                btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Menyimpan...';
                btn.classList.add('opacity-70', 'pointer-events-none');
            });
        });

        function checkFileExtensionPdf(input) {
            if (input.files.length > 0) {
                const file = input.files[0];
                const fileName = file.name.toLowerCase();

                if (!fileName.endsWith('.pdf')) {
                    showCustomAlert('File tidak valid! Tolong hanya masukkan file dengan format dokumen (.pdf).', 'Format File');
                    input.value = ''; // Kosongkan input file
                } else if (file.size > 2 * 1024 * 1024) {
                    showCustomAlert('Ukuran file terlalu besar! Maksimal ukuran file adalah 2MB.', 'Ukuran File');
                    input.value = ''; // Kosongkan input file
                }
            }
        }

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
                } else if (val.length > 0 && val.length < 5) {
                    showCustomAlert('Format jam kurang lengkap! Ketik 4 angka (contoh: 0800)', 'Format Jam');
                    e.target.value = '';
                }
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

        function openHistoryDetailModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeHistoryDetailModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        function openOrmawaEditModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeOrmawaEditModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        document.addEventListener('click', function (event) {
            if (event.target.classList && event.target.classList.contains('fixed') && event.target.id.startsWith('ormawa-history-detail-')) {
                closeHistoryDetailModal(event.target.id);
            }

            if (event.target.classList && event.target.classList.contains('fixed') && event.target.id.startsWith('ormawa-edit-modal-')) {
                closeOrmawaEditModal(event.target.id);
            }
        });
    </script>

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

</body>
</html>
