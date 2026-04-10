@extends('layouts.app')

@section('title', 'Edit Workout')

@push('styles')
<style>
    .log-page { max-width: 640px; margin: 0 auto; }
    .log-title { font-size: 1.5rem; font-weight: 700; color: var(--text); margin-bottom: 4px; }
    .log-subtitle { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px; }
    .log-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 20px 22px;
        margin-bottom: 18px;
    }
    .log-card-label {
        font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.6px; color: var(--text-muted); margin-bottom: 10px;
    }
    .date-row { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
    .date-display { font-size: 1.35rem; font-weight: 700; color: var(--yellow); }
    .date-input-wrap input[type="date"] {
        font-family: 'DM Sans', sans-serif; font-size: 0.9rem; padding: 8px 12px;
        border-radius: 8px; border: 1px solid var(--border); background: var(--bg); color: var(--text);
    }
    .split-chips { display: flex; flex-wrap: wrap; gap: 8px; }
    .split-chip {
        font-family: 'DM Sans', sans-serif; font-size: 0.78rem; font-weight: 600;
        padding: 8px 12px; border-radius: 999px; border: 1px solid #333;
        cursor: pointer; background: transparent; color: #a0a0a0;
        transition: background-color 0.15s, border-color 0.15s, color 0.15s, transform 0.1s;
    }
    .split-chip:hover:not(.active):not(.locked) { color: var(--text-muted); border-color: #444; }
    .split-chip:active:not(.locked) { transform: scale(0.97); }
    .split-chip.active { background: #f5c518; border-color: #f5c518; color: #0a0a0a; font-weight: 700; }
    .split-chip.locked:not(.active) { opacity: 0.3; cursor: not-allowed; }
    .split-chip.deleted {
        border-color: rgba(245,197,24,0.4); color: var(--yellow);
        cursor: default; opacity: 0.75;
    }
    .split-deleted-label {
        font-size: 0.72rem; color: var(--text-muted); margin-top: 8px;
    }
    .split-deleted-notice {
        margin-top: 10px; font-size: 0.8rem; color: var(--text-muted);
        background: rgba(245,197,24,0.06); border: 1px solid rgba(245,197,24,0.2);
        border-radius: 8px; padding: 8px 12px;
    }
    .lock-badge {
        display: none; align-items: center; gap: 6px; margin-top: 10px;
        font-size: 0.78rem; font-weight: 600; color: var(--yellow);
        background: rgba(245,197,24,0.08); border: 1px solid rgba(245,197,24,0.25);
        border-radius: 8px; padding: 6px 12px; width: fit-content;
    }
    .add-ex-grid { display: grid; gap: 14px; }
    .form-label { display: block; font-size: 0.8rem; font-weight: 600; color: var(--text); margin-bottom: 6px; }
    .form-input, .form-select {
        width: 100%; font-family: 'DM Sans', sans-serif; font-size: 0.9rem;
        padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border);
        background: var(--bg); color: var(--text);
    }
    .form-input:focus, .form-select:focus { outline: none; border-color: var(--yellow); }
    .metrics-row { display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 10px; align-items: end; }
    .btn-add-ex {
        width: 44px; height: 44px; border-radius: 10px; border: none;
        background: var(--yellow); color: #111; font-size: 1.4rem; font-weight: 700;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
    }
    .btn-add-ex:hover { background: var(--yellow-dark); }
    .new-ex-row { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 4px; }
    .new-ex-row .form-input { flex: 1; min-width: 160px; }
    .btn-tambah {
        font-family: 'DM Sans', sans-serif; font-size: 0.875rem; font-weight: 700;
        padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border);
        background: var(--border); color: var(--text); cursor: pointer;
    }
    .btn-tambah:hover { border-color: var(--text-muted); }

    /* Cardio box */
    .cardio-card {
        background-color: var(--bg-card);
        border: 1px solid rgba(45, 212, 191, 0.25);
        border-radius: 14px;
        padding: 16px 22px;
        margin-bottom: 18px;
    }
    .cardio-header { display: flex; align-items: center; justify-content: space-between; }
    .cardio-label {
        font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.6px; color: #2dd4bf;
    }
    .btn-cardio-toggle {
        font-family: 'DM Sans', sans-serif; font-size: 0.78rem; font-weight: 600;
        padding: 5px 12px; border-radius: 6px; cursor: pointer;
        background: rgba(45,212,191,0.1); border: 1px solid rgba(45,212,191,0.3);
        color: #2dd4bf; transition: background 0.15s;
    }
    .btn-cardio-toggle:hover { background: rgba(45,212,191,0.18); }
    .cardio-fields { display: none; margin-top: 14px; }
    .cardio-fields.open { display: block; }
    .cardio-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }
    .cardio-hint { font-size: 0.78rem; color: var(--text-muted); margin-top: 8px; }

    .ex-list { display: flex; flex-direction: column; gap: 10px; }
    .ex-item {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        padding: 14px 16px; border-radius: 10px; border: 1px solid var(--border); background: var(--bg);
    }
    .ex-item-text { font-size: 0.9rem; font-weight: 600; color: var(--text); }
    .ex-item-remove { background: none; border: none; color: var(--text-muted); font-size: 1.25rem; cursor: pointer; padding: 4px 8px; }
    .ex-item-remove:hover { color: #ef4444; }
    .notes-ta {
        width: 100%; min-height: 88px; font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem; padding: 12px; border-radius: 8px;
        border: 1px solid var(--border); background: var(--bg); color: var(--text); resize: vertical;
    }
    .btn-save {
        width: 100%; font-family: 'DM Sans', sans-serif; font-size: 1rem; font-weight: 700;
        padding: 14px; border-radius: 10px; border: none; cursor: pointer; transition: background 0.15s, color 0.15s;
    }
    .btn-save:disabled { background: var(--border); color: var(--text-muted); cursor: not-allowed; }
    .btn-save:not(:disabled) { background: #f5c518; color: #111; }
    .btn-save:not(:disabled):hover { background: #d4a900; }
    .btn-cancel {
        display: block; width: 100%; text-align: center;
        font-family: 'DM Sans', sans-serif; font-size: 0.9rem; font-weight: 600;
        padding: 12px; border-radius: 10px; border: 1px solid var(--border);
        color: var(--text-muted); text-decoration: none; margin-top: 10px;
        transition: border-color 0.15s, color 0.15s;
    }
    .btn-cancel:hover { border-color: var(--text-muted); color: var(--text); }
    .log-errors {
        background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.35);
        border-radius: 10px; padding: 12px 14px; margin-bottom: 16px;
        font-size: 0.875rem; color: #f87171;
    }
    .log-errors ul { margin: 0; padding-left: 18px; }

    @media (max-width: 768px) {
        .log-page { padding: 0 2px; }
        .log-card { padding: 16px 14px; margin-bottom: 14px; }
        .cardio-card { padding: 14px 14px; margin-bottom: 14px; }
        .split-chip { min-height: 44px; padding: 10px 14px; }
        .metrics-row { grid-template-columns: 1fr; gap: 12px; }
        .metrics-row .btn-add-ex { grid-column: 1; width: 100%; height: 48px; }
        .form-input, .form-select { min-height: 44px; }
        .date-input-wrap input[type="date"] { min-height: 44px; }
        .new-ex-row { flex-direction: column; align-items: stretch; }
        .new-ex-row .form-input { min-width: 0; width: 100%; }
        .btn-tambah { width: 100%; min-height: 48px; }
        .cardio-grid { grid-template-columns: 1fr; }
        .ex-item { flex-wrap: wrap; }
        .ex-item-text { flex: 1 1 100%; font-size: 0.85rem; }
        .ex-item-remove { min-width: 44px; min-height: 44px; margin-left: auto; }
        .btn-save { min-height: 48px; }
        .date-row { flex-direction: column; align-items: flex-start; }
    }
</style>
@endpush

@section('content')
<div class="log-page">
    <h1 class="log-title">Edit Workout</h1>
    <p class="log-subtitle">{{ $workout->day_label }} — {{ $workout->date->format('j M Y') }}</p>

    @if ($errors->any())
        <div class="log-errors">
            <ul>
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="workoutForm" action="{{ route('workout.update', $workout) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="split" id="fieldSplit" value="{{ $currentSplit }}">

        {{-- Cardio hidden inputs --}}
        <input type="hidden" name="cardio_duration" id="hiddenCardioDuration">
        <input type="hidden" name="cardio_speed"    id="hiddenCardioSpeed">
        <input type="hidden" name="cardio_incline"  id="hiddenCardioIncline">

        <div class="log-card">
            <div class="log-card-label">Tanggal</div>
            <div class="date-row">
                <span class="date-display" id="dateLabel">{{ \Carbon\Carbon::parse($currentDate)->format('j M Y') }}</span>
                <div class="date-input-wrap">
                    <input type="date" name="date" id="fieldDate" value="{{ $currentDate }}" max="{{ $todayYmd }}" required>
                </div>
            </div>
        </div>

        <div class="log-card">
            <div class="log-card-label">Pilih split</div>
            @if (!$splitExists)
                <div class="split-chips">
                    <button type="button" class="split-chip deleted" disabled>{{ $currentSplit }}</button>
                </div>
                <div class="split-deleted-label">(dihapus)</div>
                <div class="split-deleted-notice">Split ini sudah dihapus. Exercise tetap bisa diedit.</div>
            @else
                <div class="split-chips" id="splitChips">
                    @foreach ($splits as $split)
                        <button type="button" class="split-chip {{ $split['name'] === $currentSplit ? 'active' : '' }}" data-split="{{ $split['name'] }}">{{ $split['name'] }}</button>
                    @endforeach
                </div>
                <div class="lock-badge" id="lockBadge">
                    🔒 <span id="lockLabel">{{ $workout->day_label }}</span>
                </div>
            @endif
        </div>

        <div class="log-card">
            <div class="log-card-label">Tambah exercise</div>
            <div class="add-ex-grid">
                <div>
                    <label class="form-label" for="newExName">Exercise baru (custom)</label>
                    <div class="new-ex-row">
                        <input type="text" class="form-input" id="newExName" placeholder="Nama exercise baru..." autocomplete="off">
                        <button type="button" class="btn-tambah" id="btnTambahCustom">Tambah</button>
                    </div>
                </div>
                <div>
                    <label class="form-label" for="exSelect">Exercise</label>
                    <select class="form-select" id="exSelect">
                        <option value="">— Pilih exercise —</option>
                    </select>
                </div>
                <div class="metrics-row">
                    <div>
                        <label class="form-label" for="fieldSets">Sets</label>
                        <input type="number" class="form-input" id="fieldSets" min="1" max="500" placeholder="0">
                    </div>
                    <div>
                        <label class="form-label" for="fieldReps">Reps — isi yang terbesar</label>
                        <input type="number" class="form-input" id="fieldReps" min="1" max="500" placeholder="0">
                    </div>
                    <div>
                        <label class="form-label" for="fieldWeight">Weight (kg)</label>
                        <input type="number" class="form-input" id="fieldWeight" min="1" step="0.01" placeholder="1">
                    </div>
                    <button type="button" class="btn-add-ex" id="btnAddEx" title="Tambah ke list">+</button>
                </div>
            </div>
        </div>

        {{-- CARDIO BOX --}}
        <div class="cardio-card">
            <div class="cardio-header">
                <span class="cardio-label">Cardio (opsional)</span>
                <button type="button" class="btn-cardio-toggle" id="btnCardioToggle" onclick="toggleCardio()">+ Tambah</button>
            </div>
            <div class="cardio-fields" id="cardioFields">
                <div class="cardio-grid">
                    <div>
                        <label class="form-label" for="cardioDuration">Durasi (menit)</label>
                        <input type="number" class="form-input" id="cardioDuration" min="1" max="600" placeholder="30" value="{{ $cardioDuration }}">
                    </div>
                    <div>
                        <label class="form-label" for="cardioSpeed">Speed (km/h)</label>
                        <input type="number" class="form-input" id="cardioSpeed" min="0" max="30" step="0.5" placeholder="6.5" value="{{ $cardioSpeed }}">
                    </div>
                    <div>
                        <label class="form-label" for="cardioIncline">Incline (max 15)</label>
                        <input type="number" class="form-input" id="cardioIncline" min="0" max="15" step="0.5" placeholder="5" value="{{ $cardioIncline }}">
                    </div>
                </div>
                <div class="cardio-hint">Durasi wajib diisi jika ingin menyimpan cardio.</div>
            </div>
        </div>

        <div class="log-card">
            <div class="log-card-label">Exercise untuk sesi ini</div>
            <div class="ex-list" id="exList"></div>
            <p id="exEmptyHint" style="font-size:0.875rem;color:var(--text-muted);margin-top:8px;">Belum ada exercise. Tambahkan minimal satu exercise atau isi durasi cardio untuk mengaktifkan simpan.</p>
        </div>

        <div class="log-card">
            <div class="log-card-label">Notes (opsional)</div>
            <textarea class="notes-ta" name="notes" id="fieldNotes" placeholder="Catatan singkat…">{{ $currentNotes }}</textarea>
        </div>

        <button type="submit" class="btn-save" id="btnSave" disabled>Simpan Perubahan</button>
        <a href="{{ route('dashboard') }}" class="btn-cancel">Batal</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const exercisesBySplit = @json($exercisesBySplit);
    const initExList       = @json($existingExercises);
    const initCardioOpen   = {{ $initCardioOpen ? 'true' : 'false' }};

    let currentSplit = {!! json_encode($currentSplit) !!};
    let splitLocked  = false;
    let cardioOpen   = false;
    const exList     = initExList.slice();

    function formatDateLabel(ymd) {
        const d = new Date(ymd + 'T12:00:00');
        return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function mergeOptions(split) {
        return (exercisesBySplit[split] || []).slice();
    }

    function rebuildSelect() {
        const sel  = document.getElementById('exSelect');
        const prev = sel.value;
        const opts = mergeOptions(currentSplit);
        sel.innerHTML = '<option value="">— Pilih exercise —</option>';
        opts.forEach(function (name) {
            const opt = document.createElement('option');
            opt.value = name; opt.textContent = name;
            sel.appendChild(opt);
        });
        sel.value = opts.includes(prev) ? prev : '';
    }

    function setSplit(split) {
        if (splitLocked) return;
        currentSplit = split;
        document.getElementById('fieldSplit').value = split;
        document.querySelectorAll('.split-chip').forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-split') === split);
        });
        rebuildSelect();
        clearMetricInputs(true);
    }

    function lockSplit() {
        if (splitLocked) return;
        splitLocked = true;
        document.querySelectorAll('.split-chip').forEach(function (btn) {
            if (!btn.classList.contains('active')) btn.classList.add('locked');
        });
        const badge = document.getElementById('lockBadge');
        if (badge) {
            badge.style.display = 'flex';
            document.getElementById('lockLabel').textContent = currentSplit;
        }
    }

    function unlockSplit() {
        if (!splitLocked) return;
        splitLocked = false;
        document.querySelectorAll('.split-chip').forEach(function (btn) {
            btn.classList.remove('locked');
        });
        const badge = document.getElementById('lockBadge');
        if (badge) badge.style.display = 'none';
    }

    function clearMetricInputs(clearSelect) {
        document.getElementById('fieldSets').value       = '';
        document.getElementById('fieldReps').value       = '';
        document.getElementById('fieldWeight').value     = '';
        document.getElementById('fieldSets').placeholder   = '0';
        document.getElementById('fieldReps').placeholder   = '0';
        document.getElementById('fieldWeight').placeholder = '1';
        if (clearSelect) document.getElementById('exSelect').value = '';
    }

    async function fetchLastExercise(name) {
        if (!name) { clearMetricInputs(false); return; }
        const url = '{!! url('/log/last-exercise') !!}?exercise_name=' + encodeURIComponent(name);
        const r   = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await r.json();
        if (data && typeof data.sets === 'number') {
            document.getElementById('fieldSets').placeholder   = data.sets;
            document.getElementById('fieldReps').placeholder   = data.reps;
            document.getElementById('fieldWeight').placeholder = data.weight != null ? data.weight : '1';
        } else {
            clearMetricInputs(false);
        }
    }

    function updateSaveBtn() {
        const hasEx  = exList.length > 0;
        const hasDur = document.getElementById('cardioDuration').value.trim() !== '';
        document.getElementById('btnSave').disabled = !(hasEx || hasDur);
    }

    function renderList() {
        const wrap = document.getElementById('exList');
        const hint = document.getElementById('exEmptyHint');
        wrap.innerHTML = '';
        exList.forEach(function (row, idx) {
            const w  = row.weight !== '' && row.weight != null ? row.weight + ' kg' : '— kg';
            const el = document.createElement('div');
            el.className = 'ex-item';
            el.innerHTML =
                '<span class="ex-item-text">' + escapeHtml(row.name) + ' — ' + row.sets + ' × ' + row.reps + ' @ ' + w + '</span>' +
                '<button type="button" class="ex-item-remove" data-i="' + idx + '" aria-label="Hapus">×</button>';
            wrap.appendChild(el);
        });
        hint.style.display = exList.length ? 'none' : 'block';
        if (exList.length === 0) unlockSplit();
        else lockSplit();
        updateSaveBtn();

        wrap.querySelectorAll('.ex-item-remove').forEach(function (b) {
            b.addEventListener('click', function () {
                exList.splice(parseInt(b.getAttribute('data-i'), 10), 1);
                renderList();
            });
        });
    }

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    window.toggleCardio = function () {
        cardioOpen = !cardioOpen;
        document.getElementById('cardioFields').classList.toggle('open', cardioOpen);
        document.getElementById('btnCardioToggle').textContent = cardioOpen ? '− Tutup' : '+ Tambah';
        updateSaveBtn();
    };

    document.getElementById('cardioDuration').addEventListener('input', updateSaveBtn);

    document.getElementById('fieldDate').addEventListener('change', function () {
        document.getElementById('dateLabel').textContent = formatDateLabel(this.value);
    });

    const splitChipsEl = document.getElementById('splitChips');
    if (splitChipsEl) {
        splitChipsEl.addEventListener('click', function (e) {
            const btn = e.target.closest('.split-chip');
            if (!btn || splitLocked) return;
            setSplit(btn.getAttribute('data-split'));
        });
    }

    document.getElementById('exSelect').addEventListener('change', function () {
        fetchLastExercise(this.value);
    });

    document.getElementById('btnAddEx').addEventListener('click', function () {
        const name   = document.getElementById('exSelect').value.trim();
        const sets   = parseInt(document.getElementById('fieldSets').value, 10);
        const reps   = parseInt(document.getElementById('fieldReps').value, 10);
        const wRaw   = document.getElementById('fieldWeight').value;
        const weight = parseFloat(wRaw);
        if (!name)               { showToast('Pilih exercise dulu.', 'error'); return; }
        if (!sets || sets < 1)   { showToast('Sets minimal 1.', 'error'); return; }
        if (!reps || reps < 1)   { showToast('Reps minimal 1.', 'error'); return; }
        if (!wRaw || weight < 1) { showToast('Weight minimal 1 kg.', 'error'); return; }
        exList.push({ name, sets, reps, weight: wRaw });
        renderList();
        document.getElementById('exSelect').value = '';
        clearMetricInputs(false);
    });

    document.getElementById('btnTambahCustom').addEventListener('click', function () {
        const inp  = document.getElementById('newExName');
        const name = inp.value.trim();
        if (!name) { showToast('Isi nama exercise.', 'error'); return; }
        if (!exercisesBySplit[currentSplit]) exercisesBySplit[currentSplit] = [];
        if (!exercisesBySplit[currentSplit].includes(name)) {
            exercisesBySplit[currentSplit].push(name);
            exercisesBySplit[currentSplit].sort();
        }
        rebuildSelect();
        document.getElementById('exSelect').value = name;
        fetchLastExercise(name);
        inp.value = '';
        showToast('Exercise ditambahkan ke daftar.', 'success');
    });

    document.getElementById('workoutForm').addEventListener('submit', function (e) {
        const dur = document.getElementById('cardioDuration').value.trim();
        if (cardioOpen && dur !== '') {
            document.getElementById('hiddenCardioDuration').value = dur;
            document.getElementById('hiddenCardioSpeed').value    = document.getElementById('cardioSpeed').value;
            document.getElementById('hiddenCardioIncline').value  = document.getElementById('cardioIncline').value;
        }

        const form = e.target;
        form.querySelectorAll('input.dynamic-ex').forEach(n => n.remove());
        exList.forEach(function (row, i) {
            [['name', row.name], ['sets', row.sets], ['reps', row.reps], ['weight', row.weight]].forEach(function ([key, val]) {
                const input = document.createElement('input');
                input.type = 'hidden'; input.className = 'dynamic-ex';
                input.name = 'exercises[' + i + '][' + key + ']'; input.value = val;
                form.appendChild(input);
            });
        });
    });

    // Init
    rebuildSelect();
    renderList();

    if (initCardioOpen) {
        cardioOpen = true;
        document.getElementById('cardioFields').classList.add('open');
        document.getElementById('btnCardioToggle').textContent = '− Tutup';
        updateSaveBtn();
    }
})();
</script>
@endpush
