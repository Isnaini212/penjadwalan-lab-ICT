<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dosen;
use App\Models\Ormawa;
use App\Models\Schedule;
use App\Models\Lab; 
use App\Models\User; 
use Carbon\Carbon;

class DosenController extends Controller
{
    public function index()
    {
        
        $myBookings = Dosen::with('lab')->orderBy('created_at', 'desc')->take(10)->get();
        return view('booking.dosen', compact('myBookings'));
    }

    
    public function checkAvailableLabs(Request $request)
    {
        $tanggal = $request->tanggal;
        $mulai = $request->jam_mulai;
        $sks = $request->sks;
        $kapasitas = $request->kapasitas;

        if (Carbon::parse($tanggal)->dayOfWeek === Carbon::SUNDAY) {
            return response()->json([
                'jam_selesai' => '00:00',
                'labs' => []
            ]);
        }

        $jamSelesai = $this->calculateEndTime($tanggal, $mulai, $sks);

        Carbon::setLocale('id');
        $hari = Carbon::parse($tanggal)->translatedFormat('l');

        
        $busySchedules = Schedule::whereDate('tanggal', $tanggal)
            ->where(function($q) use ($mulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)
                  ->where('jam_selesai', '>', $mulai);
            })->pluck('id_lab')->toArray();

        
        $busyDosen = Dosen::where('tanggal', $tanggal)->where('status', 'approved')
            ->where(function($q) use ($mulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)->where('jam_selesai', '>', $mulai);
            })->pluck('id_lab')->toArray();

        
        $busyOrmawa = Ormawa::where('tanggal', $tanggal)->where('status', 'approved')
            ->where(function($q) use ($mulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)->where('jam_selesai', '>', $mulai);
            })->pluck('lab')->toArray();

        $allBusyLabs = array_unique(array_merge($busySchedules, $busyDosen, $busyOrmawa));
        $allLabs = Lab::where('nama_lab', '!=', 'RUANG ASISTEN')->get();

        $response = [];
        foreach ($allLabs as $lab) {
            
            // Lab sibuk JIKA sudah dipesan ATAU kapasitas lab kurang dari kapasitas yang diminta
            $isBusy = in_array($lab->id_lab, $allBusyLabs) || ($kapasitas && $lab->kapasitas < $kapasitas);
            
            $response[] = [
                'id_lab'    => $lab->id_lab, 
                'nama_lab'  => $lab->nama_lab ?? $lab->nm_lab,
                'fasilitas' => $lab->fasilitas ?? 'Tidak ada fasilitas khusus', 
                'is_busy'   => $isBusy
            ];
        }

        return response()->json([
            'jam_selesai' => $jamSelesai,
            'labs' => $response
        ]);
    }
public function store(Request $request)
{
    
    $request->validate([
        'nm_dosen'    => 'required',
        'tanggal'     => 'required',
        'jam_mulai'   => 'required',
        'sks'         => 'required|numeric',
        'id_lab'      => 'required',
        'kapasitas'   => 'required|numeric',
        'keperluan'   => 'required|string',
        'kode_matkul' => 'required|string|max:4',
    ]);

    // Validasi Tanggal & Jam tidak boleh berlalu (masa lalu)
    $bookingDateTime = Carbon::parse($request->tanggal . ' ' . $request->jam_mulai);
    if ($bookingDateTime->isPast()) {
        return back()->withInput()->withErrors(['tanggal' => 'Gagal! Tanggal dan jam peminjaman tidak boleh di masa lalu.']);
    }

    $dayOfWeek = Carbon::parse($request->tanggal)->dayOfWeek;
    if ($dayOfWeek === Carbon::SUNDAY) {
        return back()->withInput()->withErrors(['tanggal' => 'Gagal! Hari Minggu adalah hari libur, tidak bisa melakukan reservasi.']);
    }

    $jam_mulai_formatted = Carbon::parse($request->jam_mulai)->format('H:i');
    if ($dayOfWeek === Carbon::SATURDAY) {
        $allowedSaturday = ['08:00', '10:00', '13:00', '15:00'];
        if (!in_array($jam_mulai_formatted, $allowedSaturday)) {
            return back()->withInput()->withErrors(['jam_mulai' => 'Gagal! Pada hari Sabtu, jam mulai harus salah satu dari: 08:00, 10:00, 13:00, 15:00.']);
        }
    } else {
        $allowedWeekday = ['07:10', '08:00', '08:55', '09:45', '10:40', '11:35', '12:30', '13:25', '14:20', '15:15', '16:10', '17:05', '18:00', '18:45'];
        if (!in_array($jam_mulai_formatted, $allowedWeekday)) {
            return back()->withInput()->withErrors(['jam_mulai' => 'Gagal! Jam mulai tidak valid untuk hari kerja.']);
        }

        // Validasi SKS untuk weekdays
        if ($jam_mulai_formatted === '18:45') {
            if ((int)$request->sks !== 2) {
                return back()->withInput()->withErrors(['sks' => 'Gagal! Untuk Kelas Karyawan (18:45), SKS harus bernilai 2.']);
            }
        } else {
            $weekdayStarts = ['07:10', '08:00', '08:55', '09:45', '10:40', '11:35', '12:30', '13:25', '14:20', '15:15', '16:10', '17:05', '18:00'];
            $startIndex = array_search($jam_mulai_formatted, $weekdayStarts);
            if ($startIndex !== false) {
                $maxSks = min(4, 13 - $startIndex);
                if ((int)$request->sks > $maxSks) {
                    return back()->withInput()->withErrors(['sks' => "Gagal! Untuk jam mulai {$jam_mulai_formatted}, SKS maksimal yang diperbolehkan adalah {$maxSks}."]);
                }
            }
        }
    }

    // Cek kapasitas lab di backend
    $lab = Lab::find($request->id_lab);
    if ($lab && $request->kapasitas > $lab->kapasitas) {
        return back()->with('error', "Gagal! Kapasitas peserta ({$request->kapasitas}) melebihi kapasitas {$lab->nama_lab} ({$lab->kapasitas} kursi).");
    }

    $hari_otomatis = Carbon::parse($request->tanggal)->locale('id')->isoFormat('dddd');

    $jam_selesai_otomatis = $this->calculateEndTime($request->tanggal, $request->jam_mulai, $request->sks);

    // Validasi tabrakan jadwal untuk dosen yang sama (status pending/approved)
    $hasOverlap = Dosen::where('user_id', auth()->id())
        ->where('tanggal', $request->tanggal)
        ->whereIn('status', ['pending', 'approved'])
        ->where(function($q) use ($request, $jam_selesai_otomatis) {
            $q->where('jam_mulai', '<', $jam_selesai_otomatis)
              ->where('jam_selesai', '>', $request->jam_mulai);
        })->exists();

    if ($hasOverlap) {
        return back()->withInput()->withErrors(['tanggal' => 'Gagal! Anda sudah memiliki reservasi lain yang bertabrakan (status Pending/Approved) pada tanggal dan jam tersebut.']);
    }

    $kode_formatted = '(' . strtoupper(trim($request->kode_matkul)) . ')';
    $keperluan_formatted = trim($request->keperluan) . ' ' . $kode_formatted;
    
    Dosen::create([
        'user_id'     => auth()->id(),
        'nm_dosen'    => $request->nm_dosen,
        'tanggal'     => $request->tanggal,
        
        'hari'        => $hari_otomatis,        
        'jam_selesai' => $jam_selesai_otomatis,  
        
        'id_lab'      => $request->id_lab,
        'jam_mulai'   => $request->jam_mulai,
        'kapasitas'   => $request->kapasitas,
        'keperluan'   => $keperluan_formatted,
        'sks'         => $request->sks,
        'status'      => 'pending',
    ]);

    return redirect()->back()->with('success', 'Reservasi laboratorium berhasil dikirim!');
}

public function update(Request $request, $id)
{
    $booking = Dosen::findOrFail($id);

    // Pastikan booking milik user saat ini
    if ($booking->user_id !== auth()->id()) {
        abort(403);
    }

    // Pastikan status belum approved
    if ($booking->status === 'approved') {
        return back()->with('error', 'Gagal! Booking yang sudah disetujui tidak dapat diubah.');
    }

    $request->validate([
        'tanggal'     => 'required|date',
        'jam_mulai'   => 'required',
        'sks'         => 'required|numeric',
        'id_lab'      => 'required',
        'kapasitas'   => 'required|numeric',
        'keperluan'   => 'required|string',
        'kode_matkul' => 'required|string|max:4',
    ]);

    // Validasi Tanggal & Jam tidak boleh berlalu (masa lalu)
    $bookingDateTime = Carbon::parse($request->tanggal . ' ' . $request->jam_mulai);
    if ($bookingDateTime->isPast()) {
        return back()->withInput()->withErrors(['tanggal' => 'Gagal! Tanggal dan jam peminjaman tidak boleh di masa lalu.']);
    }

    $dayOfWeek = Carbon::parse($request->tanggal)->dayOfWeek;
    if ($dayOfWeek === Carbon::SUNDAY) {
        return back()->withInput()->withErrors(['tanggal' => 'Gagal! Hari Minggu adalah hari libur, tidak bisa melakukan reservasi.']);
    }

    $jam_mulai_formatted = Carbon::parse($request->jam_mulai)->format('H:i');
    if ($dayOfWeek === Carbon::SATURDAY) {
        $allowedSaturday = ['08:00', '10:00', '13:00', '15:00'];
        if (!in_array($jam_mulai_formatted, $allowedSaturday)) {
            return back()->withInput()->withErrors(['jam_mulai' => 'Gagal! Pada hari Sabtu, jam mulai harus salah satu dari: 08:00, 10:00, 13:00, 15:00.']);
        }
    } else {
        $allowedWeekday = ['07:10', '08:00', '08:55', '09:45', '10:40', '11:35', '12:30', '13:25', '14:20', '15:15', '16:10', '17:05', '18:00', '18:45'];
        if (!in_array($jam_mulai_formatted, $allowedWeekday)) {
            return back()->withInput()->withErrors(['jam_mulai' => 'Gagal! Jam mulai tidak valid untuk hari kerja.']);
        }

        // Validasi SKS untuk weekdays
        if ($jam_mulai_formatted === '18:45') {
            if ((int)$request->sks !== 2) {
                return back()->withInput()->withErrors(['sks' => 'Gagal! Untuk Kelas Karyawan (18:45), SKS harus bernilai 2.']);
            }
        } else {
            $weekdayStarts = ['07:10', '08:00', '08:55', '09:45', '10:40', '11:35', '12:30', '13:25', '14:20', '15:15', '16:10', '17:05', '18:00'];
            $startIndex = array_search($jam_mulai_formatted, $weekdayStarts);
            if ($startIndex !== false) {
                $maxSks = min(4, 13 - $startIndex);
                if ((int)$request->sks > $maxSks) {
                    return back()->withInput()->withErrors(['sks' => "Gagal! Untuk jam mulai {$jam_mulai_formatted}, SKS maksimal yang diperbolehkan adalah {$maxSks}."]);
                }
            }
        }
    }

    // Cek kapasitas lab
    $lab = Lab::find($request->id_lab);
    if ($lab && $request->kapasitas > $lab->kapasitas) {
        return back()->with('error', "Gagal! Kapasitas peserta ({$request->kapasitas}) melebihi kapasitas {$lab->nama_lab} ({$lab->kapasitas} kursi).");
    }

    $hari_otomatis = Carbon::parse($request->tanggal)->locale('id')->isoFormat('dddd');
    $jam_selesai_otomatis = $this->calculateEndTime($request->tanggal, $request->jam_mulai, $request->sks);

    // Cek apakah waktu booking berubah
    $original_mulai = Carbon::parse($booking->jam_mulai)->format('H:i');
    $original_selesai = Carbon::parse($booking->jam_selesai)->format('H:i');
    $original_tanggal = $booking->tanggal;

    $timeChanged = ($original_tanggal !== $request->tanggal) || 
                    ($original_mulai !== $request->jam_mulai) || 
                    ($original_selesai !== $jam_selesai_otomatis);

    if ($timeChanged) {
        // Validasi tabrakan jadwal untuk dosen yang sama (status pending/approved, kecuali booking saat ini)
        $hasOverlap = Dosen::where('user_id', auth()->id())
            ->where('id_booking', '!=', $id)
            ->where('tanggal', $request->tanggal)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function($q) use ($request, $jam_selesai_otomatis) {
                $q->where('jam_mulai', '<', $jam_selesai_otomatis)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })->exists();

        if ($hasOverlap) {
            return back()->withInput()->withErrors(['tanggal' => 'Gagal! Anda sudah memiliki reservasi lain yang bertabrakan (status Pending/Approved) pada tanggal dan jam tersebut.']);
        }
    }

    $kode_formatted = '(' . strtoupper(trim($request->kode_matkul)) . ')';
    $keperluan_formatted = trim($request->keperluan) . ' ' . $kode_formatted;

    $booking->update([
        'tanggal'     => $request->tanggal,
        'hari'        => $hari_otomatis,
        'jam_selesai' => $jam_selesai_otomatis,
        'id_lab'      => $request->id_lab,
        'jam_mulai'   => $request->jam_mulai,
        'kapasitas'   => $request->kapasitas,
        'keperluan'   => $keperluan_formatted,
        'sks'         => $request->sks,
    ]);

    return redirect()->back()->with('success', 'Reservasi laboratorium berhasil diperbarui!');
}

public function destroy($id)
{
    $booking = Dosen::findOrFail($id);

    // Pastikan booking milik user saat ini
    if ($booking->user_id !== auth()->id()) {
        abort(403);
    }

    // Pastikan status belum approved
    if ($booking->status === 'approved') {
        return back()->with('error', 'Gagal! Booking yang sudah disetujui tidak dapat dihapus.');
    }

    $booking->delete();

    return redirect()->back()->with('success', 'Reservasi laboratorium berhasil dihapus!');
}

private function calculateEndTime($tanggal, $jam_mulai, $sks)
{
    $dayOfWeek = Carbon::parse($tanggal)->dayOfWeek;
    $sks = (int) $sks;

    if ($dayOfWeek === Carbon::SATURDAY) {
        $saturdayEnds = [
            '08:00' => ['08:50', '09:50', '10:40', '11:30'],
            '10:00' => ['10:50', '11:50', '12:40', '13:30'],
            '13:00' => ['13:50', '14:50', '15:40', '16:30'],
            '15:00' => ['15:50', '16:50', '17:40', '18:30'],
        ];
        
        $jam_mulai_formatted = Carbon::parse($jam_mulai)->format('H:i');
        if (isset($saturdayEnds[$jam_mulai_formatted][$sks - 1])) {
            return $saturdayEnds[$jam_mulai_formatted][$sks - 1];
        }
        
        $minutes = $sks * 50;
        return Carbon::parse($jam_mulai)->addMinutes($minutes)->format('H:i');
    } else {
        $jam_mulai_formatted = Carbon::parse($jam_mulai)->format('H:i');
        if ($jam_mulai_formatted === '18:45') {
            return '20:40';
        }
        
        $weekdayStarts = ['07:10', '08:00', '08:55', '09:45', '10:40', '11:35', '12:30', '13:25', '14:20', '15:15', '16:10', '17:05', '18:00'];
        $weekdayEnds   = ['08:00', '08:50', '09:40', '10:35', '11:30', '12:25', '13:20', '14:15', '15:10', '16:05', '17:00', '17:55', '18:50'];
        
        $startIndex = array_search($jam_mulai_formatted, $weekdayStarts);
        if ($startIndex !== false) {
            $endIndex = $startIndex + $sks - 1;
            if ($endIndex < count($weekdayEnds)) {
                return $weekdayEnds[$endIndex];
            } else {
                return end($weekdayEnds);
            }
        }
        
        $minutes = $sks * 50;
        return Carbon::parse($jam_mulai)->addMinutes($minutes)->format('H:i');
    }
}
}