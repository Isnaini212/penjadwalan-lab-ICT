@extends('layouts.spv')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <section>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Dashboard</h1>
            <p class="mt-1.5 text-sm font-medium text-slate-500">Memantau indikator kinerja utama Anda</p>
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem] 2xl:grid-cols-[minmax(0,1fr)_22rem]">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-md shadow-slate-900/5">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Jadwal Hari Ini</h2>
                        <p class="mt-1 text-sm text-slate-500">Cek Jadwal Tanggal:</p>
                    </div>
                    <label class="block sm:w-56">
                        <input type="date" value="{{ request('filter_date', now()->toDateString()) }}" class="h-10 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-700 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                    </label>
                </div>

                <a href="#" class="text-xs font-bold text-red-500 underline">[Reset Ke Hari Ini]</a>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-[760px] w-full text-left text-xs">
                        <thead class="bg-slate-50 text-xs font-extrabold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-3 py-3 sticky top-0 z-10 bg-slate-50 text-xs font-extrabold uppercase tracking-wide text-slate-500">Tanggal</th>
                                <th class="px-3 py-3 sticky top-0 z-10 bg-slate-50 text-xs font-extrabold uppercase tracking-wide text-slate-500">Lab</th>
                                <th class="px-3 py-3 sticky top-0 z-10 bg-slate-50 text-xs font-extrabold uppercase tracking-wide text-slate-500">Jam (Mulai - Selesai)</th>
                                <th class="px-3 py-3 sticky top-0 z-10 bg-slate-50 text-xs font-extrabold uppercase tracking-wide text-slate-500">Mata Kuliah</th>
                                <th class="px-3 py-3 sticky top-0 z-10 bg-slate-50 text-xs font-extrabold uppercase tracking-wide text-slate-500">Dosen</th>
                                <th class="px-3 py-3 sticky top-0 z-10 bg-slate-50 text-xs font-extrabold uppercase tracking-wide text-slate-500">Asisten</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-3">
                                    <span class="block text-xs font-extrabold uppercase text-sky-700">Kamis</span>
                                    <span class="text-slate-600">28 May 2026</span>
                                </td>
                                <td class="px-3 py-3 font-bold text-slate-700">LAB 02</td>
                                <td class="px-3 py-3 font-mono text-slate-700">08:00:00 - 10:35:00</td>
                                <td class="px-3 py-3 font-semibold text-slate-700">Analisis Teks Pada Media Sosial (AA)</td>
                                <td class="px-3 py-3 text-slate-600">Safitri Juanita, S.Kom., M.T.I.</td>
                                <td class="px-3 py-3 text-slate-600">Bima Rasta Guevara</td>
                            </tr>
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-3">
                                    <span class="block text-xs font-extrabold uppercase text-sky-700">Kamis</span>
                                    <span class="text-slate-600">28 May 2026</span>
                                </td>
                                <td class="px-3 py-3 font-bold text-slate-700">LAB 04</td>
                                <td class="px-3 py-3 font-mono text-slate-700">08:00:00 - 10:35:00</td>
                                <td class="px-3 py-3 font-semibold text-slate-700">Pemrograman Berorientasi Obyek (AJ)</td>
                                <td class="px-3 py-3 text-slate-600">Lis Suryadi, S.Kom., M.Kom.</td>
                                <td class="px-3 py-3 text-slate-600">-</td>
                            </tr>
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-3">
                                    <span class="block text-xs font-extrabold uppercase text-sky-700">Kamis</span>
                                    <span class="text-slate-600">28 May 2026</span>
                                </td>
                                <td class="px-3 py-3 font-bold text-slate-700">LAB 06</td>
                                <td class="px-3 py-3 font-mono text-slate-700">08:00:00 - 09:40:00</td>
                                <td class="px-3 py-3 font-semibold text-slate-700">Pemrograman Permainan (AC)</td>
                                <td class="px-3 py-3 text-slate-600">Putri Hayati, S.ST., M.Kom.</td>
                                <td class="px-3 py-3 text-slate-600">-</td>
                            </tr>
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-3">
                                    <span class="block text-xs font-extrabold uppercase text-sky-700">Kamis</span>
                                    <span class="text-slate-600">28 May 2026</span>
                                </td>
                                <td class="px-3 py-3 font-bold text-slate-700">LAB 05</td>
                                <td class="px-3 py-3 font-mono text-slate-700">08:00:00 - 10:35:00</td>
                                <td class="px-3 py-3 font-semibold text-slate-700">Pemrograman Web (AE)</td>
                                <td class="px-3 py-3 text-slate-600">Yuliazmi, S.Kom., M.Kom.</td>
                                <td class="px-3 py-3 text-slate-600">-</td>
                            </tr>
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-3">
                                    <span class="block text-xs font-extrabold uppercase text-sky-700">Kamis</span>
                                    <span class="text-slate-600">28 May 2026</span>
                                </td>
                                <td class="px-3 py-3 font-bold text-slate-700">LAB 02</td>
                                <td class="px-3 py-3 font-mono text-slate-700">10:40:00 - 12:25:00</td>
                                <td class="px-3 py-3 font-semibold text-slate-700">Administrasi Linux (AA)</td>
                                <td class="px-3 py-3 text-slate-600">Putri Hayati, S.ST., M.Kom.</td>
                                <td class="px-3 py-3 text-slate-600">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-1">

                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-md shadow-slate-900/5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Total Matkul Hari Ini</p>
                            <p class="mt-2 text-3xl font-black text-slate-900">14</p>
                        </div>
                        <i class="fa-solid fa-building text-2xl text-sky-700/80"></i>
                    </div>
                </article>
                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-md shadow-slate-900/5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Jumlah Laboratorium</p>
                            <p class="mt-2 text-3xl font-black text-slate-900">12</p>
                        </div>
                        <i class="fa-solid fa-desktop text-2xl text-sky-700/80"></i>
                    </div>
                </article>
                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-md shadow-slate-900/5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Asisten Bertugas</p>
                            <p class="mt-2 text-3xl font-black text-slate-900">1</p>
                        </div>
                        <i class="fa-solid fa-users text-2xl text-sky-700/80"></i>
                    </div>
                </article>
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-md shadow-slate-900/5">
            <div class="mb-5 flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                    <i class="fa-solid fa-user-group text-xl"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Status Petugas Asisten</h2>
                    <p class="text-sm text-slate-500">Daftar asisten yang menjaga laboratorium saat ini</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[760px] w-full text-left text-xs">
                    <thead class="bg-slate-50 text-xs font-extrabold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-4">Nama Asisten</th>
                            <th class="px-4 py-4">Menjaga Lab</th>
                            <th class="px-4 py-4">Waktu Tugas</th>
                            <th class="px-4 py-4">Mata Kuliah Kelolaan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-sky-700 text-xs font-extrabold text-white">B</span>
                                    <span class="font-semibold text-slate-700">Bima Rasta Guevara</span>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="rounded-md bg-sky-100 px-3 py-1 text-xs font-extrabold text-sky-700">LAB 02</span></td>
                            <td class="px-4 py-4 font-mono text-slate-700">08:00:00 - 10:35:00</td>
                            <td class="px-4 py-4 italic text-slate-600">Analisis Teks Pada Media Sosial (AA)</td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-sky-700 text-xs font-extrabold text-white">B</span>
                                    <span class="font-semibold text-slate-700">Bima Rasta Guevara</span>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="rounded-md bg-sky-100 px-3 py-1 text-xs font-extrabold text-sky-700">LAB 02</span></td>
                            <td class="px-4 py-4 font-mono text-slate-700">13:25:00 - 15:10:00</td>
                            <td class="px-4 py-4 italic text-slate-600">Pemrograman Permainan (AA)</td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-sky-700 text-xs font-extrabold text-white">B</span>
                                    <span class="font-semibold text-slate-700">Bima Rasta Guevara</span>
                                </div>
                            </td>
                            <td class="px-4 py-4"><span class="rounded-md bg-sky-100 px-3 py-1 text-xs font-extrabold text-sky-700">RUANG RA</span></td>
                            <td class="px-4 py-4 font-mono text-slate-700">14:20:00 - 15:10:00</td>
                            <td class="px-4 py-4 italic text-slate-600">RA</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
