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
                <input type="hidden" name="lab" value="Menunggu SPV">

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
                            <input type="text" name="penanggung_jawab" required placeholder="Cth: Budi Santoso" value="{{ old('penanggung_jawab') }}"
                                   class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 pl-11 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                            <i class="fas fa-user-tie absolute left-4 top-3.5 text-slate-400"></i>
                        </div>
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Tanggal Peminjaman <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" required value="{{ old('tanggal') }}" min="{{ date('Y-m-d') }}"
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
                        <input type="text" name="jam_mulai" class="time-formatter w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-bold font-mono text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 text-center tracking-widest" placeholder="08:00" maxlength="5" required value="{{ old('jam_mulai') }}">
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Jam Selesai <span class="text-red-500">*</span></label>
                        <input type="text" name="jam_selesai" class="time-formatter w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-bold font-mono text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 text-center tracking-widest" placeholder="10:30" maxlength="5" required value="{{ old('jam_selesai') }}">
                    </div>

                    {{-- Kapasitas --}}
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Kapasitas (Jumlah Peserta) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="kapasitas" required placeholder="Cth: 30" min=1 value="{{ old('kapasitas') }}"
                                   class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 pl-12 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                            <i class="fas fa-chair absolute left-4 top-3.5 text-slate-400"></i>
                        </div>
                    </div>

                    {{-- Keperluan --}}
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Nama Acara & Kebutuhan Software <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" name="keperluan" required placeholder="Cth: Pelatihan Desain (Butuh Photoshop)" value="{{ old('keperluan') }}"
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
        <div class="mt-10 rounded-2xl border border-slate-200 bg-white p-6 md:p-8 shadow-xl shadow-slate-200/40">

            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-history text-indigo-500"></i> Riwayat Pengajuan Anda
                </h3>
            </div>

            <div class="overflow-x-auto custom-scrollbar rounded-xl border border-slate-200">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 text-xs font-extrabold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Tanggal & Hari</th>
                            <th class="px-6 py-4">Lab & Waktu</th>
                            <th class="px-6 py-4 w-full">Keperluan</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        {{-- 🌟 Pastikan MhsController mengirim data $myBookings --}}
                        @if(isset($myBookings) && $myBookings->count() > 0)
                            @foreach($myBookings as $book)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($book->tanggal)->translatedFormat('d F Y') }}</div>
                                    <div class="text-xs font-semibold text-slate-400 mt-0.5">{{ $book->hari }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-lg bg-slate-100 px-3 py-1 text-xs font-extrabold text-slate-600 border border-slate-200 mb-1">
                                        {{ $book->lab }}
                                    </span>
                                    <div class="font-mono text-xs font-bold text-slate-500">
                                        {{ substr($book->jam_mulai, 0, 5) }} - {{ substr($book->jam_selesai, 0, 5) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-700 whitespace-normal min-w-[200px]">
                                    {{ $book->keperluan }}
                                </td>
                                <td class="px-6 py-4 text-center">
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
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
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

        {{-- Footer --}}
        <div class="mt-8 text-center text-xs font-semibold text-slate-400">
            &copy; {{ date('Y') }} Laboratorium Komputer ICT. All rights reserved.
        </div>
    </main>

    {{-- Mesin Ketik JS --}}
    <script>
        document.getElementById('booking-form').addEventListener('submit', function() {
            const btn = document.getElementById('btn-submit');
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memproses Pengajuan...';
            btn.classList.add('opacity-70', 'pointer-events-none');
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
