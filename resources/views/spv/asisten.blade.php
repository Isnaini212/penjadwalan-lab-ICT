@extends('layouts.spv')

@section('title', 'Jadwal & Edit Asisten')

@section('content')
<div class="min-h-screen font-sans text-slate-800">
  
    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-blue-900 sm:text-3xl">Manajemen Jadwal Asisten</h1>
        <p class="mt-2 text-sm text-slate-500">Kelola jadwal, hari, dan mata kuliah yang dipegang oleh asisten secara langsung.</p>
    </div>

  
    <div class="mb-8 rounded-2xl border border-blue-200 bg-blue-50/80 p-6 shadow-xl shadow-blue-950/5 backdrop-blur">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-base font-bold text-blue-950 flex items-center gap-2">
                <span></span> Unggah Jadwal Asisten (Excel/CSV)
            </h3>
            
           
            <form action="{{ route('asisten.clear') }}" method="POST" 
                  onsubmit="return confirm('PERINGATAN KERAS!\n\nAnda yakin ingin menghapus SEMUA data jadwal asisten?\nTindakan ini tidak bisa dibatalkan!')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-xs font-bold uppercase tracking-wider text-white shadow-md transition hover:bg-red-700">
                    <i class="fas fa-trash-alt"></i> Kosongkan Semua Data
                </button>
            </form>
        </div>
        
        <form action="{{ route('spv.importAsisten') }}" method="POST" enctype="multipart/form-data" class="grid gap-4 sm:grid-cols-[1fr_auto] items-end">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Pilih File Master Jadwal</label>
                <input type="file" name="file_asisten" required 
                       class="w-full rounded-xl border border-slate-200 bg-white p-2.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-500">
            </div>

            <button type="submit" class="h-11 rounded-xl bg-blue-700 px-6 text-sm font-extrabold uppercase tracking-wide text-white shadow-lg shadow-blue-700/25 transition hover:bg-blue-800">
                Import
            </button>
        </form>
    </div>

    {{-- FORM PENCARIAN FILTER GANDA (NAMA & HARI) --}}
    <div class="mb-6 rounded-2xl border border-white/80 bg-white/80 p-6 shadow-xl shadow-blue-950/5 backdrop-blur">
        <form action="{{ route('spv.asisten') }}" method="GET" id="form-cari-asisten" class="flex flex-wrap items-end gap-4">
            
            {{-- Dropdown Nama --}}
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Pilih Nama Asisten:</label>
                <select name="nama" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100" onchange="this.form.submit()">
                    <option value="">-- Semua Asisten --</option>
                    @foreach($semuaAsisten as $asisten)
                        <option value="{{ $asisten->nama_asisten }}" {{ $namaDicari == $asisten->nama_asisten ? 'selected' : '' }}>
                            {{ $asisten->nama_asisten }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Dropdown Hari --}}
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Pilih Hari:</label>
                <select name="hari" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100" onchange="this.form.submit()">
                    <option value="">-- Semua Hari --</option>
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hariOption)
                        <option value="{{ $hariOption }}" {{ $hariDicari == $hariOption ? 'selected' : '' }}>{{ $hariOption }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Tombol Reset --}}
            @if($namaDicari || $hariDicari)
                <a href="{{ route('spv.asisten') }}" class="inline-flex h-11 items-center gap-2 rounded-xl bg-red-50 px-5 text-sm font-bold text-red-700 transition hover:bg-red-100">
                    <i class="fas fa-times"></i> Bersihkan Filter
                </a>
            @endif
        </form>
    </div>
    
   
    @if($namaDicari || $hariDicari)
        <div class="overflow-hidden rounded-2xl border border-white/80 bg-white shadow-2xl shadow-blue-950/5">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex flex-wrap items-center justify-between gap-2">
                <h3 class="text-sm font-bold text-blue-900">
                    Jadwal: <span class="text-blue-600">{{ $namaDicari ?? 'Semua Asisten' }}</span> 
                    <span class="mx-2 text-slate-300">|</span> 
                    Hari: <span class="text-amber-600">{{ $hariDicari ?? 'Semua Hari' }}</span>
                </h3>
                <span class="text-xs font-medium italic text-slate-400">*Gunakan tombol aksi di sebelah kanan untuk memperbarui data.</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1000px] border-collapse text-left text-sm" id="asistenTable">
                    <thead class="bg-blue-900 text-white text-xs font-extrabold uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Nama Asisten</th>
                            <th class="px-6 py-4 w-40">Hari</th>
                            <th class="px-6 py-4 w-64">Jam (Mulai - Selesai)</th>
                            <th class="px-6 py-4">Mata Kuliah Kelolaan</th>
                            <th class="px-6 py-4 text-right w-44">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($asistenSchedules as $a)
                        <tr class="transition hover:bg-blue-50/40">
                         
                            <td colspan="5" class="p-0">
                                <form action="{{ route('asisten.update', $a->id_asisten) }}" method="POST" class="grid grid-cols-[1fr_10rem_16rem_1fr_11rem] items-center px-0 m-0">
                                    @csrf
                                    @method('PATCH')

                                    <div class="px-6 py-3.5">
                                        @if($namaDicari)
                                            <input type="hidden" name="nama_asisten" value="{{ $a->nama_asisten }}">
                                            <div class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-500 cursor-not-allowed">
                                                <i class="fas fa-lock text-[10px]"></i> {{ $a->nama_asisten }}
                                            </div>
                                        @else
                                            <input type="text" name="nama_asisten" value="{{ $a->nama_asisten }}" 
                                                   class="h-9 w-full rounded-lg border border-slate-200 px-3 font-bold text-blue-900 outline-none focus:border-blue-500 focus:bg-blue-50/30">
                                        @endif
                                    </div>

                                    
                                    <div class="px-4 py-3.5">
                                        <select name="hari" class="h-9 w-full rounded-lg border border-slate-200 px-2 font-semibold text-slate-700 outline-none focus:border-blue-500">
                                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                                                <option value="{{ $h }}" {{ strtolower($a->hari) == strtolower($h) ? 'selected' : '' }}>{{ $h }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                              
                                    <div class="px-4 py-3.5 flex items-center gap-2">
                                        <input type="time" name="jam_mulai" value="{{ \Carbon\Carbon::parse($a->jam_mulai)->format('H:i') }}" 
                                               class="h-9 w-24 rounded-lg border border-slate-200 px-2 text-center font-medium text-slate-700 outline-none focus:border-blue-500">
                                        <span class="text-slate-400 font-bold">-</span>
                                        <input type="time" name="jam_selesai" value="{{ \Carbon\Carbon::parse($a->jam_selesai)->format('H:i') }}" 
                                               class="h-9 w-24 rounded-lg border border-slate-200 px-2 text-center font-medium text-slate-700 outline-none focus:border-blue-500">
                                    </div>

                                   
                                    <div class="px-4 py-3.5">
                                        <input type="text" name="mata_kuliah" value="{{ $a->mata_kuliah }}" 
                                               class="h-9 w-full rounded-lg border border-slate-200 px-3 font-semibold text-slate-700 outline-none focus:border-blue-500">
                                    </div>

                       
                                    <div class="px-6 py-3.5 flex items-center justify-end gap-2">
                                        <button type="submit" class="inline-flex h-8 items-center rounded-lg bg-blue-50 px-3 text-xs font-bold text-blue-700 transition hover:bg-blue-100">
                                            Simpan
                                        </button>
                                </form> 
                                        
                                        <form method="POST" action="{{ route('asisten.destroy', $a->id_asisten) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-8 items-center rounded-lg bg-red-50 px-3 text-xs font-bold text-red-700 transition hover:bg-red-100">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-slate-400">
                                <i class="fas fa-calendar-times text-4xl text-slate-300 mb-3"></i>
                                <p class="text-sm font-semibold">Tidak ada data jadwal asisten yang cocok dengan filter.</p>
                            </td>
                        </tr>
                        @endforelse

                        
                        <tr class="bg-slate-50/80 border-t-2 border-slate-200">
                            <td colspan="5" class="p-0">
                                <form action="{{ route('asisten.store') }}" method="POST" class="grid grid-cols-[1fr_10rem_16rem_1fr_11rem] items-center px-0 m-0">
                                    @csrf
                                    
                                    <div class="px-6 py-4">
                                        @if($namaDicari)
                                            <input type="hidden" name="nama_asisten" value="{{ $namaDicari }}">
                                            <div class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700">
                                                <i class="fas fa-lock text-[10px]"></i> {{ $namaDicari }}
                                            </div>
                                        @else
                                            <select name="nama_asisten" class="h-9 w-full rounded-lg border border-emerald-300 px-3 font-semibold text-slate-700 outline-none focus:border-emerald-500" required>
                                                <option value="">-- Pilih Asisten --</option>
                                                @foreach($semuaAsisten as $asisten)
                                                    <option value="{{ $asisten->nama_asisten }}">{{ $asisten->nama_asisten }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>

                                    <div class="px-4 py-4">
                                        <select name="hari" class="h-9 w-full rounded-lg border border-emerald-300 px-2 font-semibold text-slate-700 outline-none focus:border-emerald-500" required>
                                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                                                <option value="{{ $h }}" {{ $hariDicari == $h ? 'selected' : '' }}>{{ $h }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="px-4 py-4 flex items-center gap-2">
                                        <input type="time" name="jam_mulai" class="h-9 w-24 rounded-lg border border-emerald-300 px-2 text-center font-medium text-slate-700 outline-none focus:border-emerald-500" required>
                                        <span class="text-emerald-500 font-extrabold">+</span>
                                        <select name="sks" class="h-9 w-20 rounded-lg border border-emerald-300 px-2 font-semibold text-slate-700 outline-none focus:border-emerald-500" required>
                                            <option value="1">1 SKS</option>
                                            <option value="2">2 SKS</option>
                                            <option value="3">3 SKS</option>
                                        </select>
                                    </div>

                                    <div class="px-4 py-4">
                                        <input type="text" name="mata_kuliah" placeholder="Ketik nama matkul baru..." required
                                               class="h-9 w-full rounded-lg border border-emerald-300 px-3 font-semibold text-slate-700 outline-none focus:border-emerald-500">
                                    </div>

                                    <div class="px-6 py-4 text-right">
                                        <button type="submit" class="inline-flex h-9 items-center gap-1.5 rounded-xl bg-emerald-600 px-4 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-emerald-600/10 transition hover:bg-emerald-700">
                                            <i class="fas fa-plus"></i> Tambah
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @else
      
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white p-14 text-center">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 text-slate-400 mb-4 ring-8 ring-slate-100/50">
                <i class="fas fa-filter text-xl"></i>
            </div>
            <h3 class="text-base font-bold text-slate-700">Gunakan Filter Terlebih Dahulu</h3>
            <p class="mt-1 text-sm text-slate-400 max-w-sm">Silakan pilih opsi Nama Asisten atau Hari di atas untuk mulai memuat, memantau, dan mengubah isi jadwal.</p>
        </div>
    @endif
</div>
@endsection