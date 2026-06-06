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