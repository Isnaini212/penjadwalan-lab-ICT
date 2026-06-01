@extends('layouts.spv')
@section('title', 'Kontrol Display TV')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 24px; font-weight: 800; color: #0f172a;">Pengaturan Tampilan TV Display</h1>
        <p style="font-size: 14px; color: #64748b;">Kelola informasi teks berjalan dan unggah gambar promosi/pengumuman ke monitor TV.</p>
    </div>

    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 16px;">Pengumuman & Agenda Teks Berjalan (News Ticker)</h3>
        
        <form action="/spv/tv/text" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <textarea name="message" rows="3" required placeholder="Ketik teks pengumuman di sini..." style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; font-family: inherit; font-size: 14px; outline: none; resize: vertical;">{{ $announcement ? $announcement->message : '' }}</textarea>
            </div>
            <div style="text-align: right;">
                <button type="submit" style="background: #0284c7; color: white; border: none; padding: 10px 20px; font-size: 14px; font-weight: 700; border-radius: 8px; cursor: pointer; transition: 0.2s;">Simpan Teks Pengumuman</button>
            </div>
        </form>
    </div>

    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #1e293b;">Daftar Slide Gambar Aktif</h3>
                <p style="font-size: 13px; color: #64748b; margin-top: 2px;">Jumlah Slide Gambar Saat Ini: <strong>{{ count($slides) }} Gambar</strong></p>
            </div>
            
            <form action="/spv/tv/slide" method="POST" enctype="multipart/form-data" style="display: flex; gap: 10px; align-items: center;">
                @csrf
                <input type="file" name="image" accept=".jpg,.jpeg,.png" required style="font-size: 13px; color: #64748b;">
                <button type="submit" style="background: #16a34a; color: white; border: none; padding: 8px 16px; font-size: 13px; font-weight: 700; border-radius: 8px; cursor: pointer;">+ Unggah Slide Baru</button>
            </form>
        </div>

        @if(count($slides) > 0)
            <div style="display: grid; grid-template-cols: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
                @foreach($slides as $index => $slide)
                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; background: #f8fafc; position: relative;">
                        <div style="height: 130px; width: 100%; background: #000; display: flex; align-items: center; justify-content: center;">
                            <img src="{{ asset('storage/' . $slide->image_path) }}" alt="Slide" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>
                        <div style="padding: 12px; display: flex; justify-content: space-between; align-items: center; background: white;">
                            <span style="font-size: 12px; font-weight: 700; color: #64748b;">Slide Gambar {{ $index + 1 }}</span>
                            
                            <form action="/spv/tv/slide/{{ $slide->id }}" method="POST" onsubmit="return confirm('Hapus gambar slide ini dari tampilan TV?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #ef4444; font-size: 13px; font-weight: 700; cursor: pointer;">Hapus</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 40px 0; color: #94a3b8; font-size: 14px; border: 2px dashed #e2e8f0; border-radius: 8px;">
                Belum ada berkas gambar slide JPG yang diunggah. Monitor TV saat ini hanya menampilkan slide tabel jadwal kuliah utama.
            </div>
        @endif
    </div>

</div>
@endsection