<<<<<<< HEAD
@extends('layouts.app') {{-- Sesuaikan dengan layout asisten lu --}}

@section('title', 'Input Jadwal Kuliah Pribadi')

@section('content')
<div class="mb-10 rounded-2xl border border-slate-200 bg-white p-6 md:p-8 shadow-xl shadow-slate-200/40">
    
    {{-- Header Section --}}
    <div class="mb-8 border-b border-slate-100 pb-5">
        <h3 class="text-xl font-extrabold text-slate-900 md:text-2xl">
            <i class="fas fa-user-graduate mr-2 text-indigo-500"></i> Form Input Jadwal Kuliah Asisten
        </h3>
        <p class="mt-2 text-sm font-medium text-slate-500">
            Silakan isi kotak di bawah dengan <b>Nama Mata Kuliah</b> Anda. Biarkan kosong (---) pada jam yang Anda sedang <b>Free / Tidak Ada Kuliah</b>.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 border border-emerald-200 flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-emerald-500 text-xl"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('simput') }}" method="POST">
        @csrf

        {{-- Input Nama Asisten --}}
        <div class="mb-8 w-full md:max-w-md">
            <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                Nama Lengkap Asisten <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <i class="fas fa-user text-sm"></i>
                </div>
                <input type="text" name="nama_asisten" required placeholder="Contoh: Budi Santoso" 
                       class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 pl-11 pr-4 text-sm font-bold text-slate-800 shadow-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
            </div>
        </div>

        {{-- Data Time Slots Setup --}}
        @php
            $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $timeSlots = [
                ['start' => '08:00', 'end' => '08:50', 'label' => '08:00 - 08:50'],
                ['start' => '08:55', 'end' => '09:45', 'label' => '08:55 - 09:45'],
                ['start' => '09:50', 'end' => '10:40', 'label' => '09:50 - 10:40'],
                ['start' => '10:45', 'end' => '11:35', 'label' => '10:45 - 11:35'],
                ['start' => '12:30', 'end' => '13:20', 'label' => '12:30 - 13:20'],
                ['start' => '13:25', 'end' => '14:15', 'label' => '13:25 - 14:15'],
                ['start' => '14:20', 'end' => '15:10', 'label' => '14:20 - 15:10'],
                ['start' => '15:15', 'end' => '16:05', 'label' => '15:15 - 16:05'],
                ['start' => '16:10', 'end' => '17:00', 'label' => '16:10 - 17:00'],
                ['start' => '18:00', 'end' => '18:50', 'label' => '18:00 - 18:50'],
                ['start' => '18:55', 'end' => '19:45', 'label' => '18:55 - 19:45'],
                ['start' => '19:50', 'end' => '20:40', 'label' => '19:50 - 20:40'],
                ['start' => '20:45', 'end' => '21:35', 'label' => '20:45 - 21:35'],
            ];
        @endphp

        {{-- Tabel Matrix Interaktif --}}
        <div class="overflow-x-auto rounded-xl border border-slate-300 shadow-sm custom-scrollbar">
            <table class="w-full min-w-[1000px] border-collapse text-center font-sans text-[12px]">
                <thead>
                    <tr class="bg-slate-800 text-white">
                        <th class="border border-slate-700 bg-slate-900 p-3.5 font-extrabold w-[130px] tracking-wider uppercase text-xs">Waktu</th>
                        @foreach($dayNames as $day)
                            <th class="border border-slate-700 p-3.5 font-black tracking-wide uppercase w-[150px]">
                                {{ $day }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($timeSlots as $slot)
                        <tr class="transition hover:bg-slate-50">
                            {{-- Kolom Waktu --}}
                            <td class="border border-slate-200 bg-slate-50 p-2 font-extrabold text-slate-600 tracking-wider">
                                {{ $slot['label'] }}
                            </td>

                            {{-- Looping Kotak Input Matkul --}}
                            @foreach($dayNames as $day)
                                @if(strtolower($day) === 'jumat' && in_array($slot['start'], ['11:35', '12:30']))
                                    <td class="border border-slate-200 bg-slate-200 p-2 text-[11px] font-black uppercase text-slate-500 tracking-widest shadow-inner">
                                        SHOLAT JUMAT / BREAK
                                    </td>
                                @else
                                    <td class="border border-slate-200 bg-white p-1 transition-colors duration-300 relative group">
                                        <input type="text" 
                                               name="matrix[{{ $day }}][{{ $slot['start'] }}]" 
                                               placeholder="--- Kosong ---" 
                                               oninput="warnainKotak(this)"
                                               class="w-full bg-transparent text-center text-[12px] font-bold text-slate-700 placeholder:text-slate-300 placeholder:font-normal outline-none py-2 px-1 focus:bg-indigo-50 focus:text-indigo-700 rounded transition">
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Tombol Submit --}}
        <div class="mt-8 flex justify-end">
            <button type="submit" id="btn-submit-jadwal" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-8 py-3.5 text-sm font-black text-white shadow-lg shadow-indigo-600/30 transition hover:bg-indigo-700 hover:-translate-y-0.5 focus:ring-4 focus:ring-indigo-600/20">
                <i class="fas fa-paper-plane"></i> Kirim Jadwal Kuliah Saya
            </button>
        </div>
    </form>
</div>

{{-- Skrip Interaktif: Otomatis ganti warna kalau asisten ngetik di kotak --}}
<script>
function warnainKotak(inputEl) {
    const tdParent = inputEl.parentElement;
    
    if (inputEl.value.trim() !== '') {
        // Kalau diketik, kotaknya jadi merah muda (Tanda Sibuk)
        tdParent.classList.remove('bg-white');
        tdParent.classList.add('bg-red-100');
        inputEl.classList.add('text-red-800', 'font-black');
    } else {
        // Kalau kosong, balik jadi putih
        tdParent.classList.remove('bg-red-100');
        tdParent.classList.add('bg-white');
        inputEl.classList.remove('text-red-800', 'font-black');
    }
}

document.getElementById('btn-submit-jadwal')?.closest('form').addEventListener('submit', function() {
    const btn = document.getElementById('btn-submit-jadwal');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan Jadwal...';
    btn.classList.add('opacity-70', 'pointer-events-none');
});
</script>
@endsection
=======
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Manual Jadwal Asisten</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">

    {{-- Navbar --}}
    <nav class="sticky top-0 z-50 border-b border-slate-200 bg-white/80 py-4 backdrop-blur-md shadow-sm">
        <div class="container mx-auto px-6 flex items-center justify-between max-w-7xl">
            <div class="flex items-center gap-3 font-black text-emerald-600 text-xl tracking-tight">
                <i class="fas fa-clipboard-list text-2xl"></i>
                <span>PORTAL ASISTEN</span>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-10 max-w-7xl">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-200/40 overflow-hidden">
            
            {{-- Header Card --}}
            <div class="border-b border-slate-100 bg-slate-50 px-6 py-5">
                <h3 class="text-lg font-extrabold text-slate-900">
                    <i class="fas fa-calendar-plus mr-2 text-emerald-500"></i> Tambah Jadwal Manual
                </h3>
                <p class="mt-1 text-sm font-medium text-slate-500">
                    Masukkan jadwal kesibukan kuliah Anda baris demi baris.
                </p>
            </div>

            {{-- Pesan Notifikasi --}}
            @if(session('success'))
                <div class="m-6 rounded-xl bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 border border-emerald-200">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            {{-- FORM UTAMA (Ditaruh di luar tabel biar HTML valid) --}}
            <form action="{{ route('simput') }}" method="POST" id="form-quick-add">
                @csrf
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-800 text-white font-extrabold uppercase tracking-wider text-xs">
                        <tr>
                            <th class="px-6 py-4">Nama Asisten</th>
                            <th class="px-4 py-4">Hari</th>
                            <th class="px-4 py-4">Waktu (Mulai - Selesai)</th>
                            <th class="px-4 py-4 w-full">Mata Kuliah</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        
                        {{-- ========================================================== --}}
                        {{-- 1. BARIS INPUT MANUAL (Quick Add) - Selalu di Paling Atas --}}
                        {{-- ========================================================== --}}
                        <tr class="bg-emerald-50/60 transition border-b-2 border-emerald-200">
                            
                            {{-- Nama Asisten --}}
                            <td class="px-6 py-4">
                                @php
                                    $namaDicari = request('nama_asisten', session('last_asisten', '')); 
                                    $hariDicari = request('hari', session('last_hari', ''));
                                @endphp

                                @if($namaDicari)
                                    <input type="hidden" name="nama_asisten" value="{{ $namaDicari }}" form="form-quick-add">
                                    <div class="inline-flex items-center gap-1.5 rounded-lg bg-blue-100 px-3 py-2 text-xs font-bold text-blue-700 border border-blue-200">
                                        <i class="fas fa-lock text-[10px]"></i> {{ strtoupper($namaDicari) }}
                                    </div>
                                    <a href="{{ url()->current() }}" class="ml-2 text-xs font-bold text-red-500 hover:underline">Ganti</a>
                                @else
                                    <select name="nama_asisten" form="form-quick-add" class="h-10 w-48 rounded-lg border border-emerald-300 bg-white px-3 font-bold text-slate-700 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" required>
                                        
                                    </select>
                                @endif
                            </td>

                            {{-- Hari --}}
                            <td class="px-4 py-4">
                                <select name="hari" form="form-quick-add" class="h-10 w-32 rounded-lg border border-emerald-300 bg-white px-3 font-bold text-slate-700 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" required>
                                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                                        <option value="{{ $h }}" {{ $hariDicari == $h ? 'selected' : '' }}>{{ $h }}</option>
                                    @endforeach
                                </select>
                            </td>

                            {{-- Jam Mulai & SKS --}}
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <input type="text" name="jam_mulai" form="form-quick-add" placeholder="08:00" maxlength="5" 
                                           class="time-formatter h-10 w-28 rounded-lg border border-emerald-300 bg-white px-2 text-center font-bold tracking-widest font-mono text-slate-700 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" required>
                                    
                                    <span class="text-emerald-500 font-black">+</span>
                                    
                                    <select name="sks" form="form-quick-add" class="h-10 w-24 rounded-lg border border-emerald-300 bg-white px-2 font-bold text-slate-700 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" required>
                                        <option value="1">1 SKS</option>
                                        <option value="2">2 SKS</option>
                                        <option value="3">3 SKS</option>
                                        <option value="4">4 SKS</option>
                                    </select>
                                </div>
                            </td>

                            {{-- Mata Kuliah --}}
                            <td class="px-4 py-4">
                                <input type="text" name="mata_kuliah" form="form-quick-add" placeholder="Ketik nama matkul..." class="h-10 w-full min-w-[200px] rounded-lg border border-emerald-300 bg-white px-4 font-bold text-slate-700 placeholder:text-slate-400 placeholder:font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" required>
                            </td>

                            {{-- Tombol Submit --}}
                            <td class="px-6 py-4 text-right">
                                <button type="submit" form="form-quick-add" class="inline-flex h-10 items-center gap-2 rounded-xl bg-emerald-600 px-5 text-xs font-black uppercase tracking-wider text-white shadow-lg shadow-emerald-600/30 transition hover:bg-emerald-700 hover:-translate-y-0.5">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </td>
                        </tr>

                        {{-- ========================================================== --}}
                        {{-- 2. OUTPUT DATA YANG SUDAH DI-INPUT --}}
                        {{-- ========================================================== --}}
                        @forelse($jadwalTersimpan ?? [] as $jadwal)
                            <tr class="bg-white hover:bg-slate-50 transition">
                                <td class="px-6 py-3.5 font-bold text-slate-700">
                                    {{ strtoupper($jadwal->nama_asisten) }}
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-slate-600">
                                    {{ $jadwal->hari }}
                                </td>
                                <td class="px-4 py-3.5 font-mono font-bold text-indigo-600 tracking-widest">
                                    {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                </td>
                                <td class="px-4 py-3.5 font-bold text-slate-800">
                                    {{ $jadwal->mata_kuliah }}
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    {{-- Form Hapus Per Baris --}}
                                    <form action="{{ route('asisten.schedule.delete', $jadwal->id ?? $jadwal->id_asisten) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-8 items-center gap-1.5 rounded-lg bg-red-50 px-3 text-xs font-bold text-red-600 transition hover:bg-red-500 hover:text-white">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <i class="fas fa-folder-open text-4xl mb-3 text-slate-300"></i>
                                        <span class="font-bold">Belum ada jadwal yang diinput.</span>
                                        <span class="text-xs font-medium mt-1">Silakan tambahkan jadwal pada form hijau di atas.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
<script>
// MESIN AUTO-FORMAT JAM (Anti AM/PM Device)
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('time-formatter')) {
        let inputVal = e.target.value.replace(/\D/g, ''); 
        if (inputVal.length > 4) inputVal = inputVal.substring(0, 4);
        
        let formatted = inputVal;
        if (inputVal.length > 2) {
            formatted = inputVal.substring(0, 2) + ':' + inputVal.substring(2, 4);
        }
        e.target.value = formatted;
    }
});

// VALIDASI MAX 23:59 SAAT PINDAH KOLOM
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('time-formatter')) {
        let val = e.target.value;
        if (val.length === 5) {
            let parts = val.split(':');
            let hours = parseInt(parts[0], 10);
            let mins = parseInt(parts[1], 10);
            
            if (hours > 23) hours = 23;
            if (mins > 59) mins = 59;
            if (isNaN(hours)) hours = 0;
            if (isNaN(mins)) mins = 0;
            
            let hrStr = hours < 10 ? '0' + hours : hours;
            let mnStr = mins < 10 ? '0' + mins : mins;
            
            e.target.value = hrStr + ':' + mnStr;
        } else if (val.length > 0 && val.length < 5) {
            alert('Format jam kurang lengkap, Bre! Ketik 4 angka, contoh: 0800');
            e.target.value = '';
        }
    }
});
</script>
</html>
>>>>>>> 574c1955332e32a2f334accb1cf984c901d88e6f
