<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Jadwal Mingguan - Lab ICT</title>
    <link rel="icon" type="image/LogoICT.png" href="{{ asset('images/LogoICT.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-blue-100 font-sans text-slate-800 flex flex-col">

    <nav class="sticky top-0 z-40 border-b border-white/70 bg-white/80 backdrop-blur py-4 shadow-sm">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6">
            <div class="flex items-center gap-2">
                <div class="text-lg font-black tracking-tight text-blue-900 sm:text-xl">
                    LabSystem <span class="text-slate-400 font-medium">| Export PDF</span>
                </div>
            </div>
            <a href="/" class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200 border border-slate-200">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
            </a>
        </div>
    </nav>

    <main class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-md rounded-3xl border border-white bg-white/80 p-8 shadow-2xl shadow-blue-950/10 backdrop-blur-xl">

            <div class="text-center mb-8">
                <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-white text-2xl shadow-lg shadow-blue-600/30 mb-4">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Export Berkas Jadwal</h1>
                <p class="text-sm font-medium text-slate-500 mt-1">Silakan pilih minggu perkuliahan yang ingin dicetak langsung dari database.</p>
            </div>


            <form action="{{ route('cetak') }}" method="GET" target="_blank" class="space-y-6">
                <div>
                    <label for="dropdown-minggu" class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-400">Minggu Perkuliahan</label>
                    <div class="relative">
                        <select name="week" id="dropdown-minggu" class="w-full h-12 rounded-xl border border-slate-200 bg-white px-4 pl-11 text-sm font-bold text-slate-800 outline-none shadow-sm transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 appearance-none cursor-pointer">
                            @foreach($listMinggu ?? [] as $m)
                                <option value="{{ $m['id_minggu'] }}" {{ $defaultWeek == $m['id_minggu'] ? 'selected' : '' }}>
                                    {{ $m['label'] }} ({{ \Carbon\Carbon::parse($m['start'])->format('d M') }} - {{ \Carbon\Carbon::parse($m['end'])->format('d M') }})
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-calendar-week absolute left-4 top-4 text-slate-400 text-sm"></i>
                        <i class="fas fa-chevron-down absolute right-4 top-4 text-slate-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

                <button type="submit" class="w-full h-12 inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 text-sm font-black uppercase tracking-wider text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 hover:-translate-y-0.5 active:translate-y-0">
                    <i class="fas fa-print"></i> Unduh Jadwal Mingguan
                </button>
            </form>

        </div>
    </main>

</body>
</html>
