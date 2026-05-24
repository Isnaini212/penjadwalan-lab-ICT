<x-layouts.public title="Jadwal Laboratorium">
    <div class="min-h-screen">
        <header class="border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
                <div>

                    <h1 class="text-lg font-bold text-blue-900">
                        Penjadwalan Lab ICT
                    </h1>
                    <p class="text-sm text-slate-500">
                        Informasi jadwal laboratorium
                    </p>
                </div>

                <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-blue-700 text-white text-sm font-medium hover:bg-blue-900">
                    Login
                </a>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 py-8 bg-gradient-to-t from-blue-200 to-slate-100">
            <section class="mb-6">
                <div class="text-center mb-6">
                    <h2 class="text-3xl font-bold text-blue-900">
                        Jadwal Laboratorium
                    </h2>

                    <p class="mt-2 text-slate-600">
                        Menampilkan jadwal mata kuliah dan asisten lab yang sedang tersedia.
                    </p>
                </div>

                <div class="flex items-center justify-between mb-4 text-sm">

                    <div class="relative">
                        <select class=" px-5 py-3 pr-10 appearance-none flex justify-between items-center text-left rounded-lg border border-slate-300 bg-white text-slate-700 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-slate-400">
                        <option value="" disabled selected hidden>Pilih Hari</option>
                        <option value="senin">Senin</option>
                        <option value="selasa">Selasa</option>
                        <option value="rabu">Rabu</option>
                        <option value="kamis">Kamis</option>
                        <option value="jumat">Jumat</option>
                        <option value="sabtu">Sabtu</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="size-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>

                    <button
                        onclick="window.print()"
                        class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium hover:bg-slate-50"
                    >
                        Print Jadwal
                    </button>

                </div>

            </section>

            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-200">
                    <h3 class="font-semibold text-blue-900">
                        Jadwal Mata Kuliah
                    </h3>
                    <p class="text-sm text-slate-500">
                        Daftar jadwal mata kuliah yang sedang berlangsung.
                    </p>
                </div>

                <div class="max-h-[520px] overflow-auto">
                    <table class="min-w-[1100px] w-full text-sm">
                        <thead class="sticky top-0 z-10 bg-slate-100 text-slate-700">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold">Hari</th>
                                <th class="px-5 py-3 text-left font-semibold">Jam</th>
                                <th class="px-5 py-3 text-left font-semibold">Kelompok</th>
                                <th class="px-5 py-3 text-left font-semibold">Mata Kuliah</th>
                                <th class="px-5 py-3 text-left font-semibold">Dosen</th>
                                <th class="px-5 py-3 text-left font-semibold">Asisten</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200">
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">Senin</td>
                                <td class="px-4 py-3">08:00 - 10:30</td>
                                <td class="px-4 py-3">Basis Data</td>
                                <td class="px-4 py-3 font-medium text-slate-900">Lab 2</td>
                                <td class="px-4 py-3">Pak Budi</td>
                                <td class="px-4 py-3">Fitra</td>
                            </tr>

                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">Senin</td>
                                <td class="px-4 py-3">10:00 - 12:00</td>
                                <td class="px-4 py-3">Pemrograman Web</td>
                                <td class="px-4 py-3 font-medium text-slate-900">Lab 3</td>
                                <td class="px-4 py-3">Bu Sari</td>
                                <td class="px-4 py-3">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <footer class="py-6 text-center text-sm text-slate-500">
            Sistem Penjadwalan Laboratorium ICT
        </footer>
    </div>
</x-layouts.public>
