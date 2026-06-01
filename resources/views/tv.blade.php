<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV DISPLAY - LAB COMPUTER</title>
</head>
<body>


    <header>
        <div>
            <h1>Jadwal Laboratorium Komputer</h1>
            <p>Universitas Budi Luhur - Real-time Display</p>
        </div>
        <div>
            <div id="live-clock">00:00:00</div>
            <div id="live-date">Memuat...</div>
        </div>
    </header>
 


    <div id="hidden-controls" style="display: none;">
        <select id="filterDay">
            <option value=""></option>
            <option>Senin</option>
            <option>Selasa</option>
            <option>Rabu</option>
            <option>Kamis</option>
            <option>Jumat</option>
            <option>Sabtu</option>
        </select>
        <select id="filterSession">
            <option value="pagi">pagi</option>
            <option value="siang">siang</option>
            <option value="sore">sore</option>
            <option value="malam">malam</option>
        </select>
        <select id="limitSelect">
            <option value="6">6</option>
        </select>
    </div>


    <main>
        <table border="1" id="mainTable">
            <thead>
                <tr>
                    <th>RUANGAN</th>
                    <th>MATA KULIAH</th>
                    <th>DOSEN PENGAMPU</th>
                    <th>WAKTU</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @foreach($jadwal as $s)
    @php
        $roomLabel = is_object($s->lab) ? $s->lab->nama_lab : $s->lab;
    @endphp
    <tr class="schedule-row" data-day="{{ $s->hari }}" data-lab="{{ $roomLabel }}">
        <td>{{ $roomLabel }}</td>
        <td>{{ $s->matkul }}</td>
        <td>{{ $s->dosen }}</td>
        <td><span class="time-tag">{{ date('H:i', strtotime($s->jam_mulai)) }} - {{ date('H:i', strtotime($s->jam_selesai)) }}</span></td>
    </tr>
@endforeach
            </tbody>
        </table>
    </main>


    <footer>
        <div>Sesi Aktif: <span id="current-session-name">-</span></div>
        <div>
            <span>LIVE MONITORING</span>
            <span id="page-indicator">Halaman 1/1</span>
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


            let currentPage = 1;


            // Jam realtime
            function updateClock() {
                const now = new Date();
                document.getElementById('live-clock').innerText = now.toLocaleTimeString('id-ID', { hour12: false });
                document.getElementById('live-date').innerText = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            }
            setInterval(updateClock, 1000);


            // Klasifikasi sesi waktu otomatis
            function detectCurrentSession() {
                const now = new Date();
                const time = now.getHours().toString().padStart(2, '0') + ":" + now.getMinutes().toString().padStart(2, '0');
                if (time >= "07:00" && time < "12:20") return "pagi";
                if (time >= "12:20" && time < "16:05") return "siang";
                if (time >= "16:05" && time < "18:30") return "sore";
                if (time >= "18:30" && time <= "22:00") return "malam";
                return "";
            }


            // Paginasi otomatis dan filter harian
            function renderTable() {
                const now = new Date();
                const today = now.toLocaleDateString("id-ID", { weekday: "long" });
                const todayFormatted = today.charAt(0).toUpperCase() + today.slice(1);
               
                daySelect.value = todayFormatted;
                const autoSession = detectCurrentSession();
                sessionSelect.value = autoSession;
                document.getElementById('current-session-name').innerText = autoSession.toUpperCase() || "ISTIRAHAT";


                const limit = parseInt(limitSelect.value);
                const selectedDay = daySelect.value;
                const selectedSession = sessionSelect.value;


                let filtered = rows.filter(r => {
                    const d = r.dataset.day;
                    const timeText = r.querySelector(".time-tag")?.innerText.split(" - ")[0].trim() || "00:00";
                    const matchDay = d.toLowerCase() === selectedDay.toLowerCase();
                   
                    let matchSession = true;
                    if (selectedSession === "pagi") matchSession = (timeText >= "07:00" && timeText < "12:20");
                    else if (selectedSession === "siang") matchSession = (timeText >= "12:20" && timeText < "16:05");
                    else if (selectedSession === "sore") matchSession = (timeText >= "16:05" && timeText < "18:30");
                    else if (selectedSession === "malam") matchSession = (timeText >= "18:30" && timeText <= "22:00");
                   
                    return matchDay && matchSession;
                });


                filtered.sort((a, b) => {
                    const tA = a.querySelector(".time-tag")?.innerText.split(" - ")[0].trim() || "00:00";
                    const tB = b.querySelector(".time-tag")?.innerText.split(" - ")[0].trim() || "00:00";
                    return tA.localeCompare(tB);
                });


                const totalPage = Math.ceil(filtered.length / limit) || 1;
