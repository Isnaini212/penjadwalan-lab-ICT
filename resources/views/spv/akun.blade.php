@extends('layouts.spv')

@section('title', 'Buat Akun Pengguna')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Akun</h1>
    <p class="text-slate-500 font-medium mt-1">Buat akun akses portal untuk Asisten, Ormawa, atau Dosen.</p>
</div>

{{-- Alert Success --}}
@if(session('success'))
    <div class="mb-6 rounded-xl bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 border border-emerald-200 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-500 text-lg"></i> {{ session('success') }}
    </div>
@endif

{{-- Alert Errors dari Validasi Controller --}}
@if ($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 px-5 py-4 text-sm font-bold text-red-700 border border-red-200">
        <div class="flex items-center gap-2 mb-2">
            <i class="fas fa-exclamation-triangle text-red-500"></i>
            <span>Gagal membuat akun:</span>
        </div>
        <ul class="list-disc pl-8 text-xs font-medium text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="rounded-2xl border border-slate-200 bg-white p-6 md:p-8 shadow-xl shadow-slate-200/40 max-w-4xl">
    <div class="mb-8 border-b border-slate-100 pb-5 flex items-center justify-between">
        <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
            <i class="fas fa-user-plus text-indigo-500"></i> Formulir Registrasi Akun
        </h2>
    </div>

    <form action="{{ route('spv.account.store') }}" method="POST" id="create-account-form">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Nama --}}
            <div class="md:col-span-2">
                <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Nama Lengkap / Nama Organisasi <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="text" name="name" required placeholder="Cth: BEM FTI / Dr. Budi Santoso" value="{{ old('name') }}" 
                           class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 pl-12 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                    <i class="fas fa-id-card absolute left-4 top-3.5 text-slate-400 text-lg"></i>
                </div>
            </div>

            {{-- Email --}}
            <div class="md:col-span-2">
                <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Alamat Email (Untuk Login) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="email" name="email" required placeholder="Cth: ormawa@kampus.ac.id" value="{{ old('email') }}" 
                           class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 pl-12 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                    <i class="fas fa-envelope absolute left-4 top-3.5 text-slate-400 text-lg"></i>
                </div>
            </div>

            {{-- Role --}}
            <div class="md:col-span-1">
                <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Hak Akses (Role) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select name="role" required class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 pl-12 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 appearance-none cursor-pointer">
                        <option value="" disabled selected>-- Pilih Hak Akses --</option>
                        <option value="asisten" {{ old('role') == 'asisten' ? 'selected' : '' }}>Asisten Lab</option>
                        <option value="ormawa" {{ old('role') == 'ormawa' ? 'selected' : '' }}>Ormawa (BEM / HIMA)</option>
                        <option value="dosen" {{ old('role') == 'dosen' ? 'selected' : '' }}>Dosen / Staf Fakultas</option>
                    </select>
                    <i class="fas fa-user-shield absolute left-4 top-3.5 text-slate-400 text-lg"></i>
                    {{-- Ikon panah ke bawah untuk select --}}
                    <i class="fas fa-chevron-down absolute right-4 top-4 text-slate-400 text-xs pointer-events-none"></i>
                </div>
            </div>

            {{-- Password (Dengan nilai default) --}}
            <div class="md:col-span-1">
                <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Password Awal <span class="text-red-500">*</span></label>
                <div class="relative">
                    {{-- Gua set defaultnya password123 biar SPV gak capek ngetik, tapi tetep bisa diganti kalau mau --}}
                    <input type="text" name="password" required minlength="8" placeholder="Minimal 8 karakter" value="{{ old('password', 'password123') }}" 
                           class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 px-4 pl-12 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                    <i class="fas fa-key absolute left-4 top-3.5 text-slate-400 text-lg"></i>
                </div>
                <p class="text-[10px] text-slate-400 mt-1.5 font-bold">* Default sistem adalah <span class="text-indigo-500">password123</span></p>
            </div>

        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
            <button type="submit" id="btn-submit" class="inline-flex w-full md:w-auto justify-center items-center gap-2 rounded-xl bg-indigo-600 px-8 py-3.5 text-sm font-black text-white uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition hover:bg-indigo-700 hover:-translate-y-0.5 focus:ring-4 focus:ring-indigo-600/20">
                <i class="fas fa-save"></i> Simpan Akun
            </button>
        </div>
    </form>
</div>

<script>
    // Efek Loading Saat Disubmit biar ga diklik dobel sama SPV
    document.getElementById('create-account-form').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit');
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Menyimpan...';
        btn.classList.add('opacity-70', 'pointer-events-none');
    });
</script>
@endsection