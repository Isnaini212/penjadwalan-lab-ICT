<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjadwalan Lab ICT</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Script Cetak PDF --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-blue-100 font-sans text-slate-800">

    <nav class="sticky top-0 z-40 border-b border-white/70 bg-white/80 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 lg:px-8">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/LogoICT.png') }}" alt="Logo Untan" class="h-10 w-auto">
                <div class="text-lg font-semibold tracking-tight text-blue-900 sm:text-xl">
                    Penjadwalan Lab ICT
                </div>
            </div>

            <div class="flex gap-3">
                @auth
                    @if(auth()->user()->role === 'spv')
                        <a href="{{ route('spv.dashboard') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-700 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-700/20 transition hover:bg-slate-800">
                            Dashboard SPV
                        </a>
                    @elseif(auth()->user()->role === 'ormawa')
                        <a href="{{ route('ormawa.booking.index') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-700 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-700/20 transition hover:bg-slate-800">
                            Portal Ormawa
                        </a>
                    @elseif(auth()->user()->role === 'dosen')
                        <a href="{{ route('dosen.booking.index') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-700 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-700/20 transition hover:bg-slate-800">
                            Portal Dosen
                        </a>
                    @elseif(auth()->user()->role === 'asisten')
                        <a href="{{ route('asisten.jadwal') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-700 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-700/20 transition hover:bg-slate-800">
                            Jadwal Asisten
                        </a>
                    @else
                        <a href="/" class="inline-flex items-center justify-center rounded-lg bg-slate-700 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-700/20 transition hover:bg-slate-800">
                            Dashboard
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-700/25 transition hover:bg-blue-800">
                        Login Sistem
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-5 pb-12 pt-14 lg:px-8">
        {{-- HERO SECTION --}}
        <header class="mx-auto max-w-5xl text-center">
            <h1 class="text-4xl font-bold tracking-tight text-blue-900 sm:text-4xl lg:text-5xl">
                Selamat Datang di Penjadwalan Lab ICT
            </h1>
            <p class="mx-auto mt-6 max-w-3xl text-base leading-8 text-slate-600 sm:text-lg">
                Kami siap membantu kelancaran agenda Anda melalui sistem reservasi yang terintegrasi.
                Silakan cek ketersediaan ruang laboratorium per minggu di bawah ini.
            </p>
            <p class="mt-4 inline-block bg-blue-100 text-blue-800 px-4 py-1.5 rounded-full text-sm font-bold border border-blue-200 shadow-sm">
                Periode Aktif: <span id="info-rentang-aktif">{{ \Carbon\Carbon::parse($activeRange['start'] ?? now())->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($activeRange['end'] ?? now())->translatedFormat('d F Y') }}</span>
            </p>
        </header>

        {{-- FILTER & KONTROL SECTION --}}
        <section class="mt-10 rounded-2xl border border-white/80 bg-white/80 p-4 shadow-xl shadow-blue-950/10 backdrop-blur">
            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-5">
                
                {{-- 1. DROPDOWN MINGGU --}}
                <label class="block lg:col-span-1">
                    <span class="sr-only">Minggu</span>
                    <select id="dropdown-minggu" onchange="gantiMinggu(this.value)" class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 cursor-pointer">
                        @foreach($listMinggu ?? [] as $m)
                            <option value="{{ $m['id_minggu'] }}" {{ ($mingguDipilih ?? 1) == $m['id_minggu'] ? 'selected' : '' }}>
                                {{ $m['label'] }} ({{ \Carbon\Carbon::parse($m['start'])->format('d/m') }} - {{ \Carbon\Carbon::parse($m['end'])->format('d/m') }})
                            </option>
                        @endforeach
                    </select>
                </label>

                {{-- 2. CARI MATKUL/DOSEN --}}
                <label class="relative block lg:col-span-1">
                    <span class="sr-only">Cari</span>
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m17 17-3.8-3.8m1.55-4.45a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                    <input type="text" id="searchInput" placeholder="Cari Matkul/Dosen..." class="h-12 w-full rounded-xl border border-slate-200 bg-white pl-12 pr-4 text-sm font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                </label>

                {{-- 3. FILTER RUANG LAB --}}
                <label class="block lg:col-span-1">
                    <span class="sr-only">Ruang Lab</span>
                    <select id="filterLab" class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        <option value="">Semua Ruang Lab</option>
                        @foreach($labs ?? [] as $lab)
                            <option value="{{ $lab->nama_lab }}">{{ $lab->nama_lab }}</option>
                        @endforeach
                    </select>
                </label>

                {{-- 4. FILTER SESI --}}
                <label class="block lg:col-span-1">
                    <span class="sr-only">Sesi</span>
                    <select id="filterSession" class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        <option value="">Semua Sesi</option>
                        <option value="pagi">Pagi (07:00 - 12:20)</option>
                        <option value="siang">Siang (12:20 - 16:05)</option>
                        <option value="sore">Sore (16:05 - 18:30)</option>
                        <option value="malam">Malam (18:30 - 22:00)</option>
                    </select>
                </label>

                {{-- 5. TOMBOL CETAK --}}
                <button type="button" id="downloadPdfBtn" class="h-12 w-full rounded-xl bg-blue-700 px-6 text-sm font-extrabold uppercase tracking-wide text-white shadow-lg shadow-blue-700/25 transition hover:bg-blue-800 lg:col-span-1">
                    Cetak Jadwal
                </button>
            </div>
        </section>

        {{-- STATUS KETERANGAN DATA --}}
        <p id="rowCountInfo" class="mt-5 mb-2 text-sm font-medium italic text-slate-500">Memuat info jadwal...</p>

        {{-- TABEL UTAMA UTK PUBLIK --}}
        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-xl shadow-slate-200/40">
            <div class="overflow-x-auto">
                <table id="scheduleTable" class="w-full text-left border-collapse border-none">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 text-[11px] uppercase tracking-widest border-b border-slate-200">
                            <th class="px-6 py-4 font-extrabold">Hari & Tanggal</th>
                            <th class="px-6 py-4 font-extrabold">Laboratorium</th>
                            <th class="px-6 py-4 font-extrabold">Waktu</th>
                            <th class="px-6 py-4 font-extrabold w-1/3">Mata Kuliah</th>
                            <th class="px-6 py-4 font-extrabold">Dosen / Praktikan</th>
                            <th class="px-6 py-4 font-extrabold text-center">Asisten</th>
                        </tr>
                    </thead>
                    <tbody id="body-tabel-jadwal" class="divide-y divide-slate-100">
                        @forelse($schedules ?? [] as $sch)
                            <tr class="schedule-row hover:bg-slate-50/60 transition" data-lab="{{ $sch->lab->nama_lab ?? '' }}">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-sm text-slate-800">{{ $sch->hari }}</div>
                                    <div class="text-xs text-slate-400 font-bold mt-0.5">{{ \Carbon\Carbon::parse($sch->tanggal)->translatedFormat('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 text-xs font-black border border-indigo-100">
                                        {{ $sch->lab->nama_lab ?? 'Lab Terhapus' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="time-text font-mono text-xs font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 inline-block">
                                        {{ substr($sch->jam_mulai, 0, 5) }} - {{ substr($sch->jam_selesai, 0, 5) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-sm text-slate-800 tracking-tight">{{ $sch->matkul }}</div>
                                    <div class="text-[10px] text-slate-400 font-extrabold uppercase mt-0.5 tracking-wider">{{ $sch->sks }} SKS</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-slate-600">{{ $sch->dosen }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($sch->assistantSchedule)
                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2.5 py-0.5 rounded-full">
                                            {{ $sch->assistantSchedule->nama_asisten }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 font-medium bg-slate-50 px-2.5 py-0.5 rounded-full border border-slate-200">Kosong</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyState">
                                <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-calendar-times text-4xl mb-3 text-slate-200"></i>
                                        <span class="font-bold">Tidak ada jadwal praktikum pada minggu ini.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    {{-- LOGIKA CORE JAVASCRIPT (AJAX & FILTER) --}}
    <script>
        const searchInput = document.getElementById('searchInput');
        const filterLab = document.getElementById('filterLab');
        const filterSession = document.getElementById('filterSession');
        const rowCountInfo = document.getElementById('rowCountInfo');
        const tbody = document.getElementById('body-tabel-jadwal');

        // 1. FUNGSI FILTER CLIENT-SIDE LOKAL
        function filterTable() {
            const rows = document.querySelectorAll('.schedule-row');
            let count = 0;
            const searchVal = searchInput.value.toLowerCase();
            const labVal = filterLab.value;
            const sessionVal = filterSession.value;

            rows.forEach(row => {
                const rLab = row.getAttribute('data-lab');
                const timeText = row.querySelector('.time-text').innerText.split(' - ')[0].trim();
                const textContent = row.innerText.toLowerCase();

                const matchSearch = searchVal === '' || textContent.includes(searchVal);
                const matchLab = labVal === '' || rLab === labVal;

                let matchSession = true;
                if (sessionVal === "pagi") matchSession = (timeText >= "07:00" && timeText < "12:20");
                else if (sessionVal === "siang") matchSession = (timeText >= "12:20" && timeText < "16:05");
                else if (sessionVal === "sore") matchSession = (timeText >= "16:05" && timeText < "18:30");
                else if (sessionVal === "malam") matchSession = (timeText >= "18:30" && timeText <= "22:00");

                if (matchSearch && matchLab && matchSession) {
                    row.style.display = '';
                    count++;
                } else {
                    row.style.display = 'none';
                }
            });

            rowCountInfo.innerText = `Menampilkan ${count} jadwal praktikum.`;
        }

        // Jalankan trigger saat filter diketik/dipilih
        searchInput.addEventListener('input', filterTable);
        filterLab.addEventListener('change', filterTable);
        filterSession.addEventListener('change', filterTable);

        // 2. FUNGSI GANTI MINGGU VIA AJAX
        function gantiMinggu(idMinggu) {
            tbody.classList.add('opacity-40', 'pointer-events-none');
            rowCountInfo.innerText = 'Memuat jadwal dari server...';

            fetch(`/?week=${idMinggu}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                tbody.classList.remove('opacity-40', 'pointer-events-none');

                // Update teks Periode Aktif
                const opt = { day: 'numeric', month: 'long', year: 'numeric' };
                const startFmt = new Date(data.active_range.start).toLocaleDateString('id-ID', opt);
                const endFmt = new Date(data.active_range.end).toLocaleDateString('id-ID', opt);
                document.getElementById('info-rentang-aktif').textContent = `${startFmt} s/d ${endFmt}`;

                tbody.innerHTML = '';
                
                if (data.schedules.length === 0) {
                    tbody.innerHTML = `
                        <tr id="emptyState">
                            <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-calendar-times text-4xl mb-3 text-slate-200"></i>
                                    <span class="font-bold">Tidak ada jadwal praktikum pada minggu ini.</span>
                                </div>
                            </td>
                        </tr>`;
                    rowCountInfo.innerText = `Menampilkan 0 jadwal praktikum.`;
                    return;
                }

                data.schedules.forEach(sch => {
                    const dateObj = new Date(sch.tanggal);
                    const tglFmt = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                    const namaLab = sch.lab ? sch.lab.nama_lab : 'Lab Terhapus';
                    const namaAsisten = sch.assistant_schedule 
                        ? `<span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2.5 py-0.5 rounded-full">${sch.assistant_schedule.nama_asisten}</span>` 
                        : `<span class="text-xs text-slate-400 font-medium bg-slate-50 px-2.5 py-0.5 rounded-full border border-slate-200">Kosong</span>`;

                    const tr = document.createElement('tr');
                    tr.className = "schedule-row hover:bg-slate-50/60 transition";
                    tr.setAttribute('data-lab', namaLab);
                    
                    tr.innerHTML = `
                        <td class="px-6 py-4">
                            <div class="font-bold text-sm text-slate-800">${sch.hari}</div>
                            <div class="text-xs text-slate-400 font-bold mt-0.5">${tglFmt}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 text-xs font-black border border-indigo-100">
                                ${namaLab}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="time-text font-mono text-xs font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 inline-block">
                                ${sch.jam_mulai.substring(0, 5)} - ${sch.jam_selesai.substring(0, 5)}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-sm text-slate-800 tracking-tight">${sch.matkul}</div>
                            <div class="text-[10px] text-slate-400 font-extrabold uppercase mt-0.5 tracking-wider">${sch.sks} SKS</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-slate-600">${sch.dosen}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            ${namaAsisten}
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                // Terapkan filter lokal yang sedang aktif setelah tabel kereload
                filterTable();
            })
            .catch(err => {
                console.error("Gagal load data mingguan:", err);
                tbody.classList.remove('opacity-40', 'pointer-events-none');
                rowCountInfo.innerText = 'Gagal memuat jadwal. Silakan refresh halaman.';
            });
        }

        // Inisialisasi hitungan baris tabel saat halaman pertama kali diload
        document.addEventListener('DOMContentLoaded', function() {
            filterTable();
        });

        // 3. FUNGSI MESIN CETAK PDF
        document.getElementById('downloadPdfBtn').addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('landscape');
            
            // Ambil teks minggu dari dropdown
            const sel = document.getElementById('dropdown-minggu');
            const mingguTeks = sel.options[sel.selectedIndex].text;

            doc.setFontSize(16);
            doc.text('Jadwal Laboratorium ICT', 14, 20);
            doc.setFontSize(10);
            doc.text(`Periode: ${mingguTeks} | Filter Ruang: ${filterLab.value || 'Semua'}`, 14, 26);

            doc.autoTable({
                html: '#scheduleTable',
                startY: 32,
                theme: 'grid',
                styles: { fontSize: 9, halign: 'center' }
            });

            // Nama file PDF otomatis menyesuaikan teks minggu
            doc.save(`Jadwal_Lab_ICT_${mingguTeks.replace(/[^a-zA-Z0-9]/g, '_')}.pdf`);
        });
    </script>
</body>
</html>