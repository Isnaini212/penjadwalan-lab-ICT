@extends('layouts.spv')
@section('title', 'Manajemen Jadwal')

@section('content')
    <h1>Manajemen Jadwal</h1>
    <p>Halaman khusus operasi CRUD Jadwal Praktikum Lab.</p>

    <hr>

    {{-- ==================================================================
         1. CREATE: FORM TAMBAH JADWAL MANUAL
         ================================================================== --}}
    <h3>Tambah Jadwal Baru</h3>
    <form action="{{ route('spv.store') }}" method="POST">
        @csrf
        <table border="0" cellpadding="5">
            <tr>
                <td><label>Tanggal</label></td>
                <td><input type="date" name="tanggal" required></td>
            </tr>
            <tr>
                <td><label>Ruang Lab</label></td>
                <td>
                    <select name="id_lab" required>
                        <option value="">-- Pilih Lab --</option>
                        @foreach($labs as $lab)
                            <option value="{{ $lab->id_lab }}">{{ $lab->nama_lab }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Jam Mulai</label></td>
                <td><input type="time" name="jam_mulai" required></td>
            </tr>
            <tr>
                <td><label>Jumlah SKS</label></td>
                <td><input type="number" name="sks" required min="1" max="6" placeholder="Contoh: 2"></td>
            </tr>
            <tr>
                <td><label>Mata Kuliah</label></td>
                <td><input type="text" name="matkul" required placeholder="Nama Mata Kuliah"></td>
            </tr>
            <tr>
                <td><label>Nama Dosen</label></td>
                <td><input type="text" name="dosen" required placeholder="Nama Dosen"></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit">Simpan</button></td>
            </tr>
        </table>
    </form>

    <hr>

    {{-- ==================================================================
         2. READ & FILTER: FORM CEK TANGGAL HARIAN
         ================================================================== --}}
    <h3>Filter Berdasarkan Tanggal</h3>
    <form action="{{ route('spv.jadwal') }}" method="GET">
        <label>Cek Jadwal Tanggal:</label>
        <input type="date" name="filter_date" value="{{ request('filter_date', now()->toDateString()) }}" onchange="this.form.submit()">
        @if(request('filter_date'))
            <a href="{{ route('spv.jadwal') }}" style="color:red; font-size:12px;">[Reset Filter]</a>
        @endif
    </form>

    <br>

    {{-- ==================================================================
         3. READ, UPDATE, & DELETE: TABEL UTAMA OPERASI DATA
         ================================================================== --}}
    <table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>Hari & Tanggal</th>
                <th>Ruang Lab</th>
                <th>Jam (Mulai - Selesai)</th>
                <th>Mata Kuliah</th>
                <th>Dosen Pengampu</th>
                <th>Asisten Jaga</th>
                <th>Aksi Kerja</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $s)
                <tr>
                    {{-- Form Utama Untuk Fungsi UPDATE (Inline Form via ID) --}}
                    {{-- Menggunakan Method PATCH sesuai spesifikasi rute web laravel --}}
                    <form action="{{ route('spv.update', $s->id_jadwal) }}" method="POST" id="update-form-{{ $s->id_jadwal }}">
                        @csrf
                        @method('PUT')
                        {{-- Mengirimkan scope pembaruan data --}}
                        <input type="hidden" name="update_scope" value="today_only">
                    </form>

                    {{-- Kolom Tanggal (Update Otomatis via Trigger Onchange) --}}
                    <td>
                        <strong>{{ strtoupper($s->hari) }}</strong><br>
                        <input type="date" name="tanggal" value="{{ \Carbon\Carbon::parse($s->tanggal)->format('Y-m-d') }}" 
                               form="update-form-{{ $s->id_jadwal }}" 
                               onchange="document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
                    </td>

                    {{-- Kolom Lab (Update Otomatis via Dropdown Status) --}}
                    <td>
                        <select name="id_lab" form="update-form-{{ $s->id_jadwal }}" onchange="document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
                            @foreach($s->getLabStatuses() as $lab)
                                @if($s->id_lab == $lab['id_lab'])
                                    <option value="{{ $lab['id_lab'] }}" selected style="color: green; font-weight: bold;">
                                        {{ $lab['nama_lab'] }} (Aktif)
                                    </option>
                                @elseif($lab['status'] === 'busy')
                                    <option value="" disabled style="color: red; background: #fee2e2;">
                                        {{ $lab['nama_lab'] }} (Dipakai)
                                    </option>
                                @else
                                    <option value="{{ $lab['id_lab'] }}">
                                        {{ $lab['nama_lab'] }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </td>

                    {{-- Kolom Jam Sesi --}}
                    <td>
                        <input type="time" name="jam_mulai" value="{{ $s->jam_mulai }}" form="update-form-{{ $s->id_jadwal }}">
                        <span>-</span>
                        <input type="time" name="jam_selesai" value="{{ $s->jam_selesai }}" form="update-form-{{ $s->id_jadwal }}">
                    </td>

                    {{-- Kolom Judul Mata Kuliah --}}
                    <td>
                        <input type="text" name="matkul" value="{{ $s->matkul }}" form="update-form-{{ $s->id_jadwal }}">
                    </td>

                    {{-- Kolom Nama Dosen --}}
                    <td>
                        <input type="text" name="dosen" value="{{ $s->dosen }}" form="update-form-{{ $s->id_jadwal }}">
                    </td>

                    {{-- Kolom Pilihan Asisten Jaga (Update Otomatis via Dropdown Status) --}}
                    <td>
                        <select name="id_asisten" form="update-form-{{ $s->id_jadwal }}" onchange="document.getElementById('update-form-{{ $s->id_jadwal }}').submit();">
                            <option value="">-- Pilih Asisten --</option>
                            @foreach($s->getAssistantStatuses() as $asisten)
                                @if($asisten->is_busy)
                                    @if($s->id_asisten == $asisten->id_asisten)
                                        <option value="{{ $asisten->id_asisten }}" selected style="color: red; font-weight: bold;">
                                            {{ $asisten->nama }} {{ $asisten->label }}
                                        </option>
                                    @else
                                        <option value="" disabled style="color: red; background: #fee2e2;">
                                            {{ $asisten->nama }} {{ $asisten->label }}
                                        </option>
                                    @endif
                                @else
                                    <option value="{{ $asisten->id_asisten }}" {{ $s->id_asisten == $asisten->id_asisten ? 'selected' : '' }}>
                                        {{ $asisten->nama }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </td>

                    {{-- Tombol Submit Pembaruan / Pemicu Penghapusan (DELETE) --}}
                    <td>
                        <div style="display: flex; gap: 5px;">
                            {{-- Tombol Simpan Perubahan Teks (Matkul/Jam/Dosen) --}}
                            <button type="submit" form="update-form-{{ $s->id_jadwal }}" title="Simpan Perubahan Teks">
                                Simpan
                            </button>

                            {{-- Tombol Aksi HAPUS DATA (DELETE) --}}
                            <form method="POST" action="{{ route('spv.delete', $s->id_jadwal) }}" onsubmit="return confirm('Hapus data jadwal ini dari sistem?')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" style="color: red;">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: gray;">
                        Tidak ada agenda kelas praktikum pada tanggal ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection