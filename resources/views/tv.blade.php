<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV DISPLAY - LAB COMPUTER</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg-dark: #0f172a;
            --bg-panel: #1e293b;
            --bg-row-1: #1e293b;
            --bg-row-2: #334155;
            --primary: #38bdf8;
            --primary-dark: #0284c7;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --gold: #f59e0b;
        }

        body {
            background-color: var(--bg-dark);
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* TV HEADER PANEL */
        .tv-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            height: 100px;
            background: var(--bg-panel);
            border-bottom: 4px solid var(--primary-dark);
            flex-shrink: 0;
        }

        .header-title h1 {
            font-size: 30px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-title p {
            font-size: 15px;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 2px;
        }

        .header-clock {
            text-align: right;
        }

        #live-clock {
            font-size: 42px;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }

        #live-date {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 600;
            margin-top: 4px;
            text-transform: uppercase;
        }

        /* BAR INFORMASI SESI AKTIF */
        .session-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            height: 44px;
            background: var(--primary-dark);
            flex-shrink: 0;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .session-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .session-badge {
            color: var(--gold);
        }

        #page-indicator {
            background: rgba(0,0,0,0.2);
            padding: 3px 12px;
            border-radius: 4px;
            font-size: 13px;
        }

        /* WRAPPER UTAMA SLIDER */
        .main-display {
            position: relative;
            flex: 1;
            padding: 30px 40px;
            overflow: hidden;
        }

        .slider-container {
            position: relative;
            width: 100%;
            height: 100%;
        }

        /* KOMPONEN SLIDE DENGAN EFEK FADE SMOOTH */
        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease-in-out, visibility 0.5s;
            display: flex;
            flex-direction: column;
        }

        .slide.active {
            opacity: 1;
            visibility: visible;
            z-index: 10;
        }

        /* SLIDE GAMBAR / POSTER JPG */
        .slide-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.3);
        }

        /* TOMBOL GULIR/NAVIGASI SAMPING MELAYANG */
        .nav-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 40;
            background: rgba(30, 41, 59, 0.8);
            color: var(--text-main);
            border: 2px solid rgba(255, 255, 255, 0.1);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .nav-button:hover {
            background: var(--primary-dark);
            border-color: var(--primary);
            color: white;
        }

        .nav-left { left: 15px; }
        .nav-right { right: 15px; }

        /* ARSITEKTUR TABEL JADWAL KULIAH */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        th {
            text-align: left;
            padding: 0 20px 10px;
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 1px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.05);
        }

        .schedule-row {
            background: var(--bg-row-1);
        }

        .schedule-row:nth-child(even) {
            background: var(--bg-row-2);
        }

        td {
            padding: 22px 20px;
            font-size: 22px;
            font-weight: 700;
            color: var(--text-main);
            vertical-align: middle;
        }

        td:first-child {
            border-radius: 12px 0 0 12px;
            border-left: 6px solid var(--primary);
        }

        td:last-child {
            border-radius: 0 12px 12px 0;
            text-align: right;
        }

        .lab-badge {
            background: rgba(56, 189, 248, 0.15);
            color: var(--primary);
            padding: 6px 14px;
            border-radius: 6px;
            border: 1px solid rgba(56, 189, 248, 0.3);
            font-size: 18px;
        }

        .time-tag {
            color: var(--gold);
            background: rgba(245, 158, 11, 0.1);
            padding: 6px 14px;
            border-radius: 6px;
            border: 1px solid rgba(245, 158, 11, 0.25);
            font-variant-numeric: tabular-nums;
        }

        .lecturer-text {
            color: var(--text-muted);
            font-size: 19px;
            font-weight: 500;
        }

        #empty-state-row td {
            text-align: center;
            color: var(--text-muted);
            padding: 60px 0;
            font-size: 20px;
        }

        /* TICKER / RUNNING TEXT PANEL */
        .ticker-bar {
            height: 50px;
            background: #090d16;
            border-top: 2px solid var(--primary-dark);
            display: flex;
            align-items: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .ticker-title {
            padding: 0 24px;
            height: 100%;
            background: var(--primary-dark);
            display: flex;
            align-items: center;
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            white-space: nowrap;
            z-index: 15;
        }

        .ticker-track {
            flex: 1;
            overflow: hidden;
        }

        .ticker-text-move {
            display: inline-block;
            white-space: nowrap;
            animation: ticker-animation 35s linear infinite;
        }

        @keyframes ticker-animation {
            0% { transform: translateX(100vw); }
            100% { transform: translateX(-100%); }
        }

        .ticker-node {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.85);
        }

        .ticker-node-bold {
            color: var(--primary);
            font-weight: 700;
        }

        .ticker-divider {
            margin: 0 25px;
            color: var(--text-muted);
            opacity: 0.4;
        }

        #hidden-controls { display: none; }
    </style>
</head>
<body>

    <header class="tv-header">
        <div class="header-title">
            <h1>Jadwal Laboratorium Komputer</h1>
            <p>Universitas Budi Luhur - Real-time Display</p>
        </div>
        <div class="header-clock">
            <div id="live-clock">00:00:00</div>
            <div id="live-date">Memuat Hari...</div>
        </div>
    </header>

    <div class="session-bar">
        <div class="session-indicator">
            <span>Sesi Aktif :</span>
            <span id="current-session-name" class="session-badge">MEMUAT...</span>
        </div>
        <div id="page-indicator">Halaman 1/1</div>
    </div>

    <div id="hidden-controls">
        <select id="filterDay">
            <option value=""></option>
            <option>Senin</option><option>Selasa</option><option>Rabu</option><option>Kamis</option><option>Jumat</option><option>Sabtu</option>
        </select>
        <select id="filterSession">
            <option value="pagi">pagi</option><option value="siang">siang</option><option value="sore">sore</option><option value="malam">malam</option>
        </select>
        <select id="limitSelect"><option value="6">6</option></select>
    </div>

    <main class="main-display">
        
        <button class="nav-button nav-left" onclick="gantiSlideManuel(-1)">&#10094;</button>
        <button class="nav-button nav-right" onclick="gantiSlideManuel(1)">&#10095;</button>

        <div class="slider-container">
            
            <div class="slide active">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 15%;">Ruangan</th>
                            <th style="width: 45%;">Mata Kuliah</th>
                            <th style="width: 25%;">Dosen Pengampu</th>
                            <th style="width: 15%; text-align: right; padding-right: 20px;">Waktu</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse($jadwal ?? [] as $j)
                            @php 
                                $roomLabel = is_object($j->lab) ? $j->lab->nama_lab : $j->lab;
                            @endphp
                            <tr class="schedule-row" data-day="{{ $j->hari }}" data-lab="{{ $roomLabel }}">
                                <td><span class="lab-badge">{{ $roomLabel }}</span></td>
                                <td>{{ $j->matkul }}</td>
                                <td class="lecturer-text">{{ $j->dosen }}</td>
                                <td>
                                    <span class="time-tag session-time-target">
                                        {{ date('H:i', strtotime($j->jam_mulai)) }} - {{ date('H:i', strtotime($j->jam_selesai)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-state-row" style="display: none;">
                                <td colspan="4">Tidak ada agenda praktikum aktif pada sesi waktu ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="slide">
                <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=1280&auto=format&fit=crop" alt="Info Lab 1" class="slide-image">
            </div>

            <div class="slide">
                <img src="https://images.unsplash.com/photo-1531403009284-440f080d1e12?q=80&w=1280&auto=format&fit=crop" alt="Info Lab 2" class="slide-image">
            </div>

        </div>
    </main>

    <footer class="ticker-bar">
        <div class="ticker-title">Agenda Hari Ini</div>
        <div class="ticker-track">
            <div class="ticker-text-move">
                @if(isset($jadwal) && count($jadwal) > 0)
                    @foreach($jadwal as $j)
                        @php 
                            $roomLabel = is_object($j->lab) ? $j->lab->nama_lab : $j->lab;
                        @endphp
                        <span class="ticker-node">
                            <span class="ticker-node-bold">[{{ $roomLabel }}]</span> 
                            {{ $j->matkul }} &mdash; {{ date('H:i', strtotime($j->jam_mulai)) }} s/d {{ date('H:i', strtotime($j->jam_selesai)) }}
                        </span>
                        <span class="ticker-divider"> • </span>
                    @endforeach
                @else
                    <span class="ticker-node">Tidak ada aktivitas praktikum laboratorium terjadwal untuk hari ini.</span>
                @endif
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const daySelect = document.getElementById("filterDay");
            const sessionSelect = document.getElementById("filterSession");
            const limitSelect = document.getElementById("limitSelect");
            const tbody = document.getElementById("tableBody");
            const rows = Array.from(tbody.querySelectorAll("tr.schedule-row"));
            const pageIndicator = document.getElementById("page-indicator");

            let currentTablePage = 1;

            // Jam Digital Real-time
            function updateClock() {
                const now = new Date();
                document.getElementById('live-clock').innerText = now.toLocaleTimeString('id-ID', { hour12: false });
                document.getElementById('live-date').innerText = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            }
            setInterval(updateClock, 1000);

            // Klasifikasi Sesi Waktu Otomatis Sinkron Welcome
            function detectCurrentSession() {
                const now = new Date();
                const time = now.getHours().toString().padStart(2, '0') + ":" + now.getMinutes().toString().padStart(2, '0');
                if (time >= "07:00" && time < "12:20") return "pagi";
                if (time >= "12:20" && time < "16:05") return "siang";
                if (time >= "16:05" && time < "18:30") return "sore";
                if (time >= "18:30" && time <= "22:00") return "malam";
                return "";
            }

            // Engine Utama Penyaring Jadwal Otomatis
            function renderTable() {
                const now = new Date();
                const today = now.toLocaleDateString("id-ID", { weekday: "long" });
                const todayFormatted = today.charAt(0).toUpperCase() + today.slice(1);
                
                daySelect.value = todayFormatted;
                const autoSession = detectCurrentSession();
                sessionSelect.value = autoSession;
                document.getElementById('current-session-name').innerText = autoSession.toUpperCase() || "ISTIRAHAT INTERVAl";

                const limit = parseInt(limitSelect.value);
                const selectedDay = daySelect.value;
                const selectedSession = sessionSelect.value;

                let filtered = rows.filter(r => {
                    const d = r.dataset.day;
                    const timeText = r.querySelector(".session-time-target")?.innerText.split(" - ")[0].trim() || "00:00";
                    const matchDay = d.toLowerCase() === selectedDay.toLowerCase();
                    
                    let matchSession = true;
                    if (selectedSession === "pagi") matchSession = (timeText >= "07:00" && timeText < "12:20");
                    else if (selectedSession === "siang") matchSession = (timeText >= "12:20" && timeText < "16:05");
                    else if (selectedSession === "sore") matchSession = (timeText >= "16:05" && timeText < "18:30");
                    else if (selectedSession === "malam") matchSession = (timeText >= "18:30" && timeText <= "22:00");
                    
                    return matchDay && matchSession;
                });

                filtered.sort((a, b) => {
                    const tA = a.querySelector(".session-time-target")?.innerText.split(" - ")[0].trim() || "00:00";
                    const tB = b.querySelector(".session-time-target")?.innerText.split(" - ")[0].trim() || "00:00";
                    return tA.localeCompare(tB);
                });

                const totalPage = Math.ceil(filtered.length / limit) || 1;
                if (currentTablePage > totalPage) currentTablePage = 1;

                const start = (currentTablePage - 1) * limit;
                const end = start + limit;

                rows.forEach(r => r.style.display = "none");
                filtered.slice(start, end).forEach(r => {
                    r.style.display = "";
                    tbody.appendChild(r);
                });

                const emptyState = document.getElementById("empty-state-row");
                if (emptyState) {
                    if (filtered.length === 0) {
                        emptyState.style.display = "";
                        tbody.appendChild(emptyState);
                    } else {
                        emptyState.style.display = "none";
                    }
                }

                pageIndicator.innerText = `Jadwal Sesi: Halaman ${currentTablePage}/${totalPage}`;
            }

            // Paginasi Baris Tabel Otomatis berjalan tiap 10 detik
            setInterval(() => {
                currentTablePage++;
                renderTable();
            }, 10000);

            // Auto-reload Browser di Menit Batas Pergantian Sesi Utama
            setInterval(() => {
                const time = new Date().toTimeString().slice(0, 5);
                if (["07:00", "12:20", "16:05", "18:30", "22:00"].includes(time)) {
                    const lastReload = localStorage.getItem('last_session_reload');
                    if (lastReload !== time) {
                        localStorage.setItem('last_session_reload', time);
                        location.reload();
                    }
                }
            }, 20000);

            renderTable();
            updateClock();
        });

        // === ENGINE UTAMA SLIDER CAROUSEL (FADE SMOOTH CONCEPTS) ===
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
            // Durasi display antarslide dikunci 20 detik biar mahasiswa sempat membaca info poster
            sliderTimer = setInterval(putarSlideOtomatis, 20000);
        }

        segarkanTimerSlider();
    </script>
</body>
</html>