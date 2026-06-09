<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Jadwal Kuliah Asisten</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
    @php
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $initialSchedule = collect($jadwalTersimpan ?? [])
            ->groupBy('hari')
            ->map(fn ($items) => $items->map(fn ($item) => [
                'id' => $item->id_asisten,
                'mata_kuliah' => $item->mata_kuliah,
                'jam_mulai' => substr($item->jam_mulai, 0, 5),
                'jam_selesai' => substr($item->jam_selesai, 0, 5),
                'sks' => 1,
            ])->values())
            ->toArray();
    @endphp

    <main
        class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8"
        x-data="assistantScheduleForm(@js($days), @js($initialSchedule))"
        x-init="init()"
    >
        <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full border-4 border-blue-500 bg-blue-50 text-xl font-black text-blue-600">
                    {{ collect(explode(' ', $namaAsisten ?? auth()->user()->name))->map(fn($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('') }}
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-950">{{ $namaAsisten ?? auth()->user()->name }}</h1>
                    <p class="mt-1 text-sm font-medium text-slate-500">Input jadwal kuliah pribadi untuk validasi bentrok asisten.</p>
                </div>
            </div>

            <div class="inline-flex w-max items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-500 shadow-sm">
                <span class="text-amber-500">Lock</span>
                <span>Data asisten</span>
            </div>
        </header>

        @if(session('success'))
            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div x-show="hasConflict" x-cloak class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">
            Ada jadwal yang bentrok. Perbaiki dahulu sebelum menyimpan.
        </div>

        <form method="POST" action="{{ route('asisten.jadwal.store') }}" @submit="prepareSubmit">
            @csrf

            <template x-for="day in days" :key="day">
                <section class="mb-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/70" :class="{ 'border-red-200': dayHasConflict(day) }">
                    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="h-3 w-3 rounded-full" :class="schedules[day].length ? 'bg-blue-500' : 'bg-slate-200'"></span>
                            <h2 class="text-lg font-black text-slate-950" x-text="day"></h2>
                        </div>
                        <span
                            x-show="schedules[day].length"
                            class="rounded-full border border-blue-200 bg-blue-50 px-4 py-1.5 text-sm font-bold text-blue-600"
                            x-text="`${schedules[day].length} matkul`"
                        ></span>
                    </div>

                    <div class="px-6 py-5">
                        <template x-if="!schedules[day].length">
                            <div class="flex items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm font-medium text-slate-400">
                                Belum ada matkul. Klik tombol di bawah untuk menambah.
                            </div>
                        </template>

                        <template x-if="schedules[day].length">
                            <div>
                                <div class="mb-3 hidden grid-cols-[1.5fr_1fr_1fr_0.7fr_3rem] gap-3 px-1 text-xs font-bold uppercase tracking-wide text-slate-400 md:grid">
                                    <span>Nama Mata Kuliah</span>
                                    <span>Jam Mulai</span>
                                    <span>Jam Selesai</span>
                                    <span>SKS</span>
                                    <span></span>
                                </div>

                                <div class="space-y-3">
                                    <template x-for="(row, index) in schedules[day]" :key="row.key">
                                        <div class="grid gap-3 md:grid-cols-[1.5fr_1fr_1fr_0.7fr_3rem]">
                                            <input
                                                type="text"
                                                :name="`jadwal[${day}][${index}][mata_kuliah]`"
                                                x-model="row.mata_kuliah"
                                                placeholder="cth: Basis Data"
                                                class="h-12 rounded-xl border bg-white px-4 text-sm font-semibold outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                                :class="row.conflict ? 'border-red-300 bg-red-50' : 'border-slate-200'"
                                                @input="checkConflicts"
                                                required
                                            >

                                            <input
                                                type="time"
                                                :name="`jadwal[${day}][${index}][jam_mulai]`"
                                                x-model="row.jam_mulai"
                                                class="h-12 rounded-xl border bg-white px-4 text-sm font-semibold outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                                :class="row.conflict ? 'border-red-300 bg-red-50' : 'border-slate-200'"
                                                @input="recalculate(row); checkConflicts()"
                                                required
                                            >

                                            <input
                                                type="text"
                                                :name="`jadwal[${day}][${index}][jam_selesai]`"
                                                x-model="row.jam_selesai"
                                                class="h-12 rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm font-bold text-slate-500 outline-none"
                                                readonly
                                            >

                                            <select
                                                :name="`jadwal[${day}][${index}][sks]`"
                                                x-model.number="row.sks"
                                                class="h-12 rounded-xl border bg-white px-4 text-sm font-bold outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                                :class="row.conflict ? 'border-red-300 bg-red-50' : 'border-slate-200'"
                                                @change="recalculate(row); checkConflicts()"
                                                required
                                            >
                                                <option value="1">1 SKS</option>
                                                <option value="2">2 SKS</option>
                                                <option value="3">3 SKS</option>
                                                <option value="4">4 SKS</option>
                                            </select>

                                            <button
                                                type="button"
                                                class="flex h-12 items-center justify-center rounded-xl border border-slate-200 text-slate-400 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                                                @click="removeRow(day, row.key)"
                                                title="Hapus baris"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div x-show="dayHasConflict(day)" x-cloak class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                            Ada jadwal yang waktunya bertabrakan di hari ini.
                        </div>

                        <button
                            type="button"
                            class="mt-5 flex w-full items-center justify-center rounded-2xl border border-dashed border-blue-300 px-4 py-3 text-sm font-bold text-blue-600 transition hover:bg-blue-50"
                            @click="addRow(day)"
                        >
                            + Tambah matkul di hari <span x-text="day" class="ml-1"></span>
                        </button>
                    </div>
                </section>
            </template>

            <footer class="sticky bottom-4 mt-8 flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white/95 px-6 py-5 shadow-2xl shadow-slate-300/60 backdrop-blur sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">
                    Total:
                    <strong class="text-slate-950" x-text="totalRows"></strong>
                    mata kuliah di
                    <strong class="text-slate-950" x-text="totalDays"></strong>
                    hari
                </p>

                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-bold text-slate-500 transition hover:bg-slate-50"
                        @click="resetAll"
                    >
                        Reset
                    </button>
                    <button
                        type="submit"
                        class="rounded-xl bg-blue-600 px-7 py-3 text-sm font-black text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="hasConflict || totalRows === 0"
                    >
                        Simpan jadwal
                    </button>
                </div>
            </footer>
        </form>
    </main>

    <script>
        function assistantScheduleForm(days, initialSchedule) {
            return {
                days,
                schedules: {},
                hasConflict: false,

                init() {
                    this.days.forEach(day => {
                        this.schedules[day] = (initialSchedule[day] || []).map(row => ({
                            key: this.makeKey(),
                            mata_kuliah: row.mata_kuliah || '',
                            jam_mulai: row.jam_mulai || '',
                            jam_selesai: row.jam_selesai || '',
                            sks: Number(row.sks || 1),
                            conflict: false,
                        }));
                    });

                    this.checkConflicts();
                },

                get totalRows() {
                    return this.days.reduce((total, day) => total + this.schedules[day].length, 0);
                },

                get totalDays() {
                    return this.days.filter(day => this.schedules[day].length > 0).length;
                },

                addRow(day) {
                    const row = {
                        key: this.makeKey(),
                        mata_kuliah: '',
                        jam_mulai: '',
                        jam_selesai: '',
                        sks: 2,
                        conflict: false,
                    };

                    this.schedules[day].push(row);
                },

                removeRow(day, key) {
                    this.schedules[day] = this.schedules[day].filter(row => row.key !== key);
                    this.checkConflicts();
                },

                resetAll() {
                    this.days.forEach(day => this.schedules[day] = []);
                    this.hasConflict = false;
                },

                recalculate(row) {
                    if (!row.jam_mulai || !row.sks) {
                        row.jam_selesai = '';
                        return;
                    }

                    const minutes = (Number(row.sks) * 50) + ((Number(row.sks) - 1) * 5);
                    row.jam_selesai = this.addMinutes(row.jam_mulai, minutes);
                },

                checkConflicts() {
                    this.hasConflict = false;

                    this.days.forEach(day => {
                        this.schedules[day].forEach(row => row.conflict = false);

                        for (let i = 0; i < this.schedules[day].length; i++) {
                            for (let j = i + 1; j < this.schedules[day].length; j++) {
                                const a = this.schedules[day][i];
                                const b = this.schedules[day][j];

                                if (!a.jam_mulai || !a.jam_selesai || !b.jam_mulai || !b.jam_selesai) {
                                    continue;
                                }

                                if (this.toMinutes(a.jam_mulai) < this.toMinutes(b.jam_selesai) && this.toMinutes(a.jam_selesai) > this.toMinutes(b.jam_mulai)) {
                                    a.conflict = true;
                                    b.conflict = true;
                                    this.hasConflict = true;
                                }
                            }
                        }
                    });
                },

                dayHasConflict(day) {
                    return this.schedules[day].some(row => row.conflict);
                },

                prepareSubmit(event) {
                    this.checkConflicts();

                    if (this.hasConflict || this.totalRows === 0) {
                        event.preventDefault();
                    }
                },

                addMinutes(time, minutes) {
                    const total = this.toMinutes(time) + minutes;
                    const hour = Math.floor(total / 60) % 24;
                    const minute = total % 60;

                    return `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
                },

                toMinutes(time) {
                    const [hour, minute] = time.split(':').map(Number);
                    return (hour * 60) + minute;
                },

                makeKey() {
                    return Math.random().toString(36).slice(2);
                },
            };
        }
    </script>
</body>
</html>
