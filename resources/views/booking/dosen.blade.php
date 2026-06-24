<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Dosen - Booking Lab ICT</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                <i class="fas fa-chalkboard-teacher text-2xl"></i>
                <span>LabSystem <span class="text-slate-400 font-medium">| Dosen Portal</span></span>
            </div>

            {{-- 🌟 LOGIC: Profil User Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2.5 text-sm font-bold text-slate-700 bg-slate-50 px-2 py-1.5 pr-3.5 sm:pr-4 rounded-full border border-slate-200 hover:bg-slate-100 transition focus:outline-none">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-100 text-xs font-black text-indigo-700">
                        {{ collect(explode(' ', auth()->user()->name))->map(fn($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('') }}
                    </span>
                    <span class="hidden sm:inline text-slate-600">{{ auth()->user()->name ?? 'Dosen' }}</span>
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

        <div class="rounded-2xl border border-slate-200 bg-white p-6 md:p-8 shadow-xl shadow-slate-200/40">
            <div class="mb-8 border-b border-slate-100 pb-5">
                <h2 class="text-xl font-extrabold text-slate-900 md:text-2xl">
                    <i class="fas fa-calendar-check mr-2 text-indigo-500"></i> Reservasi Laboratorium
                </h2>
                <p class="mt-2 text-sm font-medium text-slate-500">
                    Masukkan Tanggal, Jam, dan SKS terlebih dahulu. Sistem akan otomatis mencarikan Lab yang kosong.
                </p>
            </div>

            @if(session('success'))
                <div class="mb-6 rounded-xl bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 border border-emerald-200 flex items-center gap-3">
                    <i class="fas fa-check-circle text-emerald-500 text-xl"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 rounded-xl bg-red-50 px-5 py-4 text-sm font-bold text-red-700 border border-red-200 flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i> {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 px-5 py-4 text-sm font-bold text-red-700 border border-red-200">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                        <span>Gagal Mengirim Reservasi:</span>
                    </div>
                    <ul class="list-disc pl-8 text-xs font-medium text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('dosen.booking.store') }}" method="POST" id="booking-form">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

                    {{-- 🌟 LOGIC: Nama Dosen Terkunci & Otomatis Terisi --}}
                    <div class="md:col-span-12">
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Nama Lengkap Dosen</label>
                        <div class="relative">
                            <input type="text" name="nm_dosen" required readonly value="{{ auth()->user()->name }}"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-100 py-3 px-4 pl-11 text-sm font-bold text-slate-500 outline-none cursor-not-allowed">
                            <i class="fas fa-user-tie absolute left-4 top-3.5 text-slate-400"></i>
                        </div>
                        <p class="text-[10px] font-bold text-indigo-500 mt-1">*Diambil otomatis dari akun Anda.</p>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" id="input_tanggal" required value="{{ old('tanggal') }}" min="{{ date('Y-m-d') }}"
                               class="trigger-ajax w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Jam Mulai <span class="text-red-500">*</span></label>
                        <select name="jam_mulai" id="input_jam" required class="trigger-ajax w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 cursor-pointer">
                            <option value="">-- Pilih Tanggal Dahulu --</option>
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Jumlah SKS <span class="text-red-500">*</span></label>
                        <select name="sks" id="input_sks" required class="trigger-ajax w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                            <option value="">-- Pilih SKS --</option>
                            <option value="1">1 SKS (50 Menit)</option>
                            <option value="2">2 SKS (105 Menit)</option>
                            <option value="3">3 SKS (160 Menit)</option>
                            <option value="4">4 SKS (215 Menit)</option>
                        </select>
                    </div>

                    <div class="md:col-span-12 text-sm font-semibold text-indigo-600 hidden" id="info_jam_selesai">
                        <i class="fas fa-info-circle mr-1"></i> Jam Selesai diperkirakan pada pukul: <span id="text_jam_selesai" class="font-bold font-mono border-b border-indigo-600">--:--</span> WIB
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Jumlah Mahasiswa <span class="text-red-500">*</span></label>
                        <input type="number" name="kapasitas" id="input_kapasitas" required placeholder="Cth: 36" value="{{ old('kapasitas') }}"
                               class="trigger-ajax w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                    </div>

                    <div class="md:col-span-5">
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Nama Acara / Keperluan Matkul <span class="text-red-500">*</span></label>
                        <input type="text" name="keperluan" required placeholder="Cth: Ujian Pemrograman Web" value="{{ old('keperluan') }}"
                               class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                    </div>

                    <div class="md:col-span-3">
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Kode Matkul <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_matkul" required placeholder="Cth: AA atau IF12" maxlength="4" value="{{ old('kode_matkul') }}"
                               class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 uppercase"
                               oninput="this.value = this.value.toUpperCase()">
                    </div>

                    <div class="md:col-span-12 rounded-xl border-2 border-dashed border-indigo-200 bg-indigo-50/50 p-6 transition">
                        <label class="mb-3 block text-sm font-extrabold text-indigo-700">
                            <i class="fas fa-door-open mr-2"></i> Pilih Laboratorium Tersedia <span class="text-red-500">*</span>
                        </label>
                        <select name="id_lab" id="select_lab" required disabled class="w-full rounded-xl border border-slate-300 bg-slate-200 py-3 px-4 text-sm font-bold text-slate-500 outline-none cursor-not-allowed transition">
                            <option value="">Isi Tanggal, Jam, SKS, & Kapasitas Dahulu</option>
                        </select>
                    </div>

                    <div class="md:col-span-12 rounded-xl border border-emerald-200 bg-emerald-50 p-5 hidden transition-all duration-300 shadow-sm" id="info_fasilitas">
                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-emerald-800 mb-1">
                            <i class="fas fa-tools mr-1"></i> Spesifikasi & Fasilitas Ruangan:
                        </h4>
                        <p class="text-sm font-bold text-emerald-950" id="text_fasilitas">---</p>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" id="btn-submit" class="inline-flex w-full md:w-auto justify-center items-center gap-2 rounded-xl bg-indigo-600 px-8 py-3.5 text-sm font-black text-white uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition hover:bg-indigo-700 hover:-translate-y-0.5">
                        <i class="fas fa-paper-plane"></i> Kirim Reservasi
                    </button>
                </div>
            </form>
        </div>

        {{-- TABEL RIWAYAT DOSEN --}}
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
                            <th class="px-6 py-4 w-full">Keperluan / Matkul</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 min-w-[200px]">Alasan Penolakan</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @if(isset($myBookings) && $myBookings->count() > 0)
                            @foreach($myBookings as $book)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($book->tanggal)->translatedFormat('d F Y') }}</div>
                                    <div class="text-xs font-semibold text-slate-400 mt-0.5">{{ $book->hari }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-lg bg-slate-100 px-3 py-1 text-xs font-extrabold text-slate-600 border border-slate-200 mb-1">
                                        {{ $book->lab->nama_lab ?? $book->lab->nm_lab ?? 'Lab Terhapus' }}
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
                                {{-- Alasan Penolakan --}}
                                <td class="px-6 py-4 whitespace-normal min-w-[200px]">
                                    @if($book->status === 'rejected' && !empty($book->alasan_penolakan))
                                        <div class="rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-xs font-semibold text-red-700">
                                            <i class="fas fa-comment-slash mr-1 text-red-400"></i>
                                            {{ $book->alasan_penolakan }}
                                        </div>
                                    @elseif($book->status === 'rejected')
                                        <span class="text-xs text-slate-400 italic">Tidak ada keterangan.</span>
                                    @else
                                        <span class="text-xs text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($book->status !== 'approved')
                                            <button type="button"
                                                    onclick="openEditModal({{ json_encode([
                                                        'id' => $book->id_booking,
                                                        'tanggal' => $book->tanggal,
                                                        'jam_mulai' => substr($book->jam_mulai, 0, 5),
                                                        'sks' => $book->sks,
                                                        'kapasitas' => $book->kapasitas,
                                                        'keperluan' => $book->keperluan,
                                                        'id_lab' => $book->id_lab
                                                    ]) }})"
                                                    class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-600 border border-indigo-200 transition duration-200 hover:bg-indigo-600 hover:text-white hover:scale-105 transform">
                                                <i class="fas fa-edit text-[10px]"></i> Edit
                                            </button>

                                            <form action="{{ route('dosen.booking.delete', $book->id_booking) }}" method="POST" class="m-0 inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus reservasi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 border border-red-200 transition duration-200 hover:bg-red-600 hover:text-white hover:scale-105 transform">
                                                    <i class="fas fa-trash text-[10px]"></i> Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-400 border border-slate-200">
                                                <i class="fas fa-lock text-[9px]"></i> Terkunci
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-clipboard-check text-4xl mb-3 text-slate-300"></i>
                                        <span class="font-bold">Anda belum pernah melakukan reservasi.</span>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 text-center text-xs font-semibold text-slate-400 pb-10">
            &copy; {{ date('Y') }} Laboratorium Komputer ICT. All rights reserved.
        </div>
    </main>

    <script>
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
            { start: '18:45', label: '18:45 (Kelas Karyawan)' }
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

        function updateJamOptions(dateInput, jamSelect, placeholderText = "-- Pilih Jam Mulai --") {
            const dateVal = dateInput.value;
            const day = getDayOfWeek(dateVal);
            
            jamSelect.innerHTML = '';
            
            if (day === null) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = '-- Pilih Tanggal Dahulu --';
                jamSelect.appendChild(opt);
                return;
            }
            
            if (day === 0) { // Sunday
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = 'Hari Minggu Libur';
                jamSelect.appendChild(opt);
                showCustomAlert('Hari Minggu adalah hari libur. Tidak dapat melakukan reservasi.', 'Hari Libur');
                dateInput.value = '';
                return;
            }
            
            const optPlaceholder = document.createElement('option');
            optPlaceholder.value = '';
            optPlaceholder.textContent = placeholderText;
            jamSelect.appendChild(optPlaceholder);
            
            const slots = (day === 6) ? saturdaySlots : weekdaySlots;
            slots.forEach(slot => {
                const opt = document.createElement('option');
                opt.value = slot.start;
                opt.textContent = slot.label;
                jamSelect.appendChild(opt);
            });
        }

        function updateSksOptions(dateInput, jamSelect, sksSelect) {
            const dateVal = dateInput.value;
            const day = getDayOfWeek(dateVal);
            const selectedJam = jamSelect.value;
            
            // Simpan value SKS yang terpilih saat ini
            const currentSelectedSks = sksSelect.value;
            
            sksSelect.innerHTML = '';
            
            if (!dateVal || !selectedJam) {
                const optPlaceholder = document.createElement('option');
                optPlaceholder.value = '';
                optPlaceholder.textContent = '-- Pilih SKS --';
                sksSelect.appendChild(optPlaceholder);
                return;
            }
            
            if (day === 0) { // Sunday
                const optPlaceholder = document.createElement('option');
                optPlaceholder.value = '';
                optPlaceholder.textContent = '-- Hari Minggu Libur --';
                sksSelect.appendChild(optPlaceholder);
                return;
            }
            
            if (day === 6) { // Saturday
                // Hari sabtu bebas pilih 1-4 SKS
                const optPlaceholder = document.createElement('option');
                optPlaceholder.value = '';
                optPlaceholder.textContent = '-- Pilih SKS --';
                sksSelect.appendChild(optPlaceholder);
                
                const sksLabels = {
                    1: '1 SKS (50 Menit)',
                    2: '2 SKS (105 Menit)',
                    3: '3 SKS (160 Menit)',
                    4: '4 SKS (215 Menit)'
                };
                for (let i = 1; i <= 4; i++) {
                    const opt = document.createElement('option');
                    opt.value = i;
                    opt.textContent = sksLabels[i];
                    sksSelect.appendChild(opt);
                }
                
                // Kembalikan value lama jika ada dan masih valid
                if (currentSelectedSks && currentSelectedSks >= 1 && currentSelectedSks <= 4) {
                    sksSelect.value = currentSelectedSks;
                }
                return;
            }
            
            // Weekdays (Senin-Jumat)
            if (selectedJam === '18:45') {
                // Kelas Karyawan, hanya boleh 2 SKS dan tidak bisa pilih yang lain
                const opt = document.createElement('option');
                opt.value = '2';
                opt.textContent = '2 SKS (Kelas Karyawan)';
                sksSelect.appendChild(opt);
                sksSelect.value = '2';
                return;
            }
            
            const weekdayStarts = ['07:10', '08:00', '08:55', '09:45', '10:40', '11:35', '12:30', '13:25', '14:20', '15:15', '16:10', '17:05', '18:00'];
            const idx = weekdayStarts.indexOf(selectedJam);
            
            const optPlaceholder = document.createElement('option');
            optPlaceholder.value = '';
            optPlaceholder.textContent = '-- Pilih SKS --';
            sksSelect.appendChild(optPlaceholder);
            
            if (idx !== -1) {
                const maxSks = Math.min(4, 13 - idx);
                const sksLabels = {
                    1: '1 SKS (50 Menit)',
                    2: '2 SKS (105 Menit)',
                    3: '3 SKS (160 Menit)',
                    4: '4 SKS (215 Menit)'
                };
                
                for (let i = 1; i <= maxSks; i++) {
                    const opt = document.createElement('option');
                    opt.value = i;
                    opt.textContent = sksLabels[i];
                    sksSelect.appendChild(opt);
                }
                
                // Kembalikan value lama jika masih dalam batas maxSks
                if (currentSelectedSks && parseInt(currentSelectedSks) <= maxSks) {
                    sksSelect.value = currentSelectedSks;
                }
            }
        }

        const inputTanggal = document.getElementById('input_tanggal');
        const inputJam = document.getElementById('input_jam');
        const inputSks = document.getElementById('input_sks');
        const inputKapasitas = document.getElementById('input_kapasitas');
        const selectLab = document.getElementById('select_lab');
        const infoJamSelesai = document.getElementById('info_jam_selesai');
        const textJamSelesai = document.getElementById('text_jam_selesai');
        const infoFasilitas = document.getElementById('info_fasilitas');
        const textFasilitas = document.getElementById('text_fasilitas');

        inputTanggal.addEventListener('change', function() {
            updateJamOptions(inputTanggal, inputJam);
            updateSksOptions(inputTanggal, inputJam, inputSks);
        });

        inputJam.addEventListener('change', function() {
            updateSksOptions(inputTanggal, inputJam, inputSks);
        });

        const triggers = document.querySelectorAll('.trigger-ajax');
        let lastCreateTriggerValues = '';
        triggers.forEach(el => {
            ['input', 'change'].forEach(evt => {
                el.addEventListener(evt, function() {
                    const currentValues = `${inputTanggal.value}-${inputJam.value}-${inputSks.value}-${inputKapasitas.value}`;
                    if (currentValues === lastCreateTriggerValues) return;

                    if(inputTanggal.value && inputJam.value && inputSks.value && inputKapasitas.value) {
                        lastCreateTriggerValues = currentValues;
                        selectLab.disabled = true;
                        selectLab.innerHTML = '<option value="">⏳ Mencari Lab yang Sesuai...</option>';
                        selectLab.classList.replace('bg-white', 'bg-slate-200');
                        infoFasilitas.classList.add('hidden');

                        // ⚠️ Pastikan nama route ini ADA di web.php lu
                        fetch('{{ route('dosen.booking.check_labs') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                tanggal: inputTanggal.value,
                                jam_mulai: inputJam.value,
                                sks: inputSks.value,
                                kapasitas: inputKapasitas.value
                            })
                        })
                        .then(async response => {
                            if (!response.ok) {
                                const err = await response.text();
                                console.error('Server Error Response:', err);
                                throw new Error('Server merespon dengan status ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            infoJamSelesai.classList.remove('hidden');
                            textJamSelesai.textContent = data.jam_selesai;

                            selectLab.innerHTML = '<option value="">-- Pilih Laboratorium --</option>';
                            let adaKosong = false;

                            data.labs.forEach(lab => {
                                let option = document.createElement('option');
                                option.value = lab.id_lab;
                                option.setAttribute('data-fasilitas', lab.fasilitas);

                                if (lab.is_busy) {
                                    option.disabled = true;
                                    option.textContent = `🔴 ${lab.nama_lab} (Penuh/Bentrok)`;
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
                                selectLab.innerHTML = '<option value="">⚠️ Semua Lab Penuh/Kapasitas Kurang</option>';
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
                        selectLab.innerHTML = '<option value="">Isi Tanggal, Jam, SKS, & Kapasitas Dahulu</option>';
                        selectLab.classList.replace('bg-white', 'bg-slate-200');
                        infoJamSelesai.classList.add('hidden');
                        infoFasilitas.classList.add('hidden');
                    }
                });
            });
        });

        selectLab.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const fasilitasText = selectedOption.getAttribute('data-fasilitas');
            if (this.value && fasilitasText) {
                infoFasilitas.classList.remove('hidden');
                textFasilitas.innerHTML = `<i class="text-emerald-600 fas fa-check-double mr-1"></i> ${fasilitasText}`;
            } else {
                infoFasilitas.classList.add('hidden');
            }
        });

        document.getElementById('booking-form').addEventListener('submit', function() {
            const btn = document.getElementById('btn-submit');
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Menyimpan...';
            btn.classList.add('opacity-70', 'pointer-events-none');
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

        // --- EDIT BOOKING MODAL FUNCTIONS ---
        let editOriginalLabId = null;
        let lastEditTriggerValues = '';

        function openEditModal(data) {
            let keperluanText = data.keperluan;
            let kodeMatkul = '';

            const match = keperluanText.match(/\s\(([^)]+)\)$/);
            if (match) {
                kodeMatkul = match[1];
                keperluanText = keperluanText.replace(/\s\(([^)]+)\)$/, '');
            }

            editOriginalLabId = data.id_lab;
            lastEditTriggerValues = `${data.tanggal}-${data.jam_mulai}-${data.sks}-${data.kapasitas}`;

            document.getElementById('edit_booking_id').value = data.id;
            document.getElementById('edit_input_tanggal').value = data.tanggal;
            
            const editTanggalInput = document.getElementById('edit_input_tanggal');
            const editJamSelect = document.getElementById('edit_input_jam');
            const editSksSelect = document.getElementById('edit_input_sks');
            
            updateJamOptions(editTanggalInput, editJamSelect);
            editJamSelect.value = data.jam_mulai;
            
            updateSksOptions(editTanggalInput, editJamSelect, editSksSelect);
            editSksSelect.value = data.sks;
            document.getElementById('edit_input_kapasitas').value = data.kapasitas;
            document.getElementById('edit_input_keperluan').value = keperluanText;
            document.getElementById('edit_input_kode_matkul').value = kodeMatkul;

            const form = document.getElementById('edit-booking-form');
            form.action = `/dosen/booking/update/${data.id}`;

            const modal = document.getElementById('edit-booking-modal');
            const box = document.getElementById('edit-booking-box');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');
                box.classList.remove('scale-95');
                box.classList.add('scale-100');
            }, 10);

            checkEditAvailableLabs(data.id_lab);
        }

        function closeEditModal() {
            const modal = document.getElementById('edit-booking-modal');
            const box = document.getElementById('edit-booking-box');

            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            box.classList.remove('scale-100');
            box.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }

        function checkEditAvailableLabs(selectedLabId = null) {
            const editTanggal = document.getElementById('edit_input_tanggal').value;
            const editJam = document.getElementById('edit_input_jam').value;
            const editSks = document.getElementById('edit_input_sks').value;
            const editKapasitas = document.getElementById('edit_input_kapasitas').value;
            const selectEditLab = document.getElementById('select_edit_lab');
            const infoEditJamSelesai = document.getElementById('info_edit_jam_selesai');
            const textEditJamSelesai = document.getElementById('text_edit_jam_selesai');
            const infoEditFasilitas = document.getElementById('info_edit_fasilitas');
            const textEditFasilitas = document.getElementById('text_edit_fasilitas');

            if (selectedLabId === null) {
                selectedLabId = editOriginalLabId;
            } else {
                editOriginalLabId = selectedLabId;
            }

            if (editTanggal && editJam && editSks && editKapasitas) {
                selectEditLab.disabled = true;
                selectEditLab.innerHTML = '<option value="">⏳ Mencari Lab yang Sesuai...</option>';
                selectEditLab.classList.replace('bg-white', 'bg-slate-200');
                infoEditFasilitas.classList.add('hidden');

                fetch('{{ route('dosen.booking.check_labs') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        tanggal: editTanggal,
                        jam_mulai: editJam,
                        sks: editSks,
                        kapasitas: editKapasitas
                    })
                })
                .then(response => response.json())
                .then(data => {
                    infoEditJamSelesai.classList.remove('hidden');
                    textEditJamSelesai.textContent = data.jam_selesai;

                    selectEditLab.innerHTML = '<option value="">-- Pilih Laboratorium --</option>';
                    let adaKosong = false;

                    data.labs.forEach(lab => {
                        let option = document.createElement('option');
                        option.value = lab.id_lab;
                        option.setAttribute('data-fasilitas', lab.fasilitas);

                        const isCurrentLab = selectedLabId && parseInt(lab.id_lab) === parseInt(selectedLabId);

                        if (lab.is_busy) {
                            option.disabled = true;
                            option.textContent = `🔴 ${lab.nama_lab} (Penuh/Bentrok)`;
                            option.style.color = '#ef4444';
                            option.style.fontWeight = 'bold';
                        } else {
                            option.textContent = isCurrentLab ? `⭐ ${lab.nama_lab} (Lab Anda Saat Ini)` : `🟢 ${lab.nama_lab} (Tersedia)`;
                            option.style.color = '#10b981';
                            option.style.fontWeight = 'bold';
                            adaKosong = true;
                            if (isCurrentLab) {
                                option.selected = true;
                            }
                        }
                        selectEditLab.appendChild(option);
                    });

                    selectEditLab.disabled = false;
                    selectEditLab.classList.replace('bg-slate-200', 'bg-white');
                    selectEditLab.classList.remove('cursor-not-allowed');

                    if (!adaKosong) {
                        selectEditLab.innerHTML = '<option value="">⚠️ Semua Lab Penuh/Kapasitas Kurang</option>';
                        selectEditLab.disabled = true;
                        selectEditLab.classList.replace('bg-white', 'bg-red-100');
                    }

                    selectEditLab.dispatchEvent(new Event('change'));
                })
                .catch(error => {
                    console.error('AJAX Error:', error);
                    selectEditLab.innerHTML = '<option value="">❌ Terjadi Kesalahan (Cek Console)</option>';
                });
            } else {
                selectEditLab.disabled = true;
                selectEditLab.innerHTML = '<option value="">Isi Tanggal, Jam, SKS, & Kapasitas Dahulu</option>';
                selectEditLab.classList.replace('bg-white', 'bg-slate-200');
                infoEditJamSelesai.classList.add('hidden');
                infoEditFasilitas.classList.add('hidden');
            }
        }

        document.getElementById('edit_input_tanggal').addEventListener('change', function() {
            updateJamOptions(this, document.getElementById('edit_input_jam'));
            updateSksOptions(this, document.getElementById('edit_input_jam'), document.getElementById('edit_input_sks'));
        });

        document.getElementById('edit_input_jam').addEventListener('change', function() {
            updateSksOptions(document.getElementById('edit_input_tanggal'), this, document.getElementById('edit_input_sks'));
        });

        const editTriggers = document.querySelectorAll('.trigger-edit-ajax');
        editTriggers.forEach(el => {
            ['input', 'change'].forEach(evt => {
                el.addEventListener(evt, function() {
                    const editTanggal = document.getElementById('edit_input_tanggal').value;
                    const editJam = document.getElementById('edit_input_jam').value;
                    const editSks = document.getElementById('edit_input_sks').value;
                    const editKapasitas = document.getElementById('edit_input_kapasitas').value;

                    const currentValues = `${editTanggal}-${editJam}-${editSks}-${editKapasitas}`;
                    if (currentValues === lastEditTriggerValues) return;

                    if (editTanggal && editJam && editSks && editKapasitas) {
                        lastEditTriggerValues = currentValues;
                        checkEditAvailableLabs();
                    }
                });
            });
        });

        const selectEditLab = document.getElementById('select_edit_lab');
        selectEditLab.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const infoEditFasilitas = document.getElementById('info_edit_fasilitas');
            const textEditFasilitas = document.getElementById('text_edit_fasilitas');

            if (selectedOption) {
                const fasilitasText = selectedOption.getAttribute('data-fasilitas');
                if (this.value && fasilitasText) {
                    infoEditFasilitas.classList.remove('hidden');
                    textEditFasilitas.innerHTML = `<i class="text-emerald-600 fas fa-check-double mr-1"></i> ${fasilitasText}`;
                } else {
                    infoEditFasilitas.classList.add('hidden');
                }
            } else {
                infoEditFasilitas.classList.add('hidden');
            }
        });

        document.getElementById('edit-booking-form').addEventListener('submit', function() {
            const btn = document.getElementById('btn-edit-submit');
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Menyimpan...';
            btn.classList.add('opacity-70', 'pointer-events-none');
        });
    </script>

    {{-- Edit Booking Modal --}}
    <div id="edit-booking-modal" class="fixed inset-0 z-[90] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" style="transition: opacity 0.3s ease;">
        <div class="relative w-full max-w-2xl rounded-2xl bg-white p-6 md:p-8 shadow-2xl transform transition-transform scale-95 max-h-[90vh] overflow-y-auto" id="edit-booking-box" style="transition: transform 0.3s ease;">
            <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-4">
                <h3 class="text-lg font-extrabold text-slate-800"><i class="fas fa-edit text-indigo-500 mr-2"></i> Edit Reservasi</h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="" method="POST" id="edit-booking-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="booking_id" id="edit_booking_id">

                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    {{-- Nama Dosen (Terkunci & Otomatis) --}}
                    <div class="md:col-span-12">
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Nama Lengkap Dosen</label>
                        <div class="relative">
                            <input type="text" name="nm_dosen" required readonly value="{{ auth()->user()->name }}"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-100 py-2.5 px-4 pl-11 text-sm font-bold text-slate-500 outline-none cursor-not-allowed">
                            <i class="fas fa-user-tie absolute left-4 top-3.5 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="md:col-span-6">
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Tanggal <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="date" name="tanggal" id="edit_input_tanggal" required min="{{ date('Y-m-d') }}"
                                   class="trigger-edit-ajax w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 px-4 pl-11 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                            <i class="fas fa-calendar-alt absolute left-4 top-3.5 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="md:col-span-6">
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Jam Mulai <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="jam_mulai" id="edit_input_jam" required class="trigger-edit-ajax w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 px-4 pl-11 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 cursor-pointer">
                                <option value="">-- Pilih Tanggal Dahulu --</option>
                            </select>
                            <i class="fas fa-clock absolute left-4 top-3.5 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="md:col-span-6">
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Jumlah SKS <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="sks" id="edit_input_sks" required class="trigger-edit-ajax w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 px-4 pl-11 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                                <option value="">-- Pilih SKS --</option>
                                <option value="1">1 SKS (50 Menit)</option>
                                <option value="2">2 SKS (105 Menit)</option>
                                <option value="3">3 SKS (160 Menit)</option>
                                <option value="4">4 SKS (215 Menit)</option>
                            </select>
                            <i class="fas fa-graduation-cap absolute left-4 top-3.5 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="md:col-span-6">
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Kapasitas Mahasiswa <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="kapasitas" id="edit_input_kapasitas" required placeholder="Cth: 40"
                                   class="trigger-edit-ajax w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 px-4 pl-11 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                            <i class="fas fa-users absolute left-4 top-3.5 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="md:col-span-12 text-sm font-semibold text-indigo-600 hidden" id="info_edit_jam_selesai">
                        <i class="fas fa-info-circle mr-1"></i> Jam Selesai diperkirakan pada pukul: <span id="text_edit_jam_selesai" class="font-bold font-mono border-b border-indigo-600">--:--</span> WIB
                    </div>

                    <div class="md:col-span-8">
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Nama Acara / Keperluan Matkul <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" name="keperluan" id="edit_input_keperluan" required placeholder="Cth: Ujian Pemrograman Web"
                                   class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 px-4 pl-11 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                            <i class="fas fa-book-open absolute left-4 top-3.5 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Kode Matkul <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" name="kode_matkul" id="edit_input_kode_matkul" required placeholder="Cth: AA" maxlength="4"
                                   class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 px-4 pl-11 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 uppercase"
                                   oninput="this.value = this.value.toUpperCase()">
                            <i class="fas fa-barcode absolute left-4 top-3.5 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="md:col-span-12 rounded-xl border-2 border-dashed border-indigo-200 bg-indigo-50/50 p-5 transition">
                        <label class="mb-2 block text-sm font-extrabold text-indigo-700">
                            <i class="fas fa-door-open mr-2"></i> Pilih Laboratorium Tersedia <span class="text-red-500">*</span>
                        </label>
                        <select name="id_lab" id="select_edit_lab" required disabled class="w-full rounded-xl border border-slate-300 bg-slate-200 py-2.5 px-4 text-sm font-bold text-slate-500 outline-none cursor-not-allowed transition">
                            <option value="">Isi Tanggal, Jam, SKS, & Kapasitas Dahulu</option>
                        </select>
                    </div>

                    <div class="md:col-span-12 rounded-xl border border-emerald-200 bg-emerald-50 p-4 hidden transition-all duration-300 shadow-sm" id="info_edit_fasilitas">
                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-emerald-800 mb-1">
                            <i class="fas fa-tools mr-1"></i> Spesifikasi & Fasilitas Ruangan:
                        </h4>
                        <p class="text-sm font-bold text-emerald-950" id="text_edit_fasilitas">---</p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                    <button type="button" onclick="closeEditModal()" class="rounded-xl border border-slate-250 bg-slate-100 hover:bg-slate-200 text-slate-700 py-3 px-6 text-sm font-bold shadow-sm transition">
                        Batal
                    </button>
                    <button type="submit" id="btn-edit-submit" class="rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-6 text-sm font-black uppercase tracking-wider shadow-lg shadow-indigo-600/20 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
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
</body>
</html>
