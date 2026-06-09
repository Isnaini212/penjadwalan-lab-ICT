@extends('layouts.spv')

@section('title', 'Manajemen Lab')

@section('content')
@vite(['resources/css/app.css', 'resources/js/app.js'])
<div class="min-h-screen font-sans text-slate-800">
    {{-- HEADER MANAGEMENT --}}
    <div class="mb-10">
        <h2 class="text-2xl font-bold tracking-tight text-blue-900 sm:text-3xl">Dashboard Manajemen Laboratorium</h2>
        <p class="mt-2 text-sm text-slate-500">Gunakan panel ini untuk memonitor dan menambah kapasitas infrastruktur.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-xl font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-xl font-semibold">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- GRID UTAMA: 2 KOLOM --}}
    <div class="grid gap-8 lg:grid-cols-[1fr_2fr]">
        
        {{-- SISI KIRI: FORM TAMBAH LAB (CARD STYLE) --}}
        <div class="h-fit rounded-2xl border border-white/80 bg-white/80 p-6 shadow-xl shadow-blue-950/5 backdrop-blur">
            <h3 class="mb-5 text-lg font-bold text-blue-900 flex items-center gap-2">
                <span></span> Tambah Lab Baru
            </h3>
            
            <form action="{{ route('spv.buatLab') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Identitas / Nama Lab</label>
                    <input type="text" name="nama_lab" placeholder="Misal: Lab Riset AI" required 
                           class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Kapasitas Mahasiswa</label>
                    <input type="number" name="kapasitas" min="0" placeholder="Contoh: 40" required 
                           class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Deskripsi Fasilitas</label>
                    <textarea name="fasilitas" rows="4" placeholder="Sebutkan PC, AC, Proyektor, dll..." 
                              class="w-full rounded-xl border border-slate-200 bg-white p-4 text-sm font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"></textarea>
                </div>

                <button type="submit" class="mt-2 h-12 w-full rounded-xl bg-blue-700 text-sm font-extrabold uppercase tracking-wide text-white shadow-lg shadow-blue-700/25 transition hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200">
                    Simpan Data Lab
                </button>
            </form>
        </div>

        {{-- SISI KANAN: DAFTAR LAB (GRID CARD STYLE) --}}
        <div class="rounded-2xl border border-white/80 bg-white/40 p-6 backdrop-blur">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-bold text-blue-900 flex items-center gap-2">
                    <span></span> Daftar Lab Tersedia
                </h3>
                <span class="inline-flex items-center rounded-full bg-blue-50 px-4 py-1 text-xs font-bold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                    Total: {{ $labs->count() }} Lab
                </span>
            </div>

            {{-- LOOPING KARTU LAB --}}
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($labs as $lab)
                <div class="flex flex-col justify-between rounded-xl border border-slate-100 bg-white p-5 shadow-md transition hover:shadow-lg hover:border-blue-100">
                    <div>
                        {{-- Perbaikan Tag: Mengganti tag <td> bawaan yang bikin layout rusak --}}
                        <h4 class="text-base font-extrabold tracking-wide text-blue-900 uppercase border-b border-slate-100 pb-3">
                            {{ $lab->nama_lab }}
                        </h4>
                        
                        <div class="mt-4 space-y-2 text-sm text-slate-600">
                            <p class="flex items-center gap-2">
                                <span class="text-slate-400"></span> 
                                <span class="font-semibold text-slate-700">Kapasitas:</span> {{ $lab->kapasitas }} Orang
                            </p>
                            <p class="flex items-start gap-2">
                                <span class="text-slate-400 mt-0.5"></span> 
                                <span><span class="font-semibold text-slate-700">Fasilitas:</span> {{ Str::limit($lab->fasilitas, 80) }}</span>
                            </p>
                        </div>
                    </div>

                    {{-- TOMBOL AKSI KERJA --}}
                    <div class="mt-6 flex gap-3 border-t border-slate-50 pt-4">
                        <button type="button"
                                class="h-9 flex-1 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-600 transition hover:bg-slate-50 hover:text-blue-700 btn-edit-lab" 
                                data-nama="{{ $lab->nama_lab }}"
                                data-kapasitas="{{ $lab->kapasitas }}"
                                data-fasilitas="{{ $lab->fasilitas }}"
                                data-url="{{ route('spv.lab.update', $lab->id_lab) }}">
                            Edit
                        </button>
                        
                        <form action="{{ route('spv.lab.delete', $lab->id_lab) }}" method="POST" class="flex-1">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="h-9 w-full rounded-lg bg-red-50 text-xs font-bold text-red-700 transition hover:bg-red-100" 
                                    onclick="return confirm('Hapus lab ini?')">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>


<div id="modalEditLab" class="modal-overlay hidden fixed inset-0 z-50 items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
    <div class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 shadow-2xl transition-all border border-slate-100">
        <h3 class="text-lg font-bold text-blue-900 mb-5">Edit Data Laboratorium</h3>
        
        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Nama Lab</label>
                <input type="text" name="nama_lab" id="edit_nama_lab" required 
                       class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Kapasitas</label>
                <input type="number" name="kapasitas" id="edit_kapasitas" min="0" required 
                       class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Fasilitas</label>
                <textarea name="fasilitas" id="edit_fasilitas" rows="3" required 
                          class="w-full rounded-xl border border-slate-200 bg-white p-4 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"></textarea>
            </div>

            <div class="mt-6 flex gap-3 pt-2">
                <button type="button" class="h-11 flex-1 rounded-xl bg-slate-100 text-sm font-bold text-slate-600 transition hover:bg-slate-200" 
                        onclick="closeEditModal()">
                    Batal
                </button>
                <button type="submit" class="h-11 flex-1 rounded-xl bg-blue-700 text-sm font-bold text-white shadow-lg shadow-blue-700/25 transition hover:bg-blue-800">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Menangkap semua tombol dengan class 'btn-edit-lab'
        const editButtons = document.querySelectorAll('.btn-edit-lab');
        
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Mengambil data dari atribut tombol yang diklik
                const namaLab = this.getAttribute('data-nama');
                const kapasitas = this.getAttribute('data-kapasitas');
                const fasilitas = this.getAttribute('data-fasilitas');
                const updateUrl = this.getAttribute('data-url');
                
                // Memasukkan data ke dalam field input modal edit
                document.getElementById('edit_nama_lab').value = namaLab;
                document.getElementById('edit_kapasitas').value = kapasitas;
                document.getElementById('edit_fasilitas').value = fasilitas;
                
                // Mengubah action form modal agar mengarah ke route update yang benar
                const form = document.getElementById('editForm');
                form.action = updateUrl; 
                
                // Memunculkan modal dengan mengubah class Tailwind
                const modal = document.getElementById('modalEditLab');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        });
    });

    // Fungsi menutup modal edit
    function closeEditModal() {
        const modal = document.getElementById('modalEditLab');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection