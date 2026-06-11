@extends('layouts.spv')

@section('title', 'Manajemen Akun')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Manajemen Akun</h1>
        <p class="text-slate-500 font-medium mt-1">Kelola dan buat akses portal untuk Asisten, Ormawa, dan Dosen.</p>
    </div>
</div>

{{-- Alert Success --}}
@if(session('success'))
    <div class="mb-6 rounded-xl bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 border border-emerald-200 flex items-center gap-3 shadow-sm">
        <i class="fas fa-check-circle text-emerald-500 text-xl"></i> 
        <div>
            {!! session('success') !!}
        </div>
    </div>
@endif

{{-- Alert Errors --}}
@if ($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 px-5 py-4 text-sm font-bold text-red-700 border border-red-200 shadow-sm">
        <div class="flex items-center gap-2 mb-2">
            <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
            <span class="text-base">Gagal memproses data:</span>
        </div>
        <ul class="list-disc pl-8 text-xs font-medium text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    {{-- KIRI: FORM BUAT AKUN (Lebar 1 Kolom di Desktop) --}}
    <div class="lg:col-span-1">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/40 sticky top-6">
            <div class="mb-6 border-b border-slate-100 pb-4">
                <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                    <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600"><i class="fas fa-user-plus"></i></div> 
                    Buat Akun Baru
                </h2>
            </div>

            <form action="{{ route('akun') }}" method="POST" id="create-account-form" class="space-y-5">
                @csrf
                
                {{-- Nama --}}
                <div>
                    <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Nama / Organisasi <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="name" required placeholder="Cth: BEM FTI" value="{{ old('name') }}" 
                               class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 px-4 pl-11 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                        <i class="fas fa-id-card absolute left-4 top-3 text-slate-400"></i>
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Email Login <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="email" name="email" required placeholder="Cth: bem@lab.com" value="{{ old('email') }}" 
                               class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 px-4 pl-11 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                        <i class="fas fa-envelope absolute left-4 top-3 text-slate-400"></i>
                    </div>
                </div>

                {{-- Role --}}
                <div>
                    <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Hak Akses <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="role" required class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 px-4 pl-11 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 appearance-none cursor-pointer">
                            <option value="" disabled selected>-- Pilih Role --</option>
                            <option value="asisten" {{ old('role') == 'asisten' ? 'selected' : '' }}>Asisten Lab</option>
                            <option value="ormawa" {{ old('role') == 'ormawa' ? 'selected' : '' }}>Ormawa</option>
                            <option value="dosen" {{ old('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        </select>
                        <i class="fas fa-user-shield absolute left-4 top-3 text-slate-400"></i>
                        <i class="fas fa-chevron-down absolute right-4 top-3.5 text-slate-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

                {{-- Password dengan Dadu Random --}}
                <div>
                    <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">Password Awal <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input type="text" id="password-input" name="password" required minlength="8" placeholder="Min. 8 karakter" value="{{ old('password', 'password123') }}" 
                                   class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 px-4 pl-11 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                            <i class="fas fa-key absolute left-4 top-3 text-slate-400"></i>
                        </div>
                        {{-- Tombol Dadu --}}
                        <button type="button" onclick="generatePassword()" class="flex items-center justify-center bg-indigo-50 border border-indigo-200 text-indigo-600 rounded-xl px-4 hover:bg-indigo-600 hover:text-white transition group focus:outline-none focus:ring-4 focus:ring-indigo-500/20" title="Acak Password">
                            <i class="fas fa-dice text-lg group-hover:rotate-180 transition-transform duration-300"></i>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5 font-bold">* Klik ikon dadu untuk acak password.</p>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" id="btn-submit" class="inline-flex w-full justify-center items-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-black text-white uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition hover:bg-indigo-700 hover:-translate-y-0.5 focus:ring-4 focus:ring-indigo-600/20">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- KANAN: STATISTIK & TABEL (Lebar 2 Kolom di Desktop) --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Widget Statistik --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-center">
                <span class="text-xs font-extrabold uppercase text-slate-400">Total Akun</span>
                <span class="text-2xl font-black text-slate-800">{{ $users->count() }}</span>
            </div>
            <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100 shadow-sm flex flex-col justify-center">
                <span class="text-xs font-extrabold uppercase text-emerald-600">Asisten</span>
                <span class="text-2xl font-black text-emerald-700">{{ $users->where('role', 'asisten')->count() }}</span>
            </div>
            <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100 shadow-sm flex flex-col justify-center">
                <span class="text-xs font-extrabold uppercase text-blue-600">Ormawa</span>
                <span class="text-2xl font-black text-blue-700">{{ $users->where('role', 'ormawa')->count() }}</span>
            </div>
            <div class="bg-purple-50 p-4 rounded-2xl border border-purple-100 shadow-sm flex flex-col justify-center">
                <span class="text-xs font-extrabold uppercase text-purple-600">Dosen</span>
                <span class="text-2xl font-black text-purple-700">{{ $users->where('role', 'dosen')->count() }}</span>
            </div>
        </div>

        {{-- Tabel Daftar Akun --}}
        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-xl shadow-slate-200/40">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider"><i class="fas fa-users text-slate-400 mr-2"></i> Daftar Pengguna Terdaftar</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 text-[11px] uppercase tracking-widest">
                            <th class="px-6 py-4 font-extrabold border-b border-slate-100">Nama Lengkap</th>
                            <th class="px-6 py-4 font-extrabold border-b border-slate-100">Email Login</th>
                            <th class="px-6 py-4 font-extrabold border-b border-slate-100">Role</th>
                            <th class="px-6 py-4 font-extrabold border-b border-slate-100 text-center">Status Sandi</th>
                            <th class="px-6 py-4 font-extrabold border-b border-slate-100 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4">
                                    <form id="update-user-{{ $user->id }}" action="{{ route('akun.update', $user) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('PATCH')
                                    </form>
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ old('name', $user->name) }}"
                                        form="update-user-{{ $user->id }}"
                                        required
                                        class="h-10 w-full min-w-[180px] rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10"
                                    >
                                </td>
                                <td class="px-6 py-4">
                                    <input
                                        type="email"
                                        name="email"
                                        value="{{ old('email', $user->email) }}"
                                        form="update-user-{{ $user->id }}"
                                        required
                                        class="h-10 w-full min-w-[210px] rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-600 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10"
                                    >
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->role == 'asisten')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-100 text-emerald-700 text-xs font-black">
                                            <i class="fas fa-microscope text-[10px]"></i> Asisten
                                        </span>
                                    @elseif($user->role == 'ormawa')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-100 text-blue-700 text-xs font-black">
                                            <i class="fas fa-users text-[10px]"></i> Ormawa
                                        </span>
                                    @elseif($user->role == 'dosen')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-purple-100 text-purple-700 text-xs font-black">
                                            <i class="fas fa-chalkboard-teacher text-[10px]"></i> Dosen
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{-- Info keamanan: Password disensor --}}
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded border border-slate-200" title="Password dienkripsi (Hash)">
                                        <i class="fas fa-lock text-[10px]"></i> Terlindungi
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button
                                        type="submit"
                                        form="update-user-{{ $user->id }}"
                                        class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 text-xs font-black uppercase tracking-wide text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700"
                                    >
                                        <i class="fas fa-save"></i>
                                        Perbarui
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-medium">
                                    <i class="fas fa-folder-open text-3xl mb-3 text-slate-200 block"></i>
                                    Belum ada akun yang didaftarkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</div>

<script>
    // Efek Loading Saat Submit Form
    document.getElementById('create-account-form').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit');
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Menyimpan...';
        btn.classList.add('opacity-70', 'pointer-events-none');
    });

    // Fungsi Generate Random Password (Kombinasi Huruf & Angka)
    function generatePassword() {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$&";
        let password = "";
        for (let i = 0; i < 10; i++) { // Generate 10 karakter
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        
        const passInput = document.getElementById('password-input');
        passInput.value = password;
        
        // Kasih efek blink hijau sebentar biar ketara ada perubahan
        passInput.classList.add('bg-emerald-50', 'border-emerald-400');
        setTimeout(() => {
            passInput.classList.remove('bg-emerald-50', 'border-emerald-400');
        }, 500);
    }
</script>
@endsection
