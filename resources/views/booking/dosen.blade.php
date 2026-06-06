<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Dosen - Booking Lab ICT</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    
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
            
            {{-- 🌟 LOGIC: Profil User & Tombol Logout --}}
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center gap-2 text-sm font-bold text-slate-600 bg-slate-100 px-4 py-2 rounded-full border border-slate-200">
                    <i class="fas fa-user-circle text-indigo-500 text-lg"></i>
                    <span>{{ auth()->user()->name ?? 'Dosen' }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 rounded-lg bg-red-50 px-4 py-2 text-sm font-bold text-red-600 transition hover:bg-red-100 hover:text-red-700 focus:outline-none">
                        <i class="fas fa-sign-out-alt"></i> <span class="hidden sm:inline">Logout</span>
                    </button>
                </form>
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
                        <input type="date" name="tanggal" id="input_tanggal" required value="{{ old('tanggal') }}" 
                               class="trigger-ajax w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Jam Mulai <span class="text-red-500">*</span></label>
                        <input type="text" name="jam_mulai" id="input_jam" class="time-formatter trigger-ajax w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-bold font-mono text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 text-center tracking-widest" placeholder="08:00" maxlength="5" required>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Jumlah SKS <span class="text-red-500">*</span></label>
                        <select name="sks" id="input_sks" required class="trigger-ajax w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                            <option value="">-- Pilih SKS --</option>
                            <option value="1">1 SKS (50 Menit)</option>
                            <option value="2">2 SKS (100 Menit)</option>
                            <option value="3">3 SKS (150 Menit)</option>
                            <option value="4">4 SKS (200 Menit)</option>
                        </select>
                    </div>

                    <div class="md:col-span-12 text-sm font-semibold text-indigo-600 hidden" id="info_jam_selesai">
                        <i class="fas fa-info-circle mr-1"></i> Jam Selesai diperkirakan pada pukul: <span id="text_jam_selesai" class="font-bold font-mono border-b border-indigo-600">--:--</span> WIB
                    </div>

                    <div class="md:col-span-12 rounded-xl border-2 border-dashed border-indigo-200 bg-indigo-50/50 p-6 transition">
                        <label class="mb-3 block text-sm font-extrabold text-indigo-700">
                            <i class="fas fa-door-open mr-2"></i> Pilih Laboratorium Tersedia <span class="text-red-500">*</span>
                        </label>
                        <select name="id_lab" id="select_lab" required disabled class="w-full rounded-xl border border-slate-300 bg-slate-200 py-3 px-4 text-sm font-bold text-slate-500 outline-none cursor-not-allowed transition">
                            <option value="">Kunci Terbuka Jika Tanggal, Jam, & SKS Terisi</option>
                        </select>
                    </div>

                    <div class="md:col-span-12 rounded-xl border border-emerald-200 bg-emerald-50 p-5 hidden transition-all duration-300 shadow-sm" id="info_fasilitas">
                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-emerald-800 mb-1">
                            <i class="fas fa-tools mr-1"></i> Spesifikasi & Fasilitas Ruangan:
                        </h4>
                        <p class="text-sm font-bold text-emerald-950" id="text_fasilitas">---</p>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Kapasitas Mahasiswa <span class="text-red-500">*</span></label>
                        <input type="number" name="kapasitas" required placeholder="Cth: 40" 
                               class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                    </div>
                    
                    <div class="md:col-span-8">
                        <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Nama Acara / Keperluan Matkul <span class="text-red-500">*</span></label>
                        <input type="text" name="keperluan" required placeholder="Cth: Ujian Pemrograman Web" 
                               class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
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
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
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
                    alert('Format jam tidak valid. Ketik 4 angka (contoh: 0800)');
                    e.target.value = '';
                }
            }
        });

        const inputTanggal = document.getElementById('input_tanggal');
        const inputJam = document.getElementById('input_jam');
        const inputSks = document.getElementById('input_sks');
        const selectLab = document.getElementById('select_lab');
        const infoJamSelesai = document.getElementById('info_jam_selesai');
        const textJamSelesai = document.getElementById('text_jam_selesai');
        const infoFasilitas = document.getElementById('info_fasilitas');
        const textFasilitas = document.getElementById('text_fasilitas');
        const triggers = document.querySelectorAll('.trigger-ajax');

        triggers.forEach(el => {
            el.addEventListener('change', function() {
                if(inputTanggal.value && inputJam.value.length === 5 && inputSks.value) {
                    selectLab.disabled = true;
                    selectLab.innerHTML = '<option value="">⏳ Mencari Lab yang Kosong...</option>';
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
                            sks: inputSks.value
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
                                option.textContent = `❌ ${lab.nama_lab} (Penuh/Bentrok)`;
                                option.style.color = '#ef4444';
                                option.style.fontWeight = 'bold';
                            } else {
                                option.textContent = `✅ ${lab.nama_lab} (Tersedia)`;
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
                            selectLab.innerHTML = '<option value="">⚠️ Mohon Maaf, Semua Lab Penuh pada Jam Ini</option>';
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
                    selectLab.innerHTML = '<option value="">Kunci Terbuka Jika Tanggal, Jam, & SKS Terisi</option>';
                    selectLab.classList.replace('bg-white', 'bg-slate-200');
                    infoJamSelesai.classList.add('hidden');
                    infoFasilitas.classList.add('hidden');
                }
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
    </script>
</body>
</html>