<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penjadwalan Lab ICT</title>
    
    {{-- Script Cetak PDF bawaan dipertahankan agar tombol cetak tidak mati --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
</head>
<body>

    {{-- NAVBAR POLOS --}}
    <nav style="display: flex; justify-content: space-between; align-items: center; padding: 10px;">
        <div style="font-weight: bold; font-size: 18px;">
            Penjadwalan Lab ICT
        </div>
        
        <div style="display: flex; gap: 15px;">
            <a href="#">Home</a>
            <a href="#">About</a>
            <a href="#">Contact</a>
            <a href="#" style="font-weight: bold;">Jadwal</a>
        </div>

        <div>
            @auth
                <a href="/spv/jadwal" style="background: gray; color: white; padding: 5px 10px; text-decoration: none;">Dashboard</a>
            @else
                <a href="/spv/jadwal" style="background: blue; color: white; padding: 5px 10px; text-decoration: none;">Masuk</a>
            @endauth
        </div>
    </nav>

    <hr>

    {{-- HERO SECTION --}}
    <header style="text-align: center; margin: 20px 0;">
        <h1>Selamat Datang di Penjadwalan Lab ICT</h1>
        <p>Silakan cek ketersediaan ruang laboratorium dan jadwal praktikum di bawah ini.</p>
    </header>

    <hr>

    {{-- FILTER & KONTROL SECTION --}}
    <section style="margin-bottom: 20px;">
        <table border="0" cellpadding="5">
            <tr>
                <td>
                    <label>Cari:</label>
                    <input type="text" id="searchInput" placeholder="Cari Matkul/Dosen...">
                </td>
                
                <td>
                    <label>Tanggal:</label>
                    <input type="date" id="filterDate" value="{{ $filterDate ?? date('Y-m-d') }}">
                </td>

                <td>
                    <label>Ruang Lab:</label>
                    <select id="filterLab">
                        <option value="">Semua Ruang Lab</option>
                        @for($i=1; $i<=11; $i++)
                            @php $formatLab = 'LAB ' . sprintf('%02d', $i); @endphp
                            <option value="{{ $formatLab }}">{{ $formatLab }}</option>
                        @endfor
                    </select>
                </td>

                <td>
                    <label>Sesi:</label>
                    <select id="filterSession">
                        <option value="">Semua Sesi</option>
                        <option value="pagi">Pagi (07:00 - 12:20)</option>
                        <option value="siang">Siang (12:20 - 16:05)</option>
                        <option value="sore">Sore (16:05 - 18:30)</option>
                        <option value="malam">Malam (18:30 - 22:00)</option>
                    </select>
                </td>

                <td>
                    <button type="button" id="downloadPdfBtn">🖨️ Cetak Jadwal (PDF)</button>
                </td>
            </tr>
        </table>
    </section>

    {{-- STATUS KETERANGAN DATA --}}
    <p id="rowCountInfo" style="font-style: italic; color: gray;">Memuat info jadwal...</p>

    {{-- TABEL UTAMA UTK PUBLIK (READ ONLY) --}}
    <table border="1" cellpadding="8" cellspacing="0" id="scheduleTable" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>Mata Kuliah</th>
                <th>Waktu & Tanggal</th>
                <th>Ruang Lab</th>
                <th>Nama Dosen</th>
                <th>Asisten Lab</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            @forelse($schedules ?? [] as $s)
                {{-- Data attributes dipertahankan agar fungsi filter javascript tidak mati --}}
                <tr class="schedule-row" 
                    data-date="{{ \Carbon\Carbon::parse($s->tanggal)->format('Y-m-d') }}" 
                    data-lab="{{ $s->lab->nama_lab ?? '' }}">
                    
                    <td><strong>{{ $s->matkul }}</strong></td>
                    <td>
                        <span class="time-text">{{ date('H:i', strtotime($s->jam_mulai)) }} - {{ date('H:i', strtotime($s->jam_selesai)) }}</span>
                        <br>
                        <small>{{ $s->hari }}, {{ date('d M Y', strtotime($s->tanggal)) }}</small>
                    </td>
                    <td>{{ $s->lab->nama_lab ?? 'Lab Tidak Ditemukan' }}</td>
                    <td>{{ $s->dosen }}</td>
                    <td>{{ $s->assistantSchedule->nama_asisten ?? '-' }}</td>
                </tr>
            @empty
                <tr id="emptyState">
                    <td colspan="5" style="text-align: center; padding: 20px; color: gray;">
                        Tidak ada jadwal kuliah praktikum pada tanggal ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

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

            // Mesin Cetak PDF Otomatis
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