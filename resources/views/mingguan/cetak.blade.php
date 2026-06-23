<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Minggu Ke-{{ $minggu }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; color: #1e3a8a; }
        .header p { margin: 4px 0 0 0; font-size: 12px; font-weight: bold; color: #555; }
        table { w-full: 100%; width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f2f5fa; color: #1e3a8a; font-weight: bold; text-transform: uppercase; font-size: 10px; border: 1px solid #cbd5e1; padding: 10px 8px; }
        td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
        .center { text-align: center; }
        .font-mono { font-family: monospace; font-size: 11px; font-weight: bold; }
        .text-muted { color: #888; font-style: italic; }
        .day-separator { background-color: #1e3a8a; color: white; padding: 8px 12px; font-weight: bold; font-size: 12px; margin-top: 20px; text-transform: uppercase; border: 1px solid #1e3a8a; border-bottom: none; }
        .day-table { margin-top: 0; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Jadwal Kegiatan & Praktikum Laboratorium ICT</h1>
        <p>Laporan Jadwal Perkuliahan: Minggu Ke {{ $minggu }} ({{ \Carbon\Carbon::parse($activeRange['start'])->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($activeRange['end'])->translatedFormat('d F Y') }})</p>
    </div>

    @php
        $groupedSchedules = $schedules->groupBy('tanggal');
    @endphp

    @forelse($groupedSchedules as $tanggal => $dailySchedules)
        @php
            $hari = $dailySchedules->first()->hari;
            $tanggalFormat = \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y');
        @endphp
        
        <div class="day-separator">
            {{ $hari }}, {{ $tanggalFormat }}
        </div>

        <table class="day-table">
            <thead>
                <tr>
                    <th style="width: 18%;">Laboratorium</th>
                    <th style="width: 15%;">Waktu Sesi</th>
                    <th style="width: 30%;">Nama Mata Kuliah</th>
                    <th style="width: 22%;">Dosen Pengampu</th>
                    <th style="width: 15%;">Asisten Praktikum</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailySchedules as $sch)
                    <tr>
                        <td class="center" style="font-weight: bold; color: #2563eb;">
                            {{ $sch->lab->nama_lab ?? 'Lab Terhapus' }}
                        </td>
                        <td class="center font-mono">
                            {{ substr($sch->jam_mulai, 0, 5) }} - {{ substr($sch->jam_selesai, 0, 5) }}
                        </td>
                        <td>
                            <strong>{{ $sch->matkul }}</strong><br>
                            <span class="text-muted">{{ $sch->sks }} SKS</span>
                        </td>
                        <td>{{ $sch->dosen }}</td>
                        <td class="center">
                            @if($sch->assistants->isNotEmpty())
                                <span style="font-weight: bold; color: #16a34a;">{{ $sch->assistant_names }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <table>
            <tr>
                <td class="center" style="padding: 30px; font-size: 13px; color: #666;">
                    <strong>Tidak ada agenda / jadwal praktikum yang terdaftar pada Minggu ke-{{ $minggu }}.</strong>
                </td>
            </tr>
        </table>
    @endforelse

</body>
</html>