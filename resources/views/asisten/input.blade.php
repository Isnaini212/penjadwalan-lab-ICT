<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Input Jadwal Kuliah Asisten</title>
    <link rel="icon" type="image/LogoICT.png" href="{{ asset('images/LogoICT.png') }}">
    
    {{--  MENGGUNAKAN TAILWIND & FONTAWESOME SEPERTI LAYOUT UTAMA --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-blue-100 font-sans text-slate-800 antialiased p-4 md:p-8">

<div class="max-w-4xl margin-0 auto mx-auto space-y-6">

    <div class="flex items-center gap-3 sm:gap-4 bg-white/80 backdrop-blur p-4 sm:p-5 rounded-2xl border border-white shadow-xl shadow-blue-950/5">
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-100 border-2 border-blue-600 flex items-center justify-center text-base sm:text-lg font-black text-blue-600 flex-shrink-0">
            {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->nama ?? 'AS', 0, 2)) }}
        </div>
        
        <div class="flex-1 min-w-0">
            <h1 id="nama-asisten-heading" class="text-sm font-extrabold text-slate-900 tracking-tight sm:text-lg truncate">
                {{ auth()->user()->name ?? auth()->user()->nama ?? '' }}
            </h1>
            <p class="text-[10px] sm:text-xs font-semibold text-slate-500 mt-0.5 truncate">Semester aktif 2025/2026</p>
        </div>
        
        <div class="ml-auto flex items-center gap-1.5 sm:gap-2">
            
            <a href="{{ route('asisten.cetak_matriks') }}" target="_blank" class="flex items-center justify-center gap-2 px-3 py-2 sm:py-1.5 text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-full hover:bg-emerald-100 hover:text-emerald-700 transition shadow-sm" title="Cetak Matriks Jaga Lab">
                <i class="fas fa-print"></i> 
                <span class="hidden sm:inline">Cetak Jadwal</span>
            </a>

            <a href="{{ url('/profile') }}" class="flex items-center justify-center gap-2 px-3 py-2 sm:py-1.5 text-xs font-bold bg-white text-slate-600 border border-slate-200 rounded-full hover:bg-slate-50 hover:text-blue-600 transition shadow-sm" title="Edit Profil">
                <i class="fas fa-user-edit"></i> 
                <span class="hidden sm:inline">Profil</span>
            </a>
            
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="flex items-center justify-center gap-2 px-3 py-2 sm:py-1.5 text-xs font-bold bg-red-50 text-red-600 border border-red-100 rounded-full hover:bg-red-100 hover:text-red-700 transition shadow-sm" title="Keluar Sistem">
                    <i class="fas fa-sign-out-alt"></i> 
                    <span class="hidden sm:inline">Logout</span>
                </button>
            </form>

        </div>
    </div>

    <div class="hidden items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-700 text-sm font-bold rounded-2xl shadow-sm shadow-red-950/5" id="main-banner">
        <i class="fas fa-exclamation-triangle text-red-500 text-base animate-pulse"></i>
        <span>Ada jadwal yang bentrok. Perbaiki sebelum menyimpan.</span>
    </div>

    <div id="days-container" class="space-y-4"></div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5 flex flex-wrap items-center justify-between gap-4 shadow-xl shadow-blue-950/5">
    <div class="text-sm font-semibold text-slate-500">
        Total: <span id="total-matkul" class="text-slate-900 font-black">0</span> matkul di <span id="total-hari" class="text-slate-900 font-black">0</span> hari
    </div>
    
    <div class="flex items-center gap-3 w-full sm:w-auto">
        @if(cache('lock_asisten_schedule', false))
            {{-- Notif jika jadwal sudah dikunci oleh SPV --}}
            <span class="text-xs font-bold text-red-600 bg-red-50 border border-red-100 px-3 py-2 rounded-xl">
                <i class="fas fa-lock mr-1"></i> Jadwal telah di-finalisasi oleh SPV. Anda hanya dapat melihat & mengunduh PDF.
            </span>
        @else
            <button class="flex-1 sm:flex-none px-4 py-2 text-sm font-bold border border-slate-200 text-slate-600 bg-white rounded-xl hover:bg-slate-50 transition" onclick="resetAll()">
                Reset
            </button>
            <button class="flex-1 sm:flex-none px-5 py-2 text-sm font-black text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-600/20 disabled:opacity-40 disabled:pointer-events-none transition flex items-center justify-center gap-2" id="btn-submit" onclick="handleSubmit()" disabled>
                <i class="fas fa-save"></i> Simpan Jadwal
            </button>
        @endif
    </div>
</div>

    <div class="{{ isset($savedSchedulesFlat) && $savedSchedulesFlat->count() > 0 ? '' : 'hidden' }} bg-white border border-slate-200 rounded-2xl p-5 shadow-xl shadow-blue-950/5" id="result-preview">
        <h2 class="text-sm font-black text-slate-800 uppercase tracking-wide flex items-center gap-2 mb-4">
            <i class="fas fa-calendar-check text-emerald-500 text-base"></i> Jadwal Kuliah Saya yang Terdaftar
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <th class="pb-3 pt-1 px-3">Hari</th>
                        <th class="pb-3 pt-1 px-3">Mata Kuliah</th>
                        <th class="pb-3 pt-1 px-3">Waktu WIB</th>
                    </tr>
                </thead>
                <tbody id="result-tbody" class="divide-y divide-slate-50 font-medium text-slate-700">
                    @if(isset($savedSchedulesFlat))
                        @foreach($savedSchedulesFlat as $sch)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3 px-3">
                                    <span class="inline-block text-xs font-black px-2.5 py-0.5 bg-blue-100 text-blue-700 rounded-full">
                                        {{ $sch->hari }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 font-bold text-slate-800">{{ $sch->mata_kuliah }}</td>
                                <td class="py-3 px-3 font-mono text-xs tracking-wider font-bold text-indigo-600">
                                    {{ date('H:i', strtotime($sch->jam_mulai)) }} – {{ date('H:i', strtotime($sch->jam_selesai)) }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="fixed bottom-6 right-6 px-4 py-3 rounded-xl shadow-2xl font-bold text-sm text-white transform translate-y-8 opacity-0 pointer-events-none transition-all duration-300 z-50 flex items-center gap-2" id="toast"></div>

<script>
  const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
  const SKS_OPTS = ['1 SKS', '2 SKS', '3 SKS', '4 SKS'];

  let state = {
    'Senin': [], 'Selasa': [], 'Rabu': [], 'Kamis': [], 'Jumat': []
  };

  @if(isset($existingSchedules))
    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $d)
      @if(isset($existingSchedules[$d]))
        @foreach($existingSchedules[$d] as $sch)
          @php
              $fullName = $sch->mata_kuliah;
              $sksText = '2 SKS';
              if (preg_match('/(.*)\s\((\d+)\sSKS\)/i', $fullName, $matches)) {
                  $matkulName = trim($matches[1]);
                  $sksText = $matches[2] . ' SKS';
              } else {
                  $matkulName = $fullName;
              }
          @endphp
          state['{{ $d }}'].push({
            id: '_' + Math.random().toString(36).slice(2, 9),
            name: '{!! addslashes($matkulName) !!}',
            start: '{{ substr($sch->jam_mulai, 0, 5) }}',
            end: '{{ substr($sch->jam_selesai, 0, 5) }}',
            sks: '{{ $sksText }}'
          });
        @endforeach
      @endif
    @endforeach
  @endif

  function makeId() { return '_' + Math.random().toString(36).slice(2, 9); }

  function toMin(t) {
    if (!t) return -1;
    const [h, m] = t.split(':').map(Number);
    return h * 60 + m;
  }

  function hitungJamSelesai(jamMulai, sksText) {
    if (!jamMulai || jamMulai.length < 5) return '';
    const sks = parseInt(sksText) || 2;

    const weekdayStarts = ['07:10', '08:00', '08:55', '09:45', '10:40', '11:35', '12:30', '13:25', '14:20', '15:15', '16:10', '17:05', '18:00', '18:55', '19:50'];
    const weekdayEnds =   ['08:00', '08:50', '09:40', '10:35', '11:30', '12:25', '13:20', '14:15', '15:10', '16:05', '17:00', '17:55', '18:50', '19:45', '20:40'];

    const startIndex = weekdayStarts.indexOf(jamMulai);
    if (startIndex !== -1) {
        const endIndex = startIndex + sks - 1;
        if (endIndex < weekdayEnds.length) {
            return weekdayEnds[endIndex];
        } else {
            return weekdayEnds[weekdayEnds.length - 1];
        }
    }

    // fallback
    const tambahanMenit = sks * 50;
    const [h, m] = jamMulai.split(':').map(Number);
    let totalMenit = Math.round(h * 60 + m + tambahanMenit);
    let endH = Math.floor(totalMenit / 60) % 24;
    let endM = totalMenit % 60;
    return `${String(endH).padStart(2, '0')}:${String(endM).padStart(2, '0')}`;
  }

  function getConflicts(day) {
    const rows = state[day].filter(r => r.start && r.end && toMin(r.start) < toMin(r.end));
    const bad = new Set();
    for (let i = 0; i < rows.length; i++) {
      for (let j = i + 1; j < rows.length; j++) {
        const a = rows[i], b = rows[j];
        if (toMin(a.start) < toMin(b.end) && toMin(a.end) > toMin(b.start)) {
          bad.add(a.id);
          bad.add(b.id);
        }
      }
    }
    return bad;
  }

  function hasAnyConflict() { return DAYS.some(d => getConflicts(d).size > 0); }
  function totalCount() { return DAYS.reduce((s, d) => s + state[d].length, 0); }
  function totalDays() { return DAYS.filter(d => state[d].length > 0).length; }

  function render() {
    const container = document.getElementById('days-container');
    container.innerHTML = '';

    DAYS.forEach(day => {
      const rows = state[day];
      const conflicts = getConflicts(day);
      const hasConflict = conflicts.size > 0;
      const hasRows = rows.length > 0;

      const block = document.createElement('div');
      block.className = `bg-white border ${hasConflict ? 'border-red-300 ring-4 ring-red-500/5' : 'border-slate-200'} rounded-2xl shadow-md shadow-slate-200/50 overflow-hidden transition-all duration-200`;
      block.dataset.day = day;

      block.innerHTML = `
        <div class="flex items-center justify-between px-5 py-3.5 bg-slate-50 border-b border-slate-100">
          <div class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
            <div class="w-2.5 h-2.5 rounded-full ${hasConflict ? 'bg-red-500 animate-pulse' : (hasRows ? 'bg-blue-600' : 'bg-slate-300')}"></div>
            ${day}
          </div>
          ${hasRows ? `<span class="text-xs font-bold px-2.5 py-1 bg-blue-50 text-blue-600 border border-blue-100 rounded-full">${rows.length} Matkul</span>` : ''}
        </div>
      `;

      if (hasRows) {
        const labels = document.createElement('div');
        labels.className = "hidden md:grid grid-cols-12 gap-3 px-5 pt-3 pb-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider";
        labels.innerHTML = `
          <div class="col-span-4">Nama Mata Kuliah</div>
          <div class="col-span-2 text-center">Jam Mulai</div>
          <div class="col-span-2 text-center">SKS</div>
          <div class="col-span-3 text-center">Jam Selesai (Auto)</div>
          <div class="col-span-1"></div>
        `;
        block.appendChild(labels);
      }

      const rowsDiv = document.createElement('div');
      rowsDiv.className = "p-4 space-y-3";

      if (!hasRows) {
        rowsDiv.innerHTML = `
          <div class="text-xs font-semibold text-slate-400 text-center py-4 flex items-center justify-center gap-2">
            <i class="far fa-calendar-minus text-sm"></i> Belum ada matkul — klik tombol di bawah untuk menambah
          </div>`;
      } else {
        rows.forEach(row => {
          const isConflict = conflicts.has(row.id);
          const div = document.createElement('div');
          div.className = "grid grid-cols-1 md:grid-cols-12 gap-3 items-center bg-slate-50/50 p-3 md:p-0 rounded-xl border border-slate-100 md:border-0 md:bg-transparent";
          div.dataset.id = row.id;
          
          div.innerHTML = `
            <div class="col-span-1 md:col-span-4">
              <label class="block md:hidden text-[10px] font-bold text-slate-400 uppercase mb-1">Nama Matkul</label>
              <input type="text" data-field="name" placeholder="Cth: Pemrograman Python" value="${escHtml(row.name)}" class="h-10 w-full rounded-xl border ${isConflict ? 'border-red-400 bg-red-50/30' : 'border-slate-200'} bg-white px-3 text-sm font-bold text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
            </div>

            <div class="col-span-1 md:col-span-2">
              <label class="block md:hidden text-[10px] font-bold text-slate-400 uppercase mb-1">Jam Mulai</label>
              <input 
                type="text" 
                data-field="start" 
                placeholder="00:00" 
                maxlength="5" 
                value="${row.start || ''}" 
                class="time-formatter h-10 w-full text-center rounded-xl border ${isConflict ? 'border-red-400 bg-red-50/30' : 'border-slate-200'} bg-white px-2 text-sm font-bold text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition tracking-widest font-mono" 
              />
            </div>

            <div class="col-span-1 md:col-span-2">
              <label class="block md:hidden text-[10px] font-bold text-slate-400 uppercase mb-1">SKS</label>
              <select data-field="sks" class="h-10 w-full text-center rounded-xl border ${isConflict ? 'border-red-400 bg-red-50/30' : 'border-slate-200'} bg-white px-2 text-sm font-bold text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 cursor-pointer transition">
                ${SKS_OPTS.map(s => `<option${s === row.sks ? ' selected' : ''}>${s}</option>`).join('')}
              </select>
            </div>

            <div class="col-span-1 md:col-span-3">
              <label class="block md:hidden text-[10px] font-bold text-slate-400 uppercase mb-1">Jam Selesai (Auto)</label>
              <div class="h-10 w-full rounded-xl border border-slate-200 bg-slate-100 flex items-center justify-center text-sm font-mono font-black text-slate-500 tracking-wider select-none">
                 <i class="far fa-clock text-xs mr-1.5 opacity-60"></i> ${row.end || '--:--'}
              </div>
            </div>

            <div class="col-span-1 md:col-span-1 text-right md:text-center mt-2 md:mt-0">
              <button class="btn-del w-10 h-10 md:w-9 md:h-9 border border-slate-200 hover:border-red-200 hover:bg-red-50 text-slate-400 hover:text-red-500 rounded-xl flex items-center justify-center transition mx-auto md:ml-auto" title="Hapus baris">
                <i class="fas fa-trash-alt text-xs"></i>
              </button>
            </div>
          `;

          div.querySelectorAll('input, select').forEach(el => {
            const handler = () => {
              let targetVal = el.value;
              let currentId = row.id;
              let currentField = el.dataset.field;
              
              if (currentField === 'start') {
                let num = targetVal.replace(/\D/g, '');
                if (num.length > 2) {
                  let hh = num.slice(0, 2);
                  let mm = num.slice(2, 4);
                  if (parseInt(hh) > 23) hh = '23';
                  if (mm.length === 2 && parseInt(mm) > 59) mm = '59';
                  num = hh + ':' + mm;
                } else if (num.length === 2) {
                  if (parseInt(num) > 23) num = '23';
                }
                el.value = num;
                targetVal = num;
              }

              const targetRow = state[day].find(r => r.id === currentId);
              if (targetRow) {
                  targetRow[currentField] = targetVal;
                  if (targetRow.start && targetRow.start.length === 5) {
                      targetRow.end = hitungJamSelesai(targetRow.start, targetRow.sks);
                  } else {
                      targetRow.end = ''; 
                  }
              }
              
              updateFooter();
              triggerSilentConflictCheck(day);
            };
            
            el.addEventListener('change', handler);
            el.addEventListener('input', handler);
          });

          div.querySelector('.btn-del').addEventListener('click', () => { delRow(day, row.id); });
          rowsDiv.appendChild(div);
        });
      }

      block.appendChild(rowsDiv);

      const cm = document.createElement('div');
      cm.className = `conflict-msg text-xs font-bold text-red-600 px-5 py-2.5 bg-red-50 border-t border-red-100 items-center gap-2 ${hasConflict ? 'flex' : 'hidden'}`;
      cm.innerHTML = `<i class="fas fa-exclamation-circle text-red-500"></i> Ada jadwal yang waktunya bertabrakan di hari ${day}`;
      block.appendChild(cm);

      const addBtn = document.createElement('button');
      addBtn.className = "w-[calc(100%-40px)] mx-5 mb-4 py-2 text-xs font-bold text-blue-600 bg-white border border-dashed border-blue-200 rounded-xl hover:bg-blue-50/50 hover:border-blue-500 transition flex items-center justify-center gap-1.5";
      addBtn.innerHTML = `<i class="fas fa-plus-circle text-[10px]"></i> Tambah matkul di hari ${day}`;
      addBtn.addEventListener('click', () => addRow(day));
      block.appendChild(addBtn);

      container.appendChild(block);
    });

    updateFooter();
  }

  function triggerSilentConflictCheck(day) {
    const block = document.querySelector(`[data-day="${day}"]`);
    if (!block) return;
    
    const conflicts = getConflicts(day);
    const isConflict = conflicts.size > 0;
    
    block.classList.toggle('border-red-300', isConflict);
    block.classList.toggle('ring-4', isConflict);
    block.classList.toggle('ring-red-500/5', isConflict);
    
    block.querySelectorAll('.matkul-row-container').forEach(rowEl => {
      const rid = rowEl.dataset.id;
      rowEl.querySelectorAll('input, select').forEach(el => {
        el.classList.toggle('border-red-400', conflicts.has(rid));
        el.classList.toggle('bg-red-50/30', conflicts.has(rid));
      });
    });
    
    const cm = block.querySelector('.conflict-msg');
    if (cm) {
        cm.classList.toggle('flex', isConflict);
        cm.classList.toggle('hidden', !isConflict);
    }
    
    state[day].forEach(row => {
        const rowEl = block.querySelector(`[data-id="${row.id}"]`);
        if (rowEl) {
            const displayDiv = rowEl.querySelector('.col-span-3 div');
            if (displayDiv) displayDiv.innerHTML = `<i class="far fa-clock text-xs mr-1.5 opacity-60"></i> ${row.end || '--:--'}`;
        }
    });
  }

  function updateFooter() {
    document.getElementById('total-matkul').textContent = totalCount();
    document.getElementById('total-hari').textContent = totalDays();
    const conflict = hasAnyConflict();
    const btn = document.getElementById('btn-submit');
    btn.disabled = totalCount() === 0 || conflict;
    const banner = document.getElementById('main-banner');
    banner.classList.toggle('flex', conflict);
    banner.classList.toggle('hidden', !conflict);
  }

  function addRow(day) { state[day].push({ id: makeId(), name: '', start: '', end: '', sks: '2 SKS' }); render(); }
  function delRow(day, id) { state[day] = state[day].filter(r => r.id !== id); render(); }
  function resetAll() { DAYS.forEach(d => { state[d] = []; }); document.getElementById('result-preview').classList.add('hidden'); render(); }

  async function handleSubmit() {
    if (hasAnyConflict() || totalCount() === 0) return;

    const btn = document.getElementById('btn-submit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Menyimpan...';

    try {
      const response = await fetch('/asisten/simpan-jadwal', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: JSON.stringify({ jadwal: state })
      });

      const result = await response.json();

      if (response.ok && result.success) {
        const tbody = document.getElementById('result-tbody');
        tbody.innerHTML = ''; 

        DAYS.forEach(day => {
          state[day].forEach((row, i) => {
            const tr = document.createElement('tr');
            tr.className = "hover:bg-slate-50/50 transition";
            const namaMatkulFormatDB = `${row.name.toUpperCase()} (${row.sks})`;
            tr.innerHTML = `
              <td class="py-3 px-3"><span class="inline-block text-xs font-black px-2.5 py-0.5 bg-blue-100 text-blue-700 rounded-full">${day}</span></td>
              <td class="py-3 px-3 font-bold text-slate-800">${escHtml(namaMatkulFormatDB)}</td>
              <td class="py-3 px-3 font-mono text-xs tracking-wider font-bold text-indigo-600">${row.start} – ${row.end}</td>
            `;
            tbody.appendChild(tr);
          });
        });

        document.getElementById('result-preview').classList.remove('hidden');
        document.getElementById('result-preview').scrollIntoView({ behavior: 'smooth', block: 'start' });
        showToast('✅ ' + result.message, 'success');
      } else {
        showToast('❌ Gagal: ' + (result.message || 'Terjadi kesalahan server'), 'error');
      }
    } catch (error) {
      console.error(error);
      showToast('❌ Terjadi kesalahan jaringan server!', 'error');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-save mr-1.5"></i> Simpan Jadwal';
    }
  }

  function showToast(msg, type) {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.className = "fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-2xl font-bold text-sm text-white transition-all duration-300 z-50 flex items-center gap-2 translate-y-0 opacity-100";
    if (type === 'success') toast.classList.add('bg-emerald-500');
    else toast.classList.add('bg-red-500');
    setTimeout(() => { toast.classList.add('translate-y-8', 'opacity-0', 'pointer-events-none'); }, 3500);
  }

  function escHtml(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  render();
</script>
</body>
</html>