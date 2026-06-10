<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal {{ $nama }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; margin: 0; padding: 0; color: #1e293b; }
        .title { text-align: center; margin-bottom: 15px; }
        .title h2 { margin: 0; font-size: 18px; color: #0f172a; text-transform: uppercase; }
        .title p { margin: 5px 0 0 0; color: #64748b; font-size: 11px; }
        
        table { width: 100%; border-collapse: collapse; text-align: center; }
        th, td { border: 1px solid #94a3b8; padding: 6px; }
        th { background-color: #1e293b; color: #ffffff; font-weight: bold; text-transform: uppercase; }
        .time-col { background-color: #f8fafc; font-weight: bold; width: 85px; }
        
        .kuliah { background-color: #fca5a5; color: #7f1d1d; font-weight: bold; }
        .asisten { background-color: #0ea5e9; color: #ffffff; font-weight: bold; }
        .ra { background-color: #fef08a; color: #854d0e; font-weight: bold; }
        .kosong { background-color: #ffffff; color: #cbd5e1; }
        .sholat { background-color: #2563eb; color: #ffffff; font-weight: bold; letter-spacing: 1px; }
    </style>
</head>
<body>

    <div class="title">
        <h2>TIMELINE JADWAL MINGGUAN</h2>
        <p>Asisten: <b>{{ strtoupper($nama) }}</b> | Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="background-color: #0f172a;">WAKTU</th>
                @foreach($dayNames as $day)
                    <th>{{ $day }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($timeSlots as $slot)
                <tr>
                    <td class="time-col">{{ $slot['label'] }}</td>

                    @foreach($dayNames as $day)
                        @if(strtolower($day) === 'jumat' && $slot['start'] === '11:40')
                            <td class="sholat">SHOLAT JUMAT</td>
                        @else
                            @php
                                $kelasKuliah = $weeklyClasses->first(function($item) use ($day, $slot) {
                                    if (strtolower($item->hari) !== strtolower($day)) return false;
                                    $itemStart = substr($item->jam_mulai, 0, 5);
                                    $itemEnd   = substr($item->jam_selesai, 0, 5);
                                    return ($itemStart < $slot['end'] && $itemEnd > $slot['start']);
                                });

                                $jadwalKalender = $assistantAllSchedules->first(function($item) use ($day, $slot) {
                                    if (strtolower($item->hari) !== strtolower($day)) return false;
                                    $itemStart = substr($item->jam_mulai, 0, 5);
                                    $itemEnd   = substr($item->jam_selesai, 0, 5);
                                    return ($itemStart < $slot['end'] && $itemEnd > $slot['start']);
                                });

                                $status = 'KOSONG';
                                $isStartHour = false;
                                $labelTampil = '';
                                
                                if ($jadwalKalender) {
                                    $isStartHour = (substr($jadwalKalender->jam_mulai, 0, 5) === $slot['start']);
                                    
                                    // 🌟 SAKTI 2: Ambil Nama Lab dari Relasi
                                    $namaLab = $jadwalKalender->lab->nama_lab ?? 'LAB TIDAK DIKETAHUI';
                                    
                                    $status = (strtoupper(trim($namaLab)) === 'RUANG RA' || strtoupper(trim($namaLab)) === 'RA') ? 'RA' : 'ASISTEN_LAB';
                                    $labelTampil = strtoupper($namaLab); 
                                    
                                } elseif ($kelasKuliah) {
                                    $isStartHour = (substr($kelasKuliah->jam_mulai, 0, 5) === $slot['start']);
                                    $status = 'KULIAH_SENDIRI';
                                    $labelTampil = strtoupper(\Illuminate\Support\Str::limit($kelasKuliah->mata_kuliah, 15));
                                }
                            @endphp

                            @if($status === 'KULIAH_SENDIRI')
                                <td class="kuliah">{{ $isStartHour ? $labelTampil : $labelTampil }}</td>
                            @elseif($status === 'RA')
                                <td class="ra">{{ $isStartHour ? 'RA' : 'RA' }}</td>
                            @elseif($status === 'ASISTEN_LAB')
                                <td class="asisten">{{ $isStartHour ? $labelTampil : $labelTampil }}</td>
                            @else
                                <td class="kosong">---</td>
                            @endif
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>