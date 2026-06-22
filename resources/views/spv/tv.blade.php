@extends('layouts.spv')
@section('title', 'Kontrol Display TV')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Pengaturan TV Display</h1>
        <p class="text-slate-500 font-medium mt-1 text-sm">Kelola teks berjalan dan gambar slide untuk monitor TV.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 border border-emerald-200 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500 text-xl"></i> {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 px-5 py-4 text-sm font-bold text-red-700 border border-red-200">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
                <span>Gagal:</span>
            </div>
            <ul class="list-disc pl-8 text-xs font-medium text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card: Teks Berjalan & Settings --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-xl shadow-slate-200/40">
        <div class="mb-4 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-700 flex-shrink-0">
                <i class="fas fa-desktop"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Teks Berjalan & Pengaturan Slide Jadwal</h3>
                <p class="text-xs text-slate-400 mt-0.5">Kelola teks pengumuman running text dan durasi tampilan slide jadwal kuliah utama</p>
            </div>
        </div>

        <form action="/spv/tv/text" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">Teks Berjalan</label>
                <textarea name="message" rows="3" required
                          placeholder="Ketik teks pengumuman di sini..."
                          class="w-full rounded-xl border border-slate-300 bg-slate-50 p-4 text-sm font-semibold text-slate-700 outline-none transition focus:border-sky-500 focus:bg-white focus:ring-4 focus:ring-sky-500/10 resize-y">{{ $announcement ? $announcement->message : '' }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">Durasi Tampilan Slide Jadwal Kuliah (Detik)</label>
                <div class="flex items-center gap-3 max-w-xs">
                    <input type="number" name="schedule_delay" min="3" max="300" 
                           value="{{ $announcement ? ($announcement->schedule_delay ?? 15) : 15 }}" required
                           class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 px-4 text-sm font-bold text-slate-700 outline-none transition focus:border-sky-500 focus:bg-white focus:ring-4 focus:ring-sky-500/10">
                    <span class="text-sm font-bold text-slate-500">Detik</span>
                </div>
                <p class="text-[10px] font-semibold text-slate-400 mt-1">*Durasi rotasi khusus untuk slide halaman tabel jadwal kuliah.</p>
            </div>

            <div class="flex justify-end pt-2 border-t border-slate-100">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-6 py-2.5 text-sm font-black text-white shadow-lg shadow-sky-600/25 transition hover:bg-sky-700">
                    <i class="fas fa-save"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    {{-- Card: Slide Gambar --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-200/40 overflow-hidden">

        {{-- Card Header --}}
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-images text-slate-400"></i> Daftar Slide Gambar Aktif
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Jumlah slide saat ini: <strong>{{ count($slides) }} gambar</strong></p>
                </div>

                {{-- Upload Form --}}
                <form action="/spv/tv/slide" method="POST" enctype="multipart/form-data"
                      class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
                    @csrf
                    <input type="file" name="image" accept=".jpg,.jpeg,.png" required onchange="checkFileExtensionTv(this)"
                           class="flex-1 text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-sky-100 file:px-3 file:py-2 file:text-xs file:font-bold file:text-sky-700 hover:file:bg-sky-200 cursor-pointer rounded-xl border border-slate-200 bg-slate-50 py-2 px-3">
                    
                    {{-- Input Delay --}}
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                        <span class="text-xs font-bold text-slate-500 whitespace-nowrap">Delay (Detik):</span>
                        <input type="number" name="delay" min="3" max="300" value="15" required
                               class="w-14 bg-transparent text-xs font-bold text-slate-700 focus:outline-none text-center">
                    </div>

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-black text-white shadow-lg shadow-emerald-600/25 transition hover:bg-emerald-700 whitespace-nowrap">
                        <i class="fas fa-plus"></i> Unggah Slide
                    </button>
                </form>
            </div>
        </div>

        {{-- Grid Slide --}}
        <div class="p-5 sm:p-6">
            @if(count($slides) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($slides as $index => $slide)
                        <div class="rounded-xl border border-slate-200 overflow-hidden bg-slate-50 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                            <div>
                                <div class="h-28 sm:h-32 w-full bg-slate-900 flex items-center justify-center relative">
                                    <img src="{{ asset('storage/' . $slide->image_path) }}"
                                         alt="Slide {{ $index + 1 }}"
                                         class="max-w-full max-h-full object-contain">
                                    <span class="absolute top-2 left-2 bg-blue-900/80 backdrop-blur-sm text-white px-2 py-0.5 rounded text-[10px] font-bold">
                                        {{ $slide->delay ?? 15 }} Detik
                                    </span>
                                </div>
                                
                                {{-- Inline Delay Form --}}
                                <div class="p-3 border-b border-slate-100 bg-white">
                                    <form action="{{ route('spv.tv.slide.update_delay', $slide->id) }}" method="POST" class="flex items-center justify-between gap-1.5">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex items-center gap-1">
                                            <span class="text-[10px] font-extrabold text-slate-400 uppercase">Delay:</span>
                                            <input type="number" name="delay" min="3" max="300" value="{{ $slide->delay ?? 15 }}" required
                                                   class="w-12 rounded border border-slate-200 px-1 py-0.5 text-center text-xs font-bold text-slate-700 focus:border-sky-500 focus:outline-none bg-slate-50">
                                            <span class="text-[10px] font-bold text-slate-400">detik</span>
                                        </div>
                                        <button type="submit" class="bg-sky-50 text-sky-600 hover:bg-sky-600 hover:text-white px-2 py-0.5 rounded text-[10px] font-bold transition border border-sky-200">
                                            Update
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="px-3 py-2.5 flex items-center justify-between bg-white/50">
                                <span class="text-xs font-bold text-slate-500">Slide {{ $index + 1 }}</span>
                                <form action="/spv/tv/slide/{{ $slide->id }}" method="POST"
                                      onsubmit="return handleCustomConfirmSubmit(event, 'Hapus slide ini?', 'Konfirmasi Hapus')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs font-black text-red-500 hover:text-red-700 transition">
                                        <i class="fas fa-trash-alt mr-1"></i>Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 border-2 border-dashed border-slate-200 rounded-xl">
                    <i class="fas fa-photo-video text-4xl text-slate-300 mb-3 block"></i>
                    <p class="text-sm font-bold text-slate-400">Belum ada gambar slide yang diunggah.</p>
                    <p class="text-xs text-slate-400 mt-1">Monitor TV hanya menampilkan jadwal kuliah utama.</p>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- Custom Alert Modal --}}
<div id="custom-alert-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" style="transition: opacity 0.3s ease;">
    <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl text-center transform transition-transform scale-95" id="custom-alert-box" style="transition: transform 0.3s ease;">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 text-amber-500">
            <i class="fas fa-exclamation-triangle text-3xl"></i>
        </div>
        <h3 class="mb-2 text-lg font-extrabold text-slate-800" id="custom-alert-title">Peringatan</h3>
        <p class="mb-6 text-sm font-medium text-slate-600" id="custom-alert-message">Pesan peringatan akan muncul di sini.</p>
        <button type="button" onclick="closeCustomAlert()" class="w-full rounded-xl bg-slate-800 py-3 text-sm font-bold text-white shadow-md transition hover:bg-slate-700">
            Mengerti
        </button>
    </div>
</div>

{{-- Custom Confirm Modal --}}
<div id="custom-confirm-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0" style="transition: opacity 0.3s ease;">
    <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl text-center transform transition-transform scale-95" id="custom-confirm-box" style="transition: transform 0.3s ease;">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-500">
            <i class="fas fa-question-circle text-3xl"></i>
        </div>
        <h3 class="mb-2 text-lg font-extrabold text-slate-800" id="custom-confirm-title">Konfirmasi</h3>
        <p class="mb-6 text-sm font-medium text-slate-600" id="custom-confirm-message">Apakah Anda yakin?</p>
        <div class="flex gap-3">
            <button type="button" onclick="closeCustomConfirm()" class="w-full rounded-xl bg-slate-100 py-3 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-200">
                Batal
            </button>
            <button type="button" id="custom-confirm-yes-btn" class="w-full rounded-xl bg-red-600 py-3 text-sm font-bold text-white shadow-md transition hover:bg-red-700">
                Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>

<script>
let currentConfirmCallback = null;

function checkFileExtensionTv(input) {
    if (input.files.length > 0) {
        const file = input.files[0];
        const validExtensions = ['.jpg', '.jpeg', '.png'];
        const fileName = file.name.toLowerCase();
        const isValid = validExtensions.some(ext => fileName.endsWith(ext));

        if (!isValid) {
            showCustomAlert('File tidak valid! Tolong hanya masukkan file dengan format gambar (.jpg, .jpeg, atau .png).', 'Format File');
            input.value = ''; // Kosongkan input file
        }
    }
}

function showCustomAlert(message, title = 'Perhatian!') {
    document.getElementById('custom-alert-title').innerText = title;
    document.getElementById('custom-alert-message').innerText = message;

    const modal = document.getElementById('custom-alert-modal');
    const box = document.getElementById('custom-alert-box');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        box.classList.remove('scale-95');
        box.classList.add('scale-100');
    }, 10);
}

function closeCustomAlert() {
    const modal = document.getElementById('custom-alert-modal');
    const box = document.getElementById('custom-alert-box');

    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    box.classList.remove('scale-100');
    box.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 300);
}

function handleCustomConfirmSubmit(event, message, title = 'Konfirmasi') {
    event.preventDefault();
    showCustomConfirm(message, title, function () {
        event.target.submit();
    });
    return false;
}

function showCustomConfirm(message, title, onConfirm) {
    document.getElementById('custom-confirm-title').innerText = title;
    document.getElementById('custom-confirm-message').innerText = message;
    currentConfirmCallback = onConfirm;

    const modal = document.getElementById('custom-confirm-modal');
    const box = document.getElementById('custom-confirm-box');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        box.classList.remove('scale-95');
        box.classList.add('scale-100');
    }, 10);

    document.getElementById('custom-confirm-yes-btn').onclick = function() {
        closeCustomConfirm();
        if (currentConfirmCallback) currentConfirmCallback();
    };
}

function closeCustomConfirm() {
    const modal = document.getElementById('custom-confirm-modal');
    const box = document.getElementById('custom-confirm-box');

    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    box.classList.remove('scale-100');
    box.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 300);
}
</script>
@endsection
