<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjadwalan Lab ICT</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Script Cetak PDF bawaan dipertahankan agar tombol cetak tidak mati --}}
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

            <div class="hidden items-center gap-10 text-sm font-semibold text-slate-600 md:flex">
                <a href="#" class="transition hover:text-blue-700">Home</a>
                <a href="#" class="transition hover:text-blue-700">About</a>
                <a href="#" class="transition hover:text-blue-700">Contact</a>
                <a href="/spv/jadwal" class="border-b-2 border-blue-600 pb-1 text-blue-700">Dashboard spv sonn</a>
            </div>

            <div>
                @auth
                    <a href="/spv/jadwal" class="inline-flex items-center justify-center rounded-lg bg-slate-700 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-700/20 transition hover:bg-slate-800">
                        Dashboard
                    </a>
                @else
                    <a href="/login" class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-700/25 transition hover:bg-blue-800">
                        Login
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
                Silakan cek ketersediaan ruang laboratorium dan jadwal praktikum di bawah ini.
            </p>
        </header>

        {{-- FILTER & KONTROL SECTION --}}
        <section class="mt-14 rounded-2xl border border-white/80 bg-white/80 p-4 shadow-xl shadow-blue-950/10 backdrop-blur">
            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-[1.4fr_0.9fr_1fr_1fr_auto]">
                <label class="relative block">
                    <span class="sr-only">Cari</span>
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="m17 17-3.8-3.8m1.55-4.45a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <input type="text" id="searchInput" placeholder="Cari Matkul/Dosen..." class="h-12 w-full rounded-xl border border-slate-200 bg-white pl-12 pr-4 text-sm font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                </label>

                <label class="block">
                    <span class="sr-only">Tanggal</span>
                    <input type="date" id="filterDate" value="{{ $filterDate ?? date('Y-m-d') }}" class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                </label>

               <label class="block">
    <span class="sr-only">Ruang Lab</span>
    <select id="filterLab" class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
        <option value="">Semua Ruang Lab</option>
        @foreach($labs as $lab)
            <option value="{{ $lab->nama_lab }}">{{ $lab->nama_lab }}</option>
        @endforeach
    </select>
</label>

                <label class="block">
                    <span class="sr-only">Sesi</span>
                    <select id="filterSession" class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        <option value="">Semua Sesi</option>
                        <option value="pagi">Pagi (07:00 - 12:20)</option>
                        <option value="siang">Siang (12:20 - 16:05)</option>
                        <option value="sore">Sore (16:05 - 18:30)</option>
                        <option value="malam">Malam (18:30 - 22:00)</option>
                    </select>
                </label>

                <button type="button" id="downloadPdfBtn" class="h-12 rounded-xl bg-blue-700 px-6 text-sm font-extrabold uppercase tracking-wide text-white shadow-lg shadow-blue-700/25 transition hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200">
                    Cetak Jadwal
                </button>
            </div>
        </section>

        {{-- STATUS KETERANGAN DATA --}}
        <p id="rowCountInfo" class="mt-5 text-sm font-medium italic text-slate-500">Memuat info jadwal...</p>

        {{-- TABEL UTAMA UTK PUBLIK (READ ONLY) --}}
        <section class="mt-4 overflow-hidden rounded-2xl border border-white/80 bg-white shadow-2xl shadow-blue-950/10">
            <div class="overflow-x-auto">
                <table id="scheduleTable" class="min-w-[900px] w-full border-collapse text-left">
                    <thead class="sticky top-0 z-10 bg-blue-900 text-white">
                        <tr>
                            <th class="px-6 py-5 text-center text-xs font-extrabold uppercase tracking-wider">Mata Kuliah</th>
                            <th class="px-6 py-5 text-center text-xs font-extrabold uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-5 text-center text-xs font-extrabold uppercase tracking-wider">Ruang Lab</th>
                            <th class="px-6 py-5 text-center text-xs font-extrabold uppercase tracking-wider">Nama Dosen</th>
                            <th class="px-6 py-5 text-center text-xs font-extrabold uppercase tracking-wider">Asisten Lab</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-slate-100 bg-white">
                        @forelse($schedules ?? [] as $s)
                            {{-- Data attributes dipertahankan agar fungsi filter javascript tidak mati --}}
                            <tr class="schedule-row transition hover:bg-blue-50/70"
                                data-date="{{ \Carbon\Carbon::parse($s->tanggal)->format('Y-m-d') }}"
                                data-lab="{{ $s->lab->nama_lab ?? '' }}">

                                <td class="px-6 py-5 text-center text-sm font-extrabold uppercase tracking-wide text-slate-700">
                                    <strong>{{ $s->matkul }}</strong>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="time-text block text-base font-extrabold text-blue-700">{{ date('H:i', strtotime($s->jam_mulai)) }} - {{ date('H:i', strtotime($s->jam_selesai)) }}</span>
                                    <small class="mt-1 block text-xs font-semibold text-slate-500">{{ $s->hari }}, {{ date('d M Y', strtotime($s->tanggal)) }}</small>
                                </td>
                                <td class="px-6 py-5 text-center text-sm font-extrabold text-slate-700">{{ $s->lab->nama_lab ?? 'Lab Tidak Ditemukan' }}</td>
                                <td class="px-6 py-5 text-center text-sm font-medium text-slate-600">{{ $s->dosen }}</td>
                                <td class="px-6 py-5 text-center text-sm font-semibold italic text-slate-600">{{ $s->assistantSchedule->nama_asisten ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr id="emptyState">
                                <td colspan="5" class="px-6 py-12 text-center text-sm font-semibold text-slate-500">
                                    Tidak ada jadwal kuliah praktikum pada tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    {{-- LOGIKA CORE JAVASCRIPT (SANGAT PENTING - JANGAN DIHAPUS) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const filterDate = document.getElementById('filterDate');
            const filterLab = document.getElementById('filterLab');
            const filterSession = document.getElementById('filterSession');
            const rows = document.querySelectorAll('.schedule-row');
            const rowCountInfo = document.getElementById('rowCountInfo');
            const emptyState = document.getElementById('emptyState');

            function filterTable() {
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

                if(emptyState) {
                    emptyState.style.display = count === 0 ? '' : 'none';
                }
            }

            // Event listener filter instan pendukung koding temanmu
            searchInput.addEventListener('input', filterTable);
            filterLab.addEventListener('change', filterTable);
            filterSession.addEventListener('change', filterTable);

            // Filter Tanggal (Reload Halaman via Query string parameter)
            filterDate.addEventListener('change', function() {
                const tbody = document.getElementById('tableBody');
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Memuat data tanggal baru...</td></tr>';
                window.location.href = window.location.pathname + '?filter_date=' + this.value;
            });

            // Jalankan filter pertama kali saat halaman dimuat
            filterTable();

            // Mesin Cetak PDF
            document.getElementById('downloadPdfBtn').addEventListener('click', function() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('landscape');

                doc.setFontSize(16);
                doc.text('Jadwal Laboratorium ICT', 14, 20);
                doc.setFontSize(10);
                doc.text(`Tanggal: ${filterDate.value || '-'} | Filter Ruang: ${filterLab.value || 'Semua'}`, 14, 26);

                doc.autoTable({
                    html: '#scheduleTable',
                    startY: 32,
                    theme: 'grid',
                    styles: { fontSize: 9, halign: 'center' }
                });

                doc.save(`Jadwal_Lab_ICT_${filterDate.value}.pdf`);
            });
        });
    </script>
</body>
</html>
