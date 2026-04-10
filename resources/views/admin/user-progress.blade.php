@extends('layouts.app')

@section('title', 'Dashboard — ' . $member->name)

@push('styles')
<style>
    .prog-page { max-width: 900px; margin: 0 auto; }

    .prog-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text-muted);
        text-decoration: none;
        margin-bottom: 18px;
        transition: color 0.15s;
    }
    .prog-back:hover { color: var(--text); }
    .prog-back svg { flex-shrink: 0; }

    .prog-user-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 22px;
        padding: 16px 20px;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
    }
    .prog-user-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid var(--yellow);
        background: var(--border);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .prog-user-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .prog-user-avatar svg { width: 22px; height: 22px; color: var(--text-muted); }
    .prog-user-name { font-size: 1rem; font-weight: 700; color: var(--text); }
    .prog-user-username { font-size: 0.82rem; color: var(--text-muted); margin-top: 2px; }
    .prog-admin-badge {
        margin-left: auto;
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--yellow);
        background: rgba(245,197,24,0.1);
        border: 1px solid rgba(245,197,24,0.3);
        padding: 4px 10px;
        border-radius: 6px;
        white-space: nowrap;
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
        letter-spacing: 0.4px;
    }
    .split-badge.push   { background: #f5c518; color: #111; }
    .split-badge.pull   { background: #60a5fa; color: #0f0f0f; }
    .split-badge.legs   { background: #a78bfa; color: #0f0f0f; }
    .split-badge.full   { background: #4ade80; color: #111; }
    .split-badge.upper  { background: #fb923c; color: #111; }
    .split-badge.lower  { background: #f87171; color: #111; }
    .split-badge.cardio { background: #2dd4bf; color: #111; }
    .split-badge.custom { background: #555;    color: #eee; }

    .session-date { font-size: 0.9rem; font-weight: 600; color: var(--text); }

    .ex-row {
        font-size: 0.82rem;
        color: var(--text-muted);
        line-height: 1.5;
        padding: 3px 0;
    }
    .ex-row + .ex-row {
        border-top: 1px solid var(--border);
        padding-top: 5px;
        margin-top: 2px;
    }
    .ex-name { font-weight: 600; color: var(--text); }

    .cardio-row {
        font-size: 0.82rem;
        color: #2dd4bf;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid rgba(45,212,191,0.2);
    }

    .session-vol {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        min-width: 72px;
    }
    .session-vol-label {
        font-size: 0.68rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .session-vol-val {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--yellow);
        margin-top: 4px;
    }

    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: var(--text-muted);
        font-size: 0.9rem;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
    }

    @media (max-width: 768px) {
        .prog-page { padding: 0 2px; }
        .prog-user-header { flex-wrap: wrap; }
        .prog-admin-badge { margin-left: 0; }
        .session-card { grid-template-columns: 1fr; }
        .session-vol { align-items: flex-start; }
    }
</style>
@endpush

@section('content')
<div class="prog-page">

    <a href="{{ route('admin') }}" class="prog-back">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Admin Panel
    </a>

    <div class="prog-user-header">
        <div class="prog-user-avatar">
            @if ($member->avatar)
                <img src="{{ asset('storage/' . $member->avatar) }}" alt="Avatar">
            @else
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 2a5 5 0 1 1 0 10A5 5 0 0 1 12 2zm0 12c-5.33 0-8 2.67-8 4v1h16v-1c0-1.33-2.67-4-8-4z" clip-rule="evenodd"/>
                </svg>
            @endif
        </div>
        <div>
            <div class="prog-user-name">{{ $member->name }}</div>
            <div class="prog-user-username">{{ $member->username }}</div>
        </div>
        <span class="prog-admin-badge">Lihat sebagai Admin</span>
    </div>

    <div class="section-title">Recent Workouts</div>

    <div class="recent-list">
        @forelse ($recentWorkouts as $w)
            @php
                $knownSplits = ['push','pull','legs','full','upper','lower','cardio'];
                $sk = strtolower($w->split);
                $badgeClass = in_array($sk, $knownSplits) ? $sk : 'custom';
                $vol = $w->exercises->sum(function ($e) {
                    return (int) $e->sets * (int) $e->reps * ($e->weight !== null ? (float) $e->weight : 0);
                });
            @endphp
            <div class="session-card">
                <div>
                    <div class="session-top">
                        <span class="split-badge {{ $badgeClass }}">{{ $w->split }}</span>
                        <span class="session-date">{{ $w->date->format('j M Y') }}</span>
                    </div>

                    @foreach ($w->exercises as $ex)
                        <div class="ex-row">
                            <span class="ex-name">{{ $ex->name }}</span>
                            · {{ $ex->sets }}&times;{{ $ex->reps }}
                            @ {{ $ex->weight !== null ? $ex->weight : '—' }} kg
                        </div>
                    @endforeach

                    @if ($w->cardioLog)
                        <div class="cardio-row">
                            🏃 {{ $w->cardioLog->duration_minutes }} min
                            @if ($w->cardioLog->speed) · {{ $w->cardioLog->speed }} km/h @endif
                            @if ($w->cardioLog->incline) · incline {{ $w->cardioLog->incline }} @endif
                        </div>
                    @endif
                </div>

                <div class="session-vol">
                    <div class="session-vol-label">Volume</div>
                    <div class="session-vol-val">{{ number_format($vol, 0, ',', '.') }}</div>
                </div>
            </div>
        @empty
            <div class="empty-state">User ini belum punya data workout.</div>
        @endforelse
    </div>

</div>
@endsection
