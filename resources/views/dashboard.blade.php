@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $maxHeat = max($heatmapData) > 0 ? max($heatmapData) : 1;
    $heatmapChunks = collect($heatmapData)->chunk(7);
@endphp

@push('styles')
<style>
    .dash-page { max-width: 1100px; margin: 0 auto; }
    .dash-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 22px;
    }
    .dash-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 22px;
    }
    @media (max-width: 768px) {
        .dash-stats { grid-template-columns: 1fr; }
    }
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 20px 22px;
    }
    .stat-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.55px;
        color: var(--text-muted);
        margin-bottom: 8px;
    }
    .stat-value {
        font-size: 1.65rem;
        font-weight: 700;
        color: var(--yellow);
        letter-spacing: -0.5px;
    }
    .stat-sub { font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; }
    .heatmap-section {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 20px 22px 18px;
        margin-bottom: 22px;
        position: relative;
    }
    .heatmap-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 14px;
    }
    .heatmap-head h2 {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.55px;
        color: var(--text-muted);
    }
    .streak-badge {
        flex-shrink: 0;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text);
        background: rgba(245, 197, 24, 0.12);
        border: 1px solid rgba(245, 197, 24, 0.35);
        padding: 6px 12px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .heatmap-scroll {
        overflow-x: auto;
        padding-bottom: 6px;
        -webkit-overflow-scrolling: touch;
    }
    .heatmap-grid {
        display: flex;
        flex-direction: row;
        gap: 3px;
        min-width: min-content;
    }
    .heatmap-col {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .heatmap-cell {
        width: 11px;
        height: 11px;
        border-radius: 2px;
        flex-shrink: 0;
    }
    .heatmap-legend {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 12px;
        font-size: 0.72rem;
        color: var(--text-muted);
    }
    .heatmap-legend-bar {
        display: flex;
        gap: 2px;
    }
    .heatmap-legend-bar span {
        width: 11px;
        height: 11px;
        border-radius: 2px;
    }
    .section-title {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.55px;
        color: var(--text-muted);
        margin-bottom: 14px;
    }
    .recent-list { display: flex; flex-direction: column; gap: 12px; }
    .session-card {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 14px;
        align-items: start;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 16px 18px;
    }
    @media (max-width: 768px) {
        .session-card { grid-template-columns: 1fr; }
        .session-vol { text-align: left; align-items: flex-start !important; }
        .session-pills { gap: 8px; }
        .ex-pill {
            font-size: 0.75rem;
            padding: 6px 10px;
            min-height: 32px;
            display: inline-flex;
            align-items: center;
        }
        .heatmap-scroll {
            margin-left: -4px;
            margin-right: -4px;
            padding-left: 4px;
            padding-right: 4px;
        }
        .chart-wrap { height: 200px; }
        .dash-title { font-size: 1.35rem; }
        .stat-card { padding: 18px 16px; }
        .heatmap-section { padding: 16px 14px 14px; }
        .chart-section { padding: 16px 14px 18px; }
        .heatmap-head {
            flex-wrap: wrap;
            gap: 10px;
        }
    }
    .session-top {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    .split-badge {
        font-size: 0.68rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 6px;
        color: #0f0f0f;
        letter-spacing: 0.4px;
    }
    .split-badge.push { background: #f5c518; color: #111; }
    .split-badge.pull { background: #60a5fa; color: #0f0f0f; }
    .split-badge.legs { background: #a78bfa; color: #0f0f0f; }
    .split-badge.full { background: #4ade80; color: #111; }
    .split-badge.upper { background: #fb923c; color: #111; }
    .split-badge.lower { background: #f87171; color: #111; }
    .split-badge.cardio { background: #2dd4bf; color: #111; }
    .session-date { font-size: 0.9rem; font-weight: 600; color: var(--text); }
    .session-meta { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 10px; }
    .session-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .ex-pill {
        font-size: 0.72rem;
        padding: 4px 9px;
        border-radius: 6px;
        background: var(--bg);
        border: 1px solid var(--border);
        color: var(--text-muted);
    }
    .session-vol {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
    }
    .session-vol-label { font-size: 0.68rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.4px; }
    .session-vol-val { font-size: 1.15rem; font-weight: 700; color: var(--yellow); margin-top: 4px; }
    .btn-delete-workout {
        background: rgba(239,68,68,0.12);
        border: 1px solid rgba(239,68,68,0.35);
        color: #ef4444;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-delete-workout:hover {
        background: rgba(239,68,68,0.22);
    }
    .chart-section {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 20px 22px 24px;
        margin-top: 22px;
    }
    .chart-wrap { position: relative; height: 280px; margin-top: 8px; }
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-muted);
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<div class="dash-page">
    <h1 class="dash-title">Dashboard</h1>

    <div class="dash-stats">
        <div class="stat-card">
            <div class="stat-label">Total Sessions</div>
            <div class="stat-value">{{ number_format($totalSessions) }}</div>
            <div class="stat-sub">Semua waktu</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">This Month</div>
            <div class="stat-value">{{ number_format($thisMonth) }}</div>
            <div class="stat-sub">{{ now()->format('F Y') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Volume</div>
            <div class="stat-value">{{ number_format($totalVolume, 0, ',', '.') }}</div>
            <div class="stat-sub">Σ sets × reps × weight (kg)</div>
        </div>
    </div>

    <div class="heatmap-section">
        <div class="heatmap-head">
            <h2>Consistency — 365 hari</h2>
            @if ($currentStreak > 0)
                <div class="streak-badge">🔥 {{ $currentStreak }} day streak</div>
            @endif
        </div>
        <div class="heatmap-scroll">
            <div class="heatmap-grid">
                @foreach ($heatmapChunks as $week)
                    <div class="heatmap-col">
                        @foreach ($week as $date => $count)
                            @php
                                $ratio = $count > 0 ? max(0.2, $count / $maxHeat) : 0;
                            @endphp
                            <div
                                class="heatmap-cell"
                                title="{{ $date }} — {{ $count }} sesi"
                                style="@if ($count === 0) background: #2a2a2a; @else background: rgba(245, 197, 24, {{ 0.25 + 0.75 * $ratio }}); @endif"
                            ></div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
        <div class="heatmap-legend">
            <span>Less</span>
            <div class="heatmap-legend-bar">
                <span style="background:#2a2a2a"></span>
                <span style="background:rgba(245,197,24,.35)"></span>
                <span style="background:rgba(245,197,24,.55)"></span>
                <span style="background:rgba(245,197,24,.75)"></span>
                <span style="background:#f5c518"></span>
            </div>
            <span>More</span>
        </div>
    </div>

    <div class="section-title">Recent Sessions</div>
    <div class="recent-list">
        @forelse ($recentWorkouts as $w)
            @php
                $vol = $w->exercises->sum(function ($e) {
                    $wt = $e->weight !== null ? (float) $e->weight : 0;
                    return (int) $e->sets * (int) $e->reps * $wt;
                });
                $totalSets = $w->exercises->sum('sets');
            @endphp
            <div class="session-card">
                <div>
                    <div class="session-top">
                        @php $sk = strtolower($w->split); @endphp
                        <span class="split-badge {{ $sk }}">{{ $w->split }}</span>
                        <span class="session-date">{{ $w->date->format('j M Y') }}</span>
                    </div>
                    <div class="session-meta">
                        {{ $w->exercises->count() }} exercise · {{ $totalSets }} total sets
                    </div>
                    <div class="session-pills">
                        @foreach ($w->exercises->take(12) as $ex)
                            <span class="ex-pill">{{ $ex->name }}</span>
                        @endforeach
                        @if ($w->exercises->count() > 12)
                            <span class="ex-pill">+{{ $w->exercises->count() - 12 }}</span>
                        @endif
                    </div>
                </div>
                <div class="session-vol">
                    <div>
                        <div class="session-vol-label">Volume</div>
                        <div class="session-vol-val">{{ number_format($vol, 0, ',', '.') }}</div>
                    </div>
                    <form method="POST" action="{{ route('workout.destroy', $w) }}" onsubmit="return confirmDeleteWorkout(event, '{{ $w->split }}', '{{ $w->date->format('j M Y') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete-workout">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">Belum ada sesi. Mulai catat di <a href="{{ route('log') }}" style="color:var(--yellow);">Log Workout</a>.</div>
        @endforelse
    </div>

    <div class="chart-section">
        <div class="section-title" style="margin-bottom:8px;">Volume per Session</div>
        @if (count($volumePerSession) === 0)
            <div class="empty-state">Belum ada data volume. Setelah kamu menyimpan workout, grafik akan muncul di sini.</div>
        @else
            <div class="chart-wrap">
                <canvas id="volumeChart"></canvas>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDeleteWorkout(e, split, date) {
    e.preventDefault();
    var form = e.target;
    var msg = 'Hapus workout ' + split + ' ' + date + '? Data tidak bisa dikembalikan.';
    if (typeof window.showConfirm === 'function') {
        window.showConfirm(msg, function() { form.submit(); });
    } else {
        if (confirm(msg)) form.submit();
    }
    return false;
}
</script>
@if (count($volumePerSession) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const volumePerSession = @json($volumePerSession);
    const splitColors = @json($splitColors);
    const labels = volumePerSession.map(function (r) {
        const d = r.date.split('-');
        return d[2] + '/' + d[1];
    });
    const dataVals = volumePerSession.map(function (r) { return r.volume; });
    const bg = volumePerSession.map(function (r) { return splitColors[r.split] || '#888888'; });

    const ctx = document.getElementById('volumeChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Volume (kg)',
                data: dataVals,
                backgroundColor: bg,
                borderRadius: 6,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: function (items) {
                            const i = items[0].dataIndex;
                            return volumePerSession[i].date;
                        },
                        label: function (item) {
                            const i = item.dataIndex;
                            return [
                                'Split: ' + volumePerSession[i].split,
                                'Volume: ' + item.formattedValue,
                            ];
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: { color: 'rgba(128,128,128,.15)' },
                    ticks: { color: '#888', maxRotation: 60, minRotation: 45, font: { size: 10 } },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(128,128,128,.15)' },
                    ticks: { color: '#888' },
                },
            },
        },
    });
})();
</script>
@endif
@endpush