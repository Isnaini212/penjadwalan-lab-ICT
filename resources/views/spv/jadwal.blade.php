<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>jadwal</title>
</head>
<body>
    Kelola data jadwal Lab ICT

    @if (@session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (isset($editJadwal))
    edit data jadwal
    <from action="{{ route('spv.update', $editJadwal->id_jadwal) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="date" name="tanggal" value="{{ $editJadwal->tanggal }}" placeholder="Tanggal">
       <select name="lab" id="lab">
            <option value="">Pilih Lab</option>
            <option value="Lab 1" {{ $editJadwal->lab == 'Lab 1' ? 'selected' : '' }}>Lab 1</option>
            <option value="Lab 2" {{ $editJadwal->lab == 'Lab 2' ? 'selected' : '' }}>Lab 2</option>
            <option value="Lab 3" {{ $editJadwal->lab == 'Lab 3' ? 'selected' : '' }}>Lab 3</option>
        </select>
        <input type="time" name="jam_mulai" value="{{ $editJadwal->jam_mulai }}" placeholder="Jam Mulai">
        <input type="number" name="sks" value="{{ $editJadwal->sks }}" placeholder="SKS">
        <input type="text" name="matkul" value="{{ $editJadwal->matkul }}" placeholder="Mata Kuliah">
        <input type="text" name="dosen" value="{{ $editJadwal->dosen }}" placeholder="Dosen">
        <input type="text" name="nama_asisten" value="{{ $editJadwal->nama_asisten }}" placeholder="Nama Asisten">
        <button type="submit">Update</button>

        @else
        tambah data jadwal
        <form action="{{ route('spv.store') }}" method="POST">
            @csrf
            <input type="date" name="tanggal" placeholder="Tanggal">
            <select name="id_lab" id="id_lab" class="form-control" required>
        @foreach($Lab as $l)
            <option value="{{ $l->id_lab }}">{{ $l->nama_lab }}</option>
        @endforeach
    </select>
            <input type="time" name="jam_mulai" placeholder="Jam Mulai">
            <input type="number" name="sks" placeholder="SKS">
            <input type="text" name="matkul" placeholder="Mata Kuliah">
            <input type="text" name="dosen" placeholder="Dosen">
            <input type="text" name="nama_asisten" placeholder="Nama Asisten">
            <button type="submit">Simpan</button>
        </form>
    @endif
</body>
</html>