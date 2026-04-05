@extends('layouts.app')

@section('title', 'Lift Progress')

@push('styles')
<style>
    .prog-page { max-width: 900px; margin: 0 auto; }
    .prog-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 20px;
    }
    .lift-selector-wrap {
        margin-bottom: 22px;
        overflow-x: auto;
        padding-bottom: 4px;
        -webkit-overflow-scrolling: touch;
    }
    .lift-selector {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .lift-btn {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 8px 14px;
        border-radius: 999px;
        border: 1px solid #333;
        background: transparent;
        color: #a0a0a0;
        cursor: pointer;
        white-space: nowrap;
        text-decoration: none;
        display: inline-block;
        transition: background 0.15s, border-color 0.15s, color 0.15s;
    }
    .lift-btn:hover { border-color: #444; color: var(--text-muted); }
    .lift-btn.active {
        background: #f5c518;
        border-color: #f5c518;
        color: #0a0a0a;
        font-weight: 700;
    }
    .pr-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }
    @media (max-width: 768px) {
        .pr-grid { grid-template-columns: 1fr; }
    }
    .pr-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 18px 20px;
    }
    .pr-label {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        margin-bottom: 8px;
    }
    .pr-value {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--yellow);
    }
    .pr-delta {
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 6px;
    }
    .pr-delta.up { color: #4ade80; }
    .pr-delta.down { color: #f87171; }
    .pr-delta.neutral { color: var(--text-muted); }
    .chart-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 20px 22px 24px;
    }
    .chart-toggle {
        display: flex;
        gap: 8px;
        margin-top: 16px;
        flex-wrap: wrap;
    }
    .chart-toggle button {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--bg);
        color: var(--text-muted);
        cursor: pointer;
    }
    .chart-toggle button:hover { border-color: var(--text-muted); color: var(--text); }
    .chart-toggle button.on {
        background: rgba(245, 197, 24, 0.15);
        border-color: var(--yellow);
        color: var(--yellow);
        font-weight: 700;
    }
    .chart-wrap-prog { position: relative; height: 300px; margin-top: 12px; }
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: var(--text-muted);
        font-size: 0.9rem;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
    }
    .empty-state a { color: var(--yellow); }

    @media (max-width: 768px) {
        .prog-page { padding: 0 2px; }
        .prog-title { font-size: 1.35rem; }
        .chart-wrap-prog { height: 220px; }
        .chart-toggle {
            flex-direction: column;
            width: 100%;
        }
        .chart-toggle button {
            width: 100%;
            min-height: 48px;
            font-size: 0.85rem;
        }
        .lift-btn {
            min-height: 44px;
            display: inline-flex;
            align-items: center;
        }
        .pr-card { padding: 16px 16px; }
        .chart-card { padding: 16px 14px 18px; }
    }
</style>
@endpush

@section('content')
<div class="prog-page">
    <h1 class="prog-title">Lift Progress</h1>

    @if ($lifts->isEmpty())
        <div class="empty-state">
            Belum ada data lift. Catat workout di <a href="{{ route('log') }}">Log Workout</a> dulu.
        </div>
    @else
        <div class="lift-selector-wrap">
            <div class="lift-selector">
                @foreach ($lifts as $name)
                    <a
                        href="{{ route('progress') }}?lift={{ rawurlencode($name) }}"
                        class="lift-btn {{ $name === $selectedLift ? 'active' : '' }}"
                    >{{ $name }}</a>
                @endforeach
            </div>
        </div>

        @if ($selectedLift && $liftHistory->isNotEmpty())
            <div class="pr-grid">
                <div class="pr-card">
                    <div class="pr-label">Best Weight</div>
                    <div class="pr-value">{{ $prWeight !== null ? number_format($prWeight, 1, ',', '.') . ' kg' : '—' }}</div>
                </div>
                <div class="pr-card">
                    <div class="pr-label">Last Session</div>
                    <div class="pr-value">{{ $lastWeight !== null ? number_format($lastWeight, 1, ',', '.') . ' kg' : '—' }}</div>
                    @if ($weightChange !== null)
                        <div class="pr-delta {{ $weightChange > 0 ? 'up' : ($weightChange < 0 ? 'down' : 'neutral') }}">
                            @if ($weightChange > 0)+@endif{{ number_format($weightChange, 1, ',', '.') }} kg vs sebelumnya
                        </div>
                    @else
                        <div class="pr-delta neutral">—</div>
                    @endif
                </div>
                <div class="pr-card">
                    <div class="pr-label">Reps Change</div>
                    @if ($repsChange !== null && $liftHistory->count() >= 2)
                        @php $rcClass = $repsChange > 0 ? 'up' : ($repsChange < 0 ? 'down' : 'neutral'); @endphp
                        <div class="pr-delta {{ $rcClass }}" style="font-size:1.35rem;font-weight:700;">
                            @if ($repsChange > 0)+@endif{{ $repsChange }} <span style="font-size:0.75rem;font-weight:600;opacity:.85;">reps</span>
                        </div>
                        <div class="pr-delta neutral" style="margin-top:6px;">vs sesi sebelumnya</div>
                    @else
                        <div class="pr-value">—</div>
                        <div class="pr-delta neutral">Butuh ≥2 sesi</div>
                    @endif
                    @if ($lastReps !== null)
                        <div class="pr-delta neutral" style="margin-top:10px;">Terakhir: {{ $lastReps }} reps</div>
                    @endif
                </div>
            </div>

            <div class="chart-card">
                <div class="pr-label" style="margin-bottom:4px;">{{ $selectedLift }}</div>
                <div class="chart-wrap-prog">
                    <canvas id="liftLineChart"></canvas>
                </div>
                <div class="chart-toggle" id="metricToggle" role="group" aria-label="Metrik chart">
                    <button type="button" class="on" data-metric="weight">Weight</button>
                    <button type="button" data-metric="reps">Reps</button>
                    <button type="button" data-metric="sets">Sets</button>
                </div>
            </div>
        @else
            <div class="empty-state">Pilih lift di atas atau tambahkan data untuk lift ini.</div>
        @endif
    @endif
</div>
@endsection

@if (!$lifts->isEmpty() && $selectedLift && $liftHistory->isNotEmpty())
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const liftHistory = @json($liftHistory);
    const labels = liftHistory.map(function (h) {
        const p = h.date.split('-');
        return p[2] + '/' + p[1] + '/' + p[0].slice(2);
    });

    function series(metric) {
        return liftHistory.map(function (h) {
            if (metric === 'weight') return h.weight;
            if (metric === 'reps') return h.reps;
            return h.sets;
        });
    }

    let metric = 'weight';
    const ctx = document.getElementById('liftLineChart');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Weight (kg)',
                data: series('weight'),
                borderColor: '#f5c518',
                backgroundColor: 'rgba(245, 197, 24, 0.12)',
                fill: true,
                tension: 0.25,
                spanGaps: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#f5c518',
                pointBorderColor: '#111',
                pointBorderWidth: 1,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: function (items) {
                            const i = items[0].dataIndex;
                            return liftHistory[i].date;
                        },
                        label: function (item) {
                            const i = item.dataIndex;
                            const h = liftHistory[i];
                            return [
                                'Weight: ' + (h.weight != null ? h.weight + ' kg' : '—'),
                                'Reps: ' + h.reps,
                                'Sets: ' + h.sets,
                            ];
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: { color: 'rgba(128,128,128,.12)' },
                    ticks: { color: '#888', maxRotation: 45, font: { size: 10 } },
                },
                y: {
                    beginAtZero: metric !== 'weight',
                    grid: { color: 'rgba(128,128,128,.12)' },
                    ticks: { color: '#888' },
                },
            },
        },
    });

    function labelFor(m) {
        if (m === 'weight') return 'Weight (kg)';
        if (m === 'reps') return 'Reps';
        return 'Sets';
    }

    document.getElementById('metricToggle').addEventListener('click', function (e) {
        const btn = e.target.closest('button[data-metric]');
        if (!btn) return;
        metric = btn.getAttribute('data-metric');
        document.querySelectorAll('#metricToggle button').forEach(function (b) {
            b.classList.toggle('on', b.getAttribute('data-metric') === metric);
        });
        chart.data.datasets[0].data = series(metric);
        chart.data.datasets[0].label = labelFor(metric);
        chart.options.scales.y.beginAtZero = metric !== 'weight';
        chart.update();
    });
})();
</script>
@endpush
@endif
