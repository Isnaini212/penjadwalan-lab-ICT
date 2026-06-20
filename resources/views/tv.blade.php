
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV DISPLAY - LAB COMPUTER</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Animasi Marquee Running Text disisipkan di sini karena membutuhkan keyframes kustom */
        @keyframes ticker-animation {
            0% { transform: translateX(100vw); }
            100% { transform: translateX(-100%); }
        }
        .animate-ticker {
            animation: ticker-animation 35s linear infinite;
        }
        /* State transisi slider yang dikendalikan oleh fungsi Javascript */
        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease-in-out, visibility 0.5s;
        }
        .slide.active {
            opacity: 1;
            visibility: visible;
            z-index: 10;
        }
    </style>
</head>
<body class="h-screen w-screen bg-gradient-to-br from-slate-50 via-blue-50 to-blue-100 font-sans text-slate-800 overflow-hidden flex flex-col antialiased">

    <header class="flex items-center justify-between px-10 h-24 bg-white/80 backdrop-blur-md border-b border-white shadow-sm flex-shrink-0 z-20">
        <div class="leading-tight flex items-center gap-4">
            <img src="{{ asset('img/logo-ubl.png') }}" alt="Logo" class="h-12 w-12 rounded-full object-cover shadow-sm" onerror="this.src='https://ui-avatars.com/api/?name=ICT&background=0284c7&color=fff'">
            <div>
                <h1 class="text-3xl font-extrabold uppercase tracking-tight text-blue-900">Jadwal Laboratorium Komputer</h1>
                <p class="text-sm text-slate-500 font-bold mt-1">Universitas Budi Luhur - Real-time Display</p>
            </div>
        </div>
        <div class="text-right leading-none">
            <div id="live-clock" class="text-4xl font-extrabold text-blue-700 tracking-wider tabular-nums">00:00:00</div>
            <div id="live-date" class="text-xs text-slate-500 font-bold tracking-widest uppercase mt-2">Memuat Hari...</div>
        </div>
    </header>

    <div class="flex items-center justify-between px-10 h-11 bg-blue-700 flex-shrink-0 text-sm font-bold tracking-wide shadow-md text-white z-10">
        <div class="flex items-center gap-2">
            <span>Sesi Aktif :</span>
            <span id="current-session-name" class="text-yellow-300 uppercase">MEMUAT...</span>
        </div>
        <div id="page-indicator" class="bg-blue-900/50 px-3 py-0.5 rounded text-xs font-semibold">Halaman 1/1</div>
    </div>

    <main class="relative flex-1 p-8 overflow-hidden">

        <button class="nav-button absolute left-4 top-1/2 -translate-y-1/2 z-40 bg-white/80 hover:bg-blue-700 text-slate-600 hover:text-white border border-slate-200 hover:border-blue-700 w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold cursor-pointer transition shadow-xl backdrop-blur-sm" onclick="gantiSlideManuel(-1)">
            &#10094;
        </button>

        <button class="nav-button absolute right-4 top-1/2 -translate-y-1/2 z-40 bg-white/80 hover:bg-blue-700 text-slate-600 hover:text-white border border-slate-200 hover:border-blue-700 w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold cursor-pointer transition shadow-xl backdrop-blur-sm" onclick="gantiSlideManuel(1)">
            &#10095;
        </button>

        <div class="relative w-full flex-1 h-full">

            <div class="slide flex flex-col active">
                <table class="w-full border-separate border-spacing-y-2.5">
                    <thead>
                        <tr class="text-left text-xs font-extrabold uppercase tracking-wider text-slate-500 bg-white/40">
                            <th class="px-5 pb-2 w-[15%]">Ruangan</th>
                            <th class="px-5 pb-2 w-[45%]">Mata Kuliah</th>
                            <th class="px-5 pb-2 w-[25%]">Dosen Pengampu</th>
                            <th class="px-5 pb-2 w-[15%] text-right pr-5">Waktu</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse($jadwal ?? [] as $j)
                            @php
                                $roomLabel = is_object($j->lab) ? $j->lab->nama_lab : $j->lab;
                            @endphp
                            <tr class="schedule-row bg-white shadow-sm hover:shadow-md transition-shadow rounded-xl" data-time="{{ date('H:i', strtotime($j->jam_mulai)) }}" style="display: none;">
                                <td class="px-5 py-4 text-base font-bold rounded-l-xl border-l-4 border-blue-500">
                                    <span class="bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg border border-blue-100 text-sm font-bold tracking-wide">
                                        {{ $roomLabel }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xl font-extrabold text-slate-800">{{ $j->matkul }}</td>
                                <td class="px-5 py-4 text-lg font-semibold text-slate-600">{{ $j->dosen }}</td>
                                <td class="px-5 py-4 text-right pr-5 rounded-r-xl">
                                    <span class="text-orange-700 bg-orange-50 px-3 py-1.5 rounded-lg border border-orange-100 text-lg font-bold tracking-wide tabular-nums">
                                        {{ date('H:i', strtotime($j->jam_mulai)) }} - {{ date('H:i', strtotime($j->jam_selesai)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            @endforelse
                        <tr id="empty-state-row" style="display: none;">
                            <td colspan="4" class="text-center text-slate-500 py-16 text-xl font-semibold tracking-wide border-2 border-dashed border-slate-300 rounded-xl bg-white/50">
                                Tidak ada agenda praktikum aktif pada sesi waktu ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @foreach($slides ?? [] as $slide)
                <div class="slide">
                    <img src="{{ asset('storage/' . $slide->image_path) }}" alt="Pengumuman Laboratorium" class="w-full h-full object-contain rounded-xl bg-black/5 backdrop-blur-sm shadow-xl border border-white/60">
                </div>
            @endforeach

        </div>
    </main>

    <!-- RUNNING TEXT TICKER PALING BAWAH (Hanya Teks Inputan SPV) -->
    <footer class="h-12 bg-white border-t border-slate-200 flex items-center flex-shrink-0 overflow-hidden shadow-[0_-5px_15px_rgba(0,0,0,0.05)] z-20">
        <div class="px-6 h-full bg-blue-700 text-white flex items-center text-xs font-extrabold uppercase tracking-widest whitespace-nowrap shadow-md z-30">
            Pengumuman
        </div>
        <div class="flex-1 overflow-hidden relative">
            <div class="inline-block whitespace-nowrap animate-ticker leading-none">
                <!-- Murni Menampilkan Teks Kustom Pengumuman Dari Dashboard SPV -->
                <span class="text-sm font-bold text-slate-700">{{ $runningText ?? 'Selamat Datang di Laboratorium ICT Universitas Budi Luhur.' }}</span>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const tbody = document.getElementById("tableBody");
            const rows = Array.from(tbody.querySelectorAll("tr.schedule-row"));
            const emptyState = document.getElementById("empty-state-row");
            const pageIndicator = document.getElementById("page-indicator");
            const sessionNameText = document.getElementById('current-session-name');

            let currentTablePage = 1;
            const rowsPerPage = 6;

            function updateClock() {
                const now = new Date();
                document.getElementById('live-clock').innerText = now.toLocaleTimeString('id-ID', { hour12: false });
                document.getElementById('live-date').innerText = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            }
            setInterval(updateClock, 1000);

            function detectCurrentSession() {
                const now = new Date();
                const time = now.getHours().toString().padStart(2, '0') + ":" + now.getMinutes().toString().padStart(2, '0');
                if (time >= "07:00" && time < "12:20") return { id: "pagi", title: "PAGI", start: "07:00", end: "12:20" };
                if (time >= "12:20" && time < "16:05") return { id: "siang", title: "SIANG", start: "12:20", end: "16:05" };
                if (time >= "16:05" && time < "18:30") return { id: "sore", title: "SORE", start: "16:05", end: "18:30" };
                if (time >= "18:30" && time <= "22:00") return { id: "malam", title: "MALAM", start: "18:30", end: "22:00" };
                return { id: "none", title: "ISTIRAHAT / INTERVAL", start: "00:00", end: "00:00" };
            }

            function renderTable() {
                const currentSession = detectCurrentSession();
                sessionNameText.innerText = currentSession.title;

                let filtered = rows.filter(r => {
                    const timeTarget = r.getAttribute('data-time');
                    return timeTarget >= currentSession.start && timeTarget < currentSession.end;
                });

                const totalPage = Math.ceil(filtered.length / rowsPerPage) || 1;
                if (currentTablePage > totalPage) currentTablePage = 1;

                const start = (currentTablePage - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                rows.forEach(r => r.style.display = "none");
                filtered.slice(start, end).forEach(r => r.style.display = "");

                if (filtered.length === 0) {
                    if (emptyState) emptyState.style.display = "";
                } else {
                    if (emptyState) emptyState.style.display = "none";
                }

                pageIndicator.innerText = `Halaman Tabel ${currentTablePage}/${totalPage}`;
            }

            setInterval(() => {
                currentTablePage++;
                renderTable();
            }, 10000);

            setInterval(() => {
                const time = new Date().toTimeString().slice(0, 5);
                if (["07:00", "12:20", "16:05", "18:30", "22:00"].includes(time)) {
                    location.reload();
                }
            }, 30000);

            renderTable();
            updateClock();
        });

        // MANAGEMENT KONTROL SLIDER CAROUSEL
        let activeIndex = 0;
        let sliderTimer;
        const allSlides = document.querySelectorAll('.slide');

        function tunjukkanSlide(index) {
            allSlides.forEach(s => s.classList.remove('active'));
            if (index >= allSlides.length) activeIndex = 0;
            if (index < 0) activeIndex = allSlides.length - 1;
            allSlides[activeIndex].classList.add('active');
        }

        function gantiSlideManuel(langkah) {
            activeIndex += langkah;
            tunjukkanSlide(activeIndex);
            segarkanTimerSlider();
        }

        function putarSlideOtomatis() {
            activeIndex++;
            tunjukkanSlide(activeIndex);
        }

        function segarkanTimerSlider() {
            clearInterval(sliderTimer);
            sliderTimer = setInterval(putarSlideOtomatis, 15000);
        }

        segarkanTimerSlider();
    </script>
</body>
</html>
