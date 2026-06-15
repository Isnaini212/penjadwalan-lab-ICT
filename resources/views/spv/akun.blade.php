@extends('layouts.spv')

@section('title', 'Manajemen Akun')

@section('content')

{{-- Header --}}
<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Manajemen Akun</h1>
    <p class="text-slate-500 font-medium mt-1 text-sm sm:text-base">Kelola dan buat akses portal untuk Asisten, Ormawa, dan Dosen.</p>
</div>

{{-- Alert Success --}}
@if(session('success'))
    <div class="mb-6 rounded-xl bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 border border-emerald-200 flex items-start gap-3 shadow-sm">
        <i class="fas fa-check-circle text-emerald-500 text-xl mt-0.5"></i>
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
        <ul class="list-disc pl-8 text-xs font-medium text-red-600 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

    {{-- KIRI: FORM BUAT AKUN --}}
    <div class="lg:col-span-1">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-xl shadow-slate-200/40 lg:sticky lg:top-6">
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
                    <label for="name" class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                        Nama / Organisasi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <input type="text" id="name" name="name" required placeholder="Cth: BEM FTI" value="{{ old('name') }}"
                               class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 pl-11 pr-4 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                        Email Login <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input type="email" id="email" name="email" required placeholder="Cth: bem@lab.com" value="{{ old('email') }}"
                               class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 pl-11 pr-4 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                    </div>
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                        Hak Akses <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <select id="role" name="role" required
                                class="w-full appearance-none cursor-pointer rounded-xl border border-slate-300 bg-slate-50 py-2.5 pl-11 pr-10 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Pilih Role --</option>
                            <option value="asisten" {{ old('role') == 'asisten' ? 'selected' : '' }}>Asisten Lab</option>
                            <option value="ormawa" {{ old('role') == 'ormawa' ? 'selected' : '' }}>Ormawa</option>
                            <option value="dosen" {{ old('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 text-xs">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password-input" class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                        Password Awal <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <i class="fas fa-key"></i>
                            </div>
                            <input type="text" id="password-input" name="password" required minlength="8" placeholder="Min. 8 karakter" value="{{ old('password') }}"
                                   class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 pl-11 pr-4 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                        </div>

                        {{-- Tombol Copy --}}
                        <button type="button" id="copy-btn" onclick="copyPassword()" title="Salin Password"
                                class="flex w-12 flex-shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-500/10">
                            <i class="fas fa-copy"></i>
                        </button>

                        {{-- Tombol Dadu --}}
                        <button type="button" onclick="generatePassword()" title="Acak Password"
                                class="group flex w-12 flex-shrink-0 items-center justify-center rounded-xl border border-indigo-200 bg-indigo-50 text-indigo-600 transition hover:bg-indigo-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-indigo-500/20">
                            <i class="fas fa-dice text-lg transition-transform duration-300 group-hover:rotate-180"></i>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5 font-bold">
                        Password acak dibuat otomatis. Salin sebelum disimpan, atau klik dadu untuk mengacak ulang.
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" id="btn-submit" class="inline-flex w-full justify-center items-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-black text-white uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition hover:bg-indigo-700 hover:-translate-y-0.5 focus:ring-4 focus:ring-indigo-600/20">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- KANAN: STATISTIK & TABEL --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Widget Statistik --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                    <i class="fas fa-users"></i>
                </div>
                <span class="block text-xs font-extrabold uppercase text-slate-400">Total Akun</span>
                <span class="block text-2xl font-black text-slate-800">{{ $users->count() }}</span>
            </div>
            <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100 shadow-sm">
                <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                    <i class="fas fa-microscope"></i>
                </div>
                <span class="block text-xs font-extrabold uppercase text-emerald-600">Asisten</span>
                <span class="block text-2xl font-black text-emerald-700">{{ $users->where('role', 'asisten')->count() }}</span>
            </div>
            <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100 shadow-sm">
                <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                    <i class="fas fa-people-group"></i>
                </div>
                <span class="block text-xs font-extrabold uppercase text-blue-600">Ormawa</span>
                <span class="block text-2xl font-black text-blue-700">{{ $users->where('role', 'ormawa')->count() }}</span>
            </div>
            <div class="bg-purple-50 p-4 rounded-2xl border border-purple-100 shadow-sm">
                <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <span class="block text-xs font-extrabold uppercase text-purple-600">Dosen</span>
                <span class="block text-2xl font-black text-purple-700">{{ $users->where('role', 'dosen')->count() }}</span>
            </div>
        </div>

        {{-- ===== Desktop: Tabel ===== --}}
        <div class="hidden md:block rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-xl shadow-slate-200/40">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">
                    <i class="fas fa-users text-slate-400 mr-2"></i> Daftar Pengguna Terdaftar
                </h3>
                <span class="text-xs font-bold text-slate-400">{{ $users->count() }} akun</span>
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
                            @php
                                $roleMap = [
                                    'asisten' => ['label' => 'Asisten', 'icon' => 'fa-microscope', 'classes' => 'bg-emerald-100 text-emerald-700'],
                                    'ormawa'  => ['label' => 'Ormawa',  'icon' => 'fa-people-group', 'classes' => 'bg-blue-100 text-blue-700'],
                                    'dosen'   => ['label' => 'Dosen',   'icon' => 'fa-chalkboard-teacher', 'classes' => 'bg-purple-100 text-purple-700'],
                                ];
                                $badge = $roleMap[$user->role] ?? ['label' => ucfirst($user->role), 'icon' => 'fa-user', 'classes' => 'bg-slate-100 text-slate-600'];
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition">
                                {{-- Hidden form for update (shared by update + delete trigger) --}}
                                <form id="update-user-{{ $user->id }}" action="{{ route('akun.update', $user) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('PATCH')
                                </form>
                                {{-- Hidden form for delete --}}
                                <form id="delete-form-{{ $user->id }}" action="{{ route('akun.destroy', $user) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                <td class="px-6 py-4">
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ $user->name }}"
                                        form="update-user-{{ $user->id }}"
                                        required
                                        class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10"
                                    >
                                </td>
                                <td class="px-6 py-4">
                                    <input
                                        type="email"
    name="email"
    value="{{ $user->email }}"
    form="update-user-{{ $user->id }}"
    required
                                        class="h-10 w-full min-w-[210px] rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-600 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10"
                                    >
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md {{ $badge['classes'] }} text-xs font-black">
                                        <i class="fas {{ $badge['icon'] }} text-[10px]"></i> {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded border border-slate-200" title="Password dienkripsi (Hash)">
                                        <i class="fas fa-lock text-[10px]"></i> Terlindungi
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex gap-2">
                                        <button
                                            type="submit"
                                            form="update-user-{{ $user->id }}"
                                            class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 text-xs font-black uppercase tracking-wide text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700"
                                        >
                                            <i class="fas fa-save"></i>
                                            Perbarui
                                        </button>
                                        <button
                                            type="button"
                                            class="btn-delete inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-red-600 px-4 text-xs font-black uppercase tracking-wide text-white shadow-lg shadow-red-600/20 transition hover:bg-red-700"
                                            data-name="{{ $user->name }}"
                                            data-form="delete-form-{{ $user->id }}"
                                            title="Hapus Akun"
                                        >
                                            <i class="fas fa-trash-alt"></i>
                                            Hapus
                                        </button>
                                    </div>
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

        {{-- ===== Mobile: Cards ===== --}}
        <div class="md:hidden rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-xl shadow-slate-200/40">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">
                    <i class="fas fa-users text-slate-400 mr-2"></i> Daftar Pengguna
                </h3>
                <span class="text-xs font-bold text-slate-400">{{ $users->count() }} akun</span>
            </div>

            @forelse($users as $user)
                @php
                    $roleMap = [
                        'asisten' => ['label' => 'Asisten', 'icon' => 'fa-microscope', 'classes' => 'bg-emerald-100 text-emerald-700'],
                        'ormawa'  => ['label' => 'Ormawa',  'icon' => 'fa-people-group', 'classes' => 'bg-blue-100 text-blue-700'],
                        'dosen'   => ['label' => 'Dosen',   'icon' => 'fa-chalkboard-teacher', 'classes' => 'bg-purple-100 text-purple-700'],
                    ];
                    $badge = $roleMap[$user->role] ?? ['label' => ucfirst($user->role), 'icon' => 'fa-user', 'classes' => 'bg-slate-100 text-slate-600'];
                @endphp
                <div class="p-4 border-b border-slate-100 last:border-b-0">
                    <form id="update-user-m-{{ $user->id }}" action="{{ route('akun.update', $user) }}" method="POST" class="hidden">
                        @csrf
                        @method('PATCH')
                    </form>

                    {{-- Top row: badges --}}
                    <div class="mb-3 flex items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md {{ $badge['classes'] }} text-xs font-black">
                            <i class="fas {{ $badge['icon'] }} text-[10px]"></i> {{ $badge['label'] }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded border border-slate-200">
                            <i class="fas fa-lock text-[10px]"></i> Terlindungi
                        </span>
                    </div>

                    {{-- Name --}}
                    <div class="mb-3">
                        <label for="name-m-{{ $user->id }}" class="mb-1 block text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                            Nama / Organisasi
                        </label>
                        <input
                            type="text"
                            id="name-m-{{ $user->id }}"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            form="update-user-m-{{ $user->id }}"
                            required
                            class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10"
                        >
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label for="email-m-{{ $user->id }}" class="mb-1 block text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                            Email Login
                        </label>
                        <input
                            type="email"
                            id="email-m-{{ $user->id }}"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            form="update-user-m-{{ $user->id }}"
                            required
                            class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-600 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10"
                        >
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2">
                        <button
                            type="submit"
                            form="update-user-m-{{ $user->id }}"
                            class="flex-1 inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 text-xs font-black uppercase tracking-wide text-white shadow-lg shadow-blue-600/20 transition active:scale-[0.98]"
                        >
                            <i class="fas fa-save"></i>
                            Perbarui
                        </button>
                        <button
                            type="button"
                            class="btn-delete flex-shrink-0 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-red-600 text-white shadow-lg shadow-red-600/20 transition active:scale-[0.98]"
                            data-name="{{ $user->name }}"
                            data-form="delete-form-{{ $user->id }}"
                            title="Hapus Akun"
                        >
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-slate-400 font-medium">
                    <i class="fas fa-folder-open text-3xl mb-3 text-slate-200 block"></i>
                    Belum ada akun yang didaftarkan.
                </div>
            @endforelse
        </div>

    </div>
</div>

{{-- ===== Modal Konfirmasi Hapus ===== --}}
<div id="confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
            <i class="fas fa-exclamation-triangle text-xl"></i>
        </div>
        <h3 class="text-lg font-black text-slate-900">Hapus akun ini?</h3>
        <p class="mt-2 text-sm font-medium text-slate-500 leading-relaxed">
            Yakin ingin menghapus akun <span id="confirm-name" class="font-extrabold text-slate-700"></span>?
            Semua jadwal dan riwayat yang terkait dengan akun ini akan ikut terhapus secara permanen.
        </p>
        <div class="mt-6 flex gap-3">
            <button type="button" id="confirm-cancel" class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                Batal
            </button>
            <button type="button" id="confirm-delete-btn" class="flex-1 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-black text-white shadow-lg shadow-red-600/20 transition hover:bg-red-700">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

<script>
    // ===== Submit loading state untuk form buat akun =====
    const createForm = document.getElementById('create-account-form');
    if (createForm) {
        createForm.addEventListener('submit', function() {
            const btn = document.getElementById('btn-submit');
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Menyimpan...';
            btn.classList.add('opacity-70', 'pointer-events-none');
        });
    }

    // ===== Generate password acak =====
    function generatePassword() {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$&";
        let password = "";
        for (let i = 0; i < 10; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }

        const passInput = document.getElementById('password-input');
        passInput.value = password;

        passInput.classList.add('bg-emerald-50', 'border-emerald-400');
        setTimeout(() => {
            passInput.classList.remove('bg-emerald-50', 'border-emerald-400');
        }, 500);
    }

    // Auto-generate password saat halaman pertama dibuka (kalau belum ada nilai)
    document.addEventListener('DOMContentLoaded', () => {
        const passInput = document.getElementById('password-input');
        if (passInput && !passInput.value.trim()) {
            generatePassword();
        }
    });

    // ===== Copy password ke clipboard =====
    function copyPassword() {
        const passInput = document.getElementById('password-input');
        if (!passInput.value) return;

        navigator.clipboard.writeText(passInput.value).then(() => {
            const icon = document.querySelector('#copy-btn i');
            const original = icon.className;
            icon.className = 'fas fa-check text-emerald-500';
            setTimeout(() => { icon.className = original; }, 1200);
        }).catch(() => {});
    }

    // ===== Modal konfirmasi hapus =====
    const confirmModal = document.getElementById('confirm-modal');
    const confirmName = document.getElementById('confirm-name');
    let pendingDeleteForm = null;

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            pendingDeleteForm = document.getElementById(btn.dataset.form);
            confirmName.textContent = btn.dataset.name;
            confirmModal.classList.remove('hidden');
            confirmModal.classList.add('flex');
        });
    });

    function closeConfirmModal() {
        confirmModal.classList.add('hidden');
        confirmModal.classList.remove('flex');
        pendingDeleteForm = null;
    }

    document.getElementById('confirm-cancel').addEventListener('click', closeConfirmModal);

    confirmModal.addEventListener('click', (e) => {
        if (e.target === confirmModal) closeConfirmModal();
    });

    document.getElementById('confirm-delete-btn').addEventListener('click', () => {
        if (pendingDeleteForm) pendingDeleteForm.submit();
    });
</script>
@endsection