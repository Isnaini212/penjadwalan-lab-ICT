@extends('layouts.spv')
@section('title', 'Manajemen Lab')

@section('content')
    <h1>Manajemen Laboratorium</h1>
    <p>Halaman khusus operasi CRUD Ruang Laboratorium.</p>

    <hr>

    {{-- 1. NOTIFIKASI / ALERT --}}
    @if(session('success'))
        <div style="color: green; background-color: #e6f4ea; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="color: red; background-color: #fce8e6; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Ganti isi dari file lab.blade.php kamu dari bagian FORM TAMBAH sampai TABEL saja --}}

    {{-- 2. CREATE: FORM TAMBAH LAB BARU --}}
    <h3>Tambah Lab Baru</h3>
    <form action="{{ route('spv.buatLab') }}" method="POST">
        @csrf
        <table border="0" cellpadding="5">
            <tr>
                <td><label>Nama Lab</label></td>
                <td><input type="text" name="nama_lab" placeholder="Contoh: lab komputer" required></td>
            </tr>
            <tr>
                <td><label>Kapasitas</label></td>
                {{-- Ditambahkan atribut min="1" agar tidak bisa di-down ke minus --}}
                <td><input type="number" name="kapasitas" min="1" placeholder="Contoh: 40" required></td>
            </tr>
            <tr>
                <td><label>Fasilitas</label></td>
                <td><input type="text" name="fasilitas" placeholder="Contoh: AC, PC, Proyektor" required></td>
            </tr>
        </table>
        <button type="submit" style="margin-top: 10px; background-color: blue; color: white; padding: 5px 10px; cursor: pointer;">Simpan Lab</button>
    </form>

    <hr>

    {{-- 3. READ, UPDATE, & DELETE: TABEL DAFTAR LAB --}}
    <h3>Daftar Laboratorium</h3>
    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; text-align: left; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>ID Lab</th>
                <th>Nama Lab</th>
                <th>Kapasitas</th>
                <th>Fasilitas</th>
                <th>Aksi / Operasi</th>
            </tr>
        </thead>
        <tbody>
            @if($labs->isEmpty())
                <tr>
                    <td colspan="5" style="text-align: center;">Belum ada data laboratorium.</td>
                </tr>
            @else
                @foreach($labs as $item)
                    <tr>
                        {{-- PERBAIKAN: Route diarahkan ke 'spv.lab.update' --}}
                        <form action="{{ route('spv.lab.update', $item->id_lab) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <td>{{ $item->id_lab }}</td>
                            <td>
                                <input type="text" name="nama_lab" value="{{ $item->nama_lab }}" required>
                            </td>
                            <td>
                                {{-- Ditambahkan atribut min="1" pada kolom edit --}}
                                <input type="number" name="kapasitas" value="{{ $item->kapasitas }}" min="1" style="width: 60px;" required>
                            </td>
                            <td>
                                <input type="text" name="fasilitas" value="{{ $item->fasilitas }}" style="width: 90%;" required>
                            </td>
                            <td>
                                <button type="submit" style="background-color: orange; color: white; padding: 3px 8px; cursor: pointer; border: none; border-radius: 3px;">
                                    Update
                                </button>
                        </form>

                                {{-- PERBAIKAN: Route diarahkan ke 'spv.lab.delete' --}}
                                <form action="{{ route('spv.lab.delete', $item->id_lab) }}" method="POST" style="display: inline; margin-left: 5px;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lab ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background-color: red; color: white; padding: 3px 8px; cursor: pointer; border: none; border-radius: 3px;">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
@endsection