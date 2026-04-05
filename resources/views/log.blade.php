@extends('layouts.app')

@section('title', 'Log Workout')

@push('styles')
<style>
    .log-page { max-width: 640px; margin: 0 auto; }
    .log-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 20px;
    }
    .log-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 20px 22px;
        margin-bottom: 18px;
    }
    .log-card-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--text-muted);
        margin-bottom: 10px;
    }
    .date-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }
    .date-display {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--yellow);
    }
    .date-input-wrap input[type="date"] {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--bg);
        color: var(--text);
    }
    .split-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .split-chip {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid #333;
        cursor: pointer;
        background: transparent;
        color: #a0a0a0;
        transition: background-color 0.15s, border-color 0.15s, color 0.15s, transform 0.1s;
    }
    .split-chip:hover:not(.active) {
        color: var(--text-muted);
        border-color: #444;
    }
    .split-chip:active { transform: scale(0.97); }
    .split-chip.active {
        background: #f5c518;
        border-color: #f5c518;
        color: #0a0a0a;
        font-weight: 700;
    }
    .add-ex-grid {
        display: grid;
        gap: 14px;
    }
    .add-ex-row {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 10px;
        align-items: end;
    }
    .form-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 6px;
    }
    .form-input, .form-select {
        width: 100%;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--bg);
        color: var(--text);
    }
    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: var(--yellow);
    }
    .metrics-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr auto;
        gap: 10px;
        align-items: end;
    }
    .btn-add-ex {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        border: none;
        background: var(--yellow);
        color: #111;
        font-size: 1.4rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }
    .btn-add-ex:hover { background: var(--yellow-dark); }
    .new-ex-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 4px;
    }
    .new-ex-row .form-input { flex: 1; min-width: 160px; }
    .btn-tambah {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem;
        font-weight: 700;
        padding: 10px 18px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--border);
        color: var(--text);
        cursor: pointer;
    }
    .btn-tambah:hover { border-color: var(--text-muted); }
    .ex-list { display: flex; flex-direction: column; gap: 10px; }
    .ex-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: var(--bg);
    }
    .ex-item-text { font-size: 0.9rem; font-weight: 600; color: var(--text); }
    .ex-item-remove {
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 1.25rem;
        cursor: pointer;
        padding: 4px 8px;
        line-height: 1;
    }
    .ex-item-remove:hover { color: #ef4444; }
    .notes-ta {
        width: 100%;
        min-height: 88px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--bg);
        color: var(--text);
        resize: vertical;
    }
    .btn-save {
        width: 100%;
        font-family: 'DM Sans', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        padding: 14px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
    }
    .btn-save:disabled {
        background: var(--border);
        color: var(--text-muted);
        cursor: not-allowed;
    }
    .btn-save:not(:disabled) {
        background: #f5c518;
        color: #111;
    }
    .btn-save:not(:disabled):hover { background: #d4a900; }
    .log-errors {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.35);
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 16px;
        font-size: 0.875rem;
        color: #f87171;
    }
    .log-errors ul { margin: 0; padding-left: 18px; }

    @media (max-width: 768px) {
        .log-page { padding: 0 2px; }
        .log-title { font-size: 1.35rem; }
        .log-card { padding: 16px 14px; margin-bottom: 14px; }
        .split-chips {
            gap: 8px;
        }
        .split-chip {
            min-height: 44px;
            padding: 10px 14px;
        }
        .metrics-row {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .metrics-row .btn-add-ex {
            grid-column: 1;
            width: 100%;
            height: 48px;
            min-height: 48px;
        }
        .form-input,
        .form-select {
            min-height: 44px;
        }
        .date-input-wrap input[type="date"] {
            min-height: 44px;
        }
        .new-ex-row {
            flex-direction: column;
            align-items: stretch;
        }
        .new-ex-row .form-input {
            min-width: 0;
            width: 100%;
        }
        .btn-tambah {
            width: 100%;
            min-height: 48px;
        }
        .ex-item {
            flex-wrap: wrap;
            align-items: flex-start;
        }
        .ex-item-text {
            flex: 1 1 100%;
            min-width: 0;
            word-break: break-word;
            font-size: 0.85rem;
            line-height: 1.45;
        }
        .ex-item-remove {
            min-width: 44px;
            min-height: 44px;
            margin-left: auto;
        }
        .btn-save {
            min-height: 48px;
        }
        .date-row {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="log-page">
    <h1 class="log-title">Log Workout</h1>

    @if ($errors->any())
        <div class="log-errors">
            <ul>
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="workoutForm" action="{{ route('log.store') }}" method="POST">
        @csrf
        <input type="hidden" name="split" id="fieldSplit" value="PUSH">

        <div class="log-card">
            <div class="log-card-label">Tanggal</div>
            <div class="date-row">
                <span class="date-display" id="dateLabel">{{ $todayLabel }}</span>
                <div class="date-input-wrap">
                    <input type="date" name="date" id="fieldDate" value="{{ $todayYmd }}" max="{{ $todayYmd }}" required>
                </div>
            </div>
        </div>

        <div class="log-card">
            <div class="log-card-label">Pilih split</div>
            <div class="split-chips" id="splitChips" role="group" aria-label="Split">
                @foreach ($splits as $split)
                    <button type="button" class="split-chip {{ $split === 'PUSH' ? 'active' : '' }}" data-split="{{ $split }}">{{ $split }}</button>
                @endforeach
            </div>
        </div>

        <div class="log-card">
            <div class="log-card-label">Tambah exercise</div>
            <div class="add-ex-grid">
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
                        <input type="number" class="form-input" id="fieldWeight" min="0" step="0.01" placeholder="Opsional">
                    </div>
                    <button type="button" class="btn-add-ex" id="btnAddEx" title="Tambah ke list">+</button>
                </div>
                <div>
                    <label class="form-label" for="newExName">Exercise baru (custom)</label>
                    <div class="new-ex-row">
                        <input type="text" class="form-input" id="newExName" placeholder="Nama exercise baru..." autocomplete="off">
                        <button type="button" class="btn-tambah" id="btnTambahCustom">Tambah</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="log-card">
            <div class="log-card-label">Exercise untuk sesi ini</div>
            <div class="ex-list" id="exList"></div>
            <p id="exEmptyHint" style="font-size:0.875rem;color:var(--text-muted);margin-top:8px;">Belum ada exercise. Tambahkan minimal satu untuk mengaktifkan simpan.</p>
        </div>

        <div class="log-card">
            <div class="log-card-label">Notes (opsional)</div>
            <textarea class="notes-ta" name="notes" id="fieldNotes" placeholder="Catatan singkat…">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="btn-save" id="btnSave" disabled>Save Workout</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const exercisesBySplit = @json($exercisesBySplit);
    const customBySplit = @json($customBySplit);

    let currentSplit = 'PUSH';
    const exList = [];

    function formatDateLabel(ymd) {
        const d = new Date(ymd + 'T12:00:00');
        return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function mergeOptions(split) {
        const base = exercisesBySplit[split] || [];
        const customs = customBySplit[split] || [];
        const set = new Set();
        base.forEach(function (n) { set.add(n); });
        customs.forEach(function (n) { set.add(n); });
        return Array.from(set).sort(function (a, b) { return a.localeCompare(b); });
    }

    function rebuildSelect() {
        const sel = document.getElementById('exSelect');
        const prev = sel.value;
        sel.innerHTML = '<option value="">— Pilih exercise —</option>';
        mergeOptions(currentSplit).forEach(function (name) {
            const opt = document.createElement('option');
            opt.value = name;
            opt.textContent = name;
            sel.appendChild(opt);
        });
        if (mergeOptions(currentSplit).indexOf(prev) !== -1) sel.value = prev;
        else sel.value = '';
    }

    function setSplit(split) {
        currentSplit = split;
        document.getElementById('fieldSplit').value = split;
        document.querySelectorAll('.split-chip').forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-split') === split);
        });
        exList.length = 0;
        renderList();
        rebuildSelect();
        clearMetricInputs(true);
    }

    function clearMetricInputs(clearSelect) {
        document.getElementById('fieldSets').value = '';
        document.getElementById('fieldReps').value = '';
        document.getElementById('fieldWeight').value = '';
        if (clearSelect) document.getElementById('exSelect').value = '';
    }

    async function fetchLastExercise(name) {
        if (!name) {
            clearMetricInputs(false);
            return;
        }
        const url = '{{ url('/log/last-exercise') }}' + '?exercise_name=' + encodeURIComponent(name);
        const r = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await r.json();
        if (data && typeof data.sets === 'number') {
            document.getElementById('fieldSets').value = data.sets;
            document.getElementById('fieldReps').value = data.reps;
            document.getElementById('fieldWeight').value = data.weight != null ? data.weight : '';
        } else {
            document.getElementById('fieldSets').value = '';
            document.getElementById('fieldReps').value = '';
            document.getElementById('fieldWeight').value = '';
        }
    }

    function renderList() {
        const wrap = document.getElementById('exList');
        const hint = document.getElementById('exEmptyHint');
        const btn = document.getElementById('btnSave');
        wrap.innerHTML = '';
        exList.forEach(function (row, idx) {
            const w = row.weight !== '' && row.weight != null ? row.weight + ' kg' : '— kg';
            const el = document.createElement('div');
            el.className = 'ex-item';
            el.innerHTML = '<span class="ex-item-text">' + escapeHtml(row.name) + ' — ' + row.sets + ' × ' + row.reps + ' @ ' + w + '</span>' +
                '<button type="button" class="ex-item-remove" data-i="' + idx + '" aria-label="Hapus">×</button>';
            wrap.appendChild(el);
        });
        hint.style.display = exList.length ? 'none' : 'block';
        btn.disabled = exList.length === 0;
        wrap.querySelectorAll('.ex-item-remove').forEach(function (b) {
            b.addEventListener('click', function () {
                const i = parseInt(b.getAttribute('data-i'), 10);
                exList.splice(i, 1);
                renderList();
            });
        });
    }

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    document.getElementById('fieldDate').addEventListener('change', function () {
        document.getElementById('dateLabel').textContent = formatDateLabel(this.value);
    });

    document.getElementById('splitChips').addEventListener('click', function (e) {
        const btn = e.target.closest('.split-chip');
        if (!btn) return;
        setSplit(btn.getAttribute('data-split'));
    });

    document.getElementById('exSelect').addEventListener('change', function () {
        fetchLastExercise(this.value);
    });

    document.getElementById('btnAddEx').addEventListener('click', function () {
        const name = document.getElementById('exSelect').value.trim();
        const sets = parseInt(document.getElementById('fieldSets').value, 10);
        const reps = parseInt(document.getElementById('fieldReps').value, 10);
        const wRaw = document.getElementById('fieldWeight').value;
        if (!name) {
            showToast('Pilih exercise dulu.', 'error');
            return;
        }
        if (!sets || sets < 1 || !reps || reps < 1) {
            showToast('Isi sets dan reps (minimal 1).', 'error');
            return;
        }
        const row = { name: name, sets: sets, reps: reps, weight: wRaw === '' ? '' : wRaw };
        exList.push(row);
        renderList();
        document.getElementById('exSelect').value = '';
        clearMetricInputs(false);
    });

    document.getElementById('btnTambahCustom').addEventListener('click', function () {
        const inp = document.getElementById('newExName');
        const name = inp.value.trim();
        if (!name) {
            showToast('Isi nama exercise.', 'error');
            return;
        }
        if (!customBySplit[currentSplit]) customBySplit[currentSplit] = [];
        if (customBySplit[currentSplit].indexOf(name) === -1) {
            customBySplit[currentSplit].push(name);
            customBySplit[currentSplit].sort();
        }
        rebuildSelect();
        document.getElementById('exSelect').value = name;
        fetchLastExercise(name);
        inp.value = '';
        showToast('Exercise ditambahkan ke daftar (tersimpan ke akun saat Save Workout).', 'success');
    });

    document.getElementById('workoutForm').addEventListener('submit', function (e) {
        const form = e.target;
        form.querySelectorAll('input.dynamic-ex').forEach(function (n) { n.remove(); });
        exList.forEach(function (row, i) {
            [['name', row.name], ['sets', row.sets], ['reps', row.reps]].forEach(function (pair) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.className = 'dynamic-ex';
                input.name = 'exercises[' + i + '][' + pair[0] + ']';
                input.value = pair[1];
                form.appendChild(input);
            });
            if (row.weight !== '' && row.weight != null) {
                const w = document.createElement('input');
                w.type = 'hidden';
                w.className = 'dynamic-ex';
                w.name = 'exercises[' + i + '][weight]';
                w.value = row.weight;
                form.appendChild(w);
            }
        });
    });

    rebuildSelect();
    renderList();
})();
</script>
@endpush
