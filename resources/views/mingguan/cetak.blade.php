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
    </style>
</head>
<body>

    <div class="header">
        <h1>Jadwal Kegiatan & Praktikum Laboratorium ICT</h1>
        <p>Laporan Jadwal Perkuliahan: Minggu Ke {{ $minggu }} ({{ \Carbon\Carbon::parse($activeRange['start'])->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($activeRange['end'])->translatedFormat('d F Y') }})</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Hari & Tanggal</th>
                <th style="width: 15%;">Laboratorium</th>
                <th style="width: 12%;">Waktu Sesi</th>
                <th style="width: 25%;">Nama Mata Kuliah</th>
                <th style="width: 18%;">Dosen Pengampu</th>
                <th style="width: 15%;">Asisten Praktikum</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $sch)
                <tr>
                    <td class="center">
                        <strong>{{ $sch->hari }}</strong><br>
                        <span class="text-muted">{{ \Carbon\Carbon::parse($sch->tanggal)->translatedFormat('d M Y') }}</span>
                    </td>
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
                        @if($sch->assistantSchedule)
                            <span style="font-weight: bold; color: #16a34a;">{{ $sch->assistantSchedule->nama_asisten }}</span>
                        @else
                            <span class="text-muted">Kosong</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center" style="padding: 30px; font-size: 13px; color: #666;">
                        <strong>Tidak ada agenda / jadwal praktikum yang terdaftar pada Minggu ke-{{ $minggu }}.</strong>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>