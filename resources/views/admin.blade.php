@extends('layouts.app')

@section('title', 'Admin Panel')

@push('styles')
<style>
    .admin-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        gap: 12px;
        flex-wrap: wrap;
    }

    .admin-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .admin-header h1 {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text);
    }

    .badge-admin-label {
        background-color: var(--yellow);
        color: #111;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        letter-spacing: 0.8px;
        text-transform: uppercase;
    }

    .btn-create-user {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: var(--yellow);
        color: #111;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem;
        font-weight: 700;
        padding: 9px 18px;
        border-radius: 8px;
        text-decoration: none;
        transition: background-color 0.15s;
        white-space: nowrap;
    }

    .btn-create-user:hover { background-color: #d4a900; }

    /* Summary */
    .summary-bar {
        display: flex;
        gap: 16px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .summary-item {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 14px 20px;
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 120px;
    }

    .summary-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--yellow);
    }

    .summary-label {
        font-size: 0.78rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Table */
    .table-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
    }

    .table-wrap { overflow-x: auto; }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    thead { background-color: var(--border); }

    thead th {
        padding: 13px 16px;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        color: var(--text-muted);
        white-space: nowrap;
    }

    tbody tr.user-row {
        border-top: 1px solid var(--border);
        transition: background-color 0.1s;
    }

    tbody tr.user-row:hover { background-color: rgba(255,255,255,0.02); }
    html.light tbody tr.user-row:hover { background-color: rgba(0,0,0,0.02); }

    tr.expand-row td { padding: 0; border-top: none; }

    .expand-panel {
        display: none;
        background-color: var(--bg);
        border-top: 1px dashed var(--border);
        border-bottom: 1px solid var(--border);
        padding: 20px 16px;
    }

    html.light .expand-panel { background-color: #f8f8f8; }
    .expand-panel.open { display: block; }

    td {
        padding: 14px 16px;
        vertical-align: middle;
        color: var(--text);
    }

    .td-name { font-weight: 600; }
    .td-username { color: var(--text-muted); font-size: 0.85rem; }

    .badge-status {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 11px;
        border-radius: 20px;
        white-space: nowrap;
    }

    .badge-aktif    { background-color: rgba(34,197,94,0.15);  color: #4ade80; border: 1px solid rgba(34,197,94,0.3); }
    .badge-nonaktif { background-color: rgba(239,68,68,0.12);  color: #f87171; border: 1px solid rgba(239,68,68,0.3); }

    .expired-text  { color: #f87171; font-size: 0.85rem; font-weight: 500; }
    .no-limit-text { color: var(--text-muted); font-size: 0.85rem; }
    .expires-ok    { font-size: 0.85rem; color: var(--text); }
    .expires-soon  { color: #facc15; font-size: 0.85rem; font-weight: 500; }
    .sesi-count    { font-weight: 700; color: var(--yellow); }

    .action-col { min-width: 260px; }

    .action-wrap { display: flex; flex-direction: column; gap: 8px; }

    .action-row {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .btn-toggle {
        border: none;
        border-radius: 7px;
        padding: 7px 14px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.15s;
        white-space: nowrap;
    }

    .btn-toggle:hover { opacity: 0.8; }
    .btn-activate   { background-color: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.3); }
    .btn-deactivate { background-color: rgba(239,68,68,0.12); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }

    .input-days {
        width: 64px;
        background-color: var(--bg);
        border: 1px solid var(--border);
        border-radius: 7px;
        padding: 7px 8px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.85rem;
        color: var(--text);
        outline: none;
        transition: border-color 0.15s;
        text-align: center;
    }

    .input-days:focus { border-color: var(--yellow); }
    .label-hari { font-size: 0.8rem; color: var(--text-muted); white-space: nowrap; }

    .btn-tambah {
        background-color: var(--yellow);
        color: #111;
        border: none;
        border-radius: 7px;
        padding: 7px 13px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
        transition: background-color 0.15s;
    }

    .btn-tambah:hover { background-color: #d4a900; }

    .btn-reset-access {
        background-color: transparent;
        color: var(--text-muted);
        border: 1px solid var(--border);
        border-radius: 7px;
        padding: 7px 13px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: border-color 0.15s, color 0.15s;
    }

    .btn-reset-access:hover { border-color: var(--text-muted); color: var(--text); }

    .action-divider { border: none; border-top: 1px solid var(--border); margin: 2px 0; }

    .btn-kelola {
        background: none;
        border: 1px solid var(--border);
        border-radius: 7px;
        padding: 6px 12px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        transition: border-color 0.15s, color 0.15s, background-color 0.15s;
        white-space: nowrap;
    }

    .btn-kelola:hover,
    .btn-kelola.active {
        border-color: var(--yellow);
        color: var(--yellow);
        background-color: rgba(245,197,24,0.06);
    }

    .btn-progress {
        display: inline-flex;
        align-items: center;
        border: 1px solid rgba(96, 165, 250, 0.35);
        border-radius: 7px;
        padding: 6px 12px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8rem;
        font-weight: 600;
        color: #60a5fa;
        background: rgba(96, 165, 250, 0.08);
        text-decoration: none;
        white-space: nowrap;
        transition: background 0.15s, border-color 0.15s;
    }

    .btn-progress:hover {
        background: rgba(96, 165, 250, 0.16);
        border-color: rgba(96, 165, 250, 0.55);
    }

    .panel-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    @media (max-width: 700px) { .panel-grid { grid-template-columns: 1fr; } }

    .panel-section {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 16px;
    }

    .panel-section-title {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--text-muted);
        margin-bottom: 12px;
    }

    .panel-input {
        width: 100%;
        background-color: var(--bg);
        border: 1px solid var(--border);
        border-radius: 7px;
        padding: 9px 12px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem;
        color: var(--text);
        outline: none;
        transition: border-color 0.15s;
        margin-bottom: 8px;
    }

    .panel-input:focus { border-color: var(--yellow); }

    .panel-input-wrap { position: relative; margin-bottom: 8px; }
    .panel-input-wrap .panel-input { margin-bottom: 0; padding-right: 38px; }

    .panel-toggle-pw {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        padding: 0;
        transition: color 0.15s;
    }

    .panel-toggle-pw:hover { color: var(--text); }

    .btn-panel-save {
        width: 100%;
        background-color: var(--yellow);
        color: #111;
        border: none;
        border-radius: 7px;
        padding: 9px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.15s;
        margin-top: 4px;
    }

    .btn-panel-save:hover { background-color: #d4a900; }

    .btn-delete-avatar {
        width: 100%;
        background-color: transparent;
        color: #f87171;
        border: 1px solid rgba(239,68,68,0.35);
        border-radius: 7px;
        padding: 9px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.15s;
        margin-top: 8px;
    }

    .btn-delete-avatar:hover { background-color: rgba(239,68,68,0.08); }

    .btn-delete-user {
        width: 100%;
        background-color: #dc2626;
        color: #ffffff;
        border: none;
        border-radius: 7px;
        padding: 9px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.15s;
        margin-top: 4px;
    }

    .btn-delete-user:hover { background-color: #b91c1c; }

    .no-avatar-text { font-size: 0.82rem; color: var(--text-muted); font-style: italic; }

    .empty-state {
        text-align: center;
        padding: 48px 20px;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .admin-header {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-create-user {
            justify-content: center;
            min-height: 48px;
        }

        .summary-bar {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .summary-item {
            min-width: 0;
            padding: 12px 14px;
        }

        .table-wrap table thead {
            display: none;
        }

        .table-wrap table tbody tr.user-row {
            display: block;
            border: 1px solid var(--border);
            border-bottom: none;
            border-radius: 14px 14px 0 0;
            margin-bottom: 0;
            background: var(--bg-card);
        }

        .table-wrap table tbody tr.user-row td {
            display: block;
            width: 100%;
            padding: 12px 16px;
            border: none;
            border-top: 1px solid var(--border);
            text-align: left;
        }

        .table-wrap table tbody tr.user-row td:first-child {
            border-top: none;
        }

        .table-wrap table tbody tr.user-row td::before {
            content: attr(data-label);
            display: block;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .table-wrap table tbody tr.expand-row {
            display: block;
            border: 1px solid var(--border);
            border-top: none;
            border-radius: 0 0 14px 14px;
            margin-bottom: 16px;
            background: var(--bg-card);
        }

        .table-wrap table tbody tr.expand-row td {
            display: block;
            width: 100%;
            padding: 0;
            border: none;
        }

        .table-wrap .action-col {
            min-width: 0;
        }

        .btn-toggle,
        .btn-tambah,
        .btn-reset-access,
        .btn-kelola,
        .btn-progress {
            min-height: 44px;
            padding: 10px 14px;
        }

        .input-days {
            min-height: 44px;
            width: 72px;
        }

        .panel-grid {
            grid-template-columns: 1fr;
        }

        .table-wrap table tbody tr:not(.expand-row) td[colspan] {
            display: block;
            padding: 24px 16px;
        }

        .table-wrap table tbody tr:not(.expand-row) td[colspan]::before {
            display: none;
        }

        .table-wrap table tbody tr.expand-row td[colspan] {
            padding: 0 !important;
        }

        .btn-panel-save,
        .btn-delete-avatar,
        .btn-delete-user {
            min-height: 48px;
        }

        .panel-input {
            min-height: 44px;
        }
    }
</style>
@endpush

@section('content')

<div class="admin-header">
    <div class="admin-header-left">
        <h1>Admin Panel</h1>
        <span class="badge-admin-label">Admin</span>
    </div>
    <a href="{{ route('admin.create-user') }}" class="btn-create-user">
        + Buat User Baru
    </a>
</div>

<div class="summary-bar">
    <div class="summary-item">
        <div class="summary-value">{{ $users->count() }}</div>
        <div class="summary-label">Total Member</div>
    </div>
    <div class="summary-item">
        <div class="summary-value">{{ $users->where('is_active', true)->count() }}</div>
        <div class="summary-label">Aktif</div>
    </div>
    <div class="summary-item">
        <div class="summary-value">{{ $users->where('is_active', false)->count() }}</div>
        <div class="summary-label">Nonaktif</div>
    </div>
    <div class="summary-item">
        <div class="summary-value">{{ $users->sum('workouts_count') }}</div>
        <div class="summary-label">Total Sesi</div>
    </div>
</div>

<div class="table-card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Expired</th>
                    <th>Sesi</th>
                    <th class="action-col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)

                <tr class="user-row">
                    <td data-label="Nama">
                        <div class="td-name">{{ $user->name }}</div>
                        <div class="td-username">{{ '@' . $user->username }}</div>
                    </td>

                    <td data-label="Status">
                        @if ($user->is_active)
                            <span class="badge-status badge-aktif">Aktif</span>
                        @else
                            <span class="badge-status badge-nonaktif">Nonaktif</span>
                        @endif
                    </td>

                    <td data-label="Expired">
                        @if ($user->expires_at === null)
                            <span class="no-limit-text">Tidak ada batas</span>
                        @elseif ($user->expires_at->isPast())
                            <span class="expired-text">Habis {{ $user->expires_at->format('d M Y') }}</span>
                        @elseif ($user->expires_at->diffInDays(now()) <= 7)
                            <span class="expires-soon">
                                {{ $user->expires_at->format('d M Y') }}
                                <span style="font-size:0.78rem;">({{ $user->expires_at->diffForHumans() }})</span>
                            </span>
                        @else
                            <span class="expires-ok">
                                {{ $user->expires_at->format('d M Y') }}
                                <span style="color:var(--text-muted);font-size:0.78rem;">({{ $user->expires_at->diffForHumans() }})</span>
                            </span>
                        @endif
                    </td>

                    <td data-label="Sesi"><span class="sesi-count">{{ $user->workouts_count }}</span></td>

                    <td data-label="Aksi">
                        <div class="action-wrap">
                            <div class="action-row">
                                <form id="form-toggle-{{ $user->id }}"
                                      action="{{ route('admin.toggle', $user) }}"
                                      method="POST" style="display:none;">
                                    @csrf
                                </form>

                                @if ($user->is_active)
                                    <button type="button" class="btn-toggle btn-deactivate"
                                        data-username="{{ $user->username }}"
                                        onclick="confirmAction(
                                            'Nonaktifkan akun @' + this.dataset.username + '?',
                                            'form-toggle-{{ $user->id }}',
                                            { okLabel: 'Ya, Nonaktifkan' }
                                        )">
                                        Nonaktifkan
                                    </button>
                                @else
                                    <button type="button" class="btn-toggle btn-activate"
                                        onclick="document.getElementById('form-toggle-{{ $user->id }}').submit()">
                                        Aktifkan
                                    </button>
                                @endif

                                <button type="button" class="btn-kelola"
                                    id="kelola-btn-{{ $user->id }}"
                                    onclick="togglePanel({{ $user->id }})">
                                    Kelola Akun
                                </button>

                                <a href="{{ route('admin.user.progress', $user) }}" class="btn-progress">Progress</a>
                            </div>

                            <hr class="action-divider">

                            <div class="action-row">
                                <input type="number" class="input-days"
                                    id="days-{{ $user->id }}"
                                    min="1" max="365" placeholder="30">
                                <span class="label-hari">hari</span>

                                <form id="form-add-{{ $user->id }}"
                                      action="{{ route('admin.add-access', $user) }}"
                                      method="POST" style="display:none;">
                                    @csrf
                                    <input type="hidden" name="days" value="">
                                </form>
                                <button type="button" class="btn-tambah"
                                    data-username="{{ $user->username }}"
                                    onclick="injectAndConfirm('days-{{ $user->id }}', 'form-add-{{ $user->id }}', this.dataset.username, 'add')">
                                    Tambah
                                </button>

                                <form id="form-reset-{{ $user->id }}"
                                      action="{{ route('admin.reset-access', $user) }}"
                                      method="POST" style="display:none;">
                                    @csrf
                                    <input type="hidden" name="days" value="">
                                </form>
                                <button type="button" class="btn-reset-access"
                                    data-username="{{ $user->username }}"
                                    onclick="injectAndConfirm('days-{{ $user->id }}', 'form-reset-{{ $user->id }}', this.dataset.username, 'reset')">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr class="expand-row">
                    <td colspan="5">
                        <div class="expand-panel" id="panel-{{ $user->id }}">
                            <div class="panel-grid">

                                <div class="panel-section">
                                    <div class="panel-section-title">Ubah Nama</div>
                                    <form action="{{ route('admin.update-name', $user) }}" method="POST">
                                        @csrf
                                        <input type="text" name="name" class="panel-input"
                                               value="{{ $user->name }}" placeholder="Nama baru" maxlength="50">
                                        <button type="submit" class="btn-panel-save">Simpan Nama</button>
                                    </form>
                                </div>

                                <div class="panel-section">
                                    <div class="panel-section-title">Reset Password</div>
                                    <form id="form-resetpw-{{ $user->id }}"
                                          action="{{ route('admin.reset-password', $user) }}"
                                          method="POST">
                                        @csrf
                                        <div class="panel-input-wrap">
                                            <input type="password" name="new_password"
                                                   class="panel-input"
                                                   placeholder="Password baru (min 6)"
                                                   autocomplete="new-password">
                                            <button type="button" class="panel-toggle-pw"
                                                    onclick="togglePanelPw(this)" tabindex="-1">
                                                <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                <svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                            </button>
                                        </div>
                                        <div class="panel-input-wrap">
                                            <input type="password" name="new_password_confirmation"
                                                   class="panel-input"
                                                   placeholder="Konfirmasi password"
                                                   autocomplete="new-password">
                                            <button type="button" class="panel-toggle-pw"
                                                    onclick="togglePanelPw(this)" tabindex="-1">
                                                <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                <svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                            </button>
                                        </div>
                                        <button type="button" class="btn-panel-save"
                                            data-username="{{ $user->username }}"
                                            onclick="confirmAction(
                                                'Reset password akun @' + this.dataset.username + '?',
                                                'form-resetpw-{{ $user->id }}',
                                                { okLabel: 'Ya, Reset' }
                                            )">
                                            Reset Password
                                        </button>
                                    </form>
                                </div>

                                <div class="panel-section">
                                    <div class="panel-section-title">Foto Profil</div>
                                    @if ($user->avatar)
                                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                                            <img src="{{ asset('storage/' . $user->avatar) }}"
                                                 alt="Avatar"
                                                 style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid var(--yellow);">
                                            <span style="font-size:0.82rem;color:var(--text-muted);">Punya foto profil</span>
                                        </div>
                                        <form id="form-delavatar-{{ $user->id }}"
                                              action="{{ route('admin.delete-avatar', $user) }}"
                                              method="POST" style="display:none;">
                                            @csrf
                                        </form>
                                        <button type="button" class="btn-delete-avatar"
                                            data-username="{{ $user->username }}"
                                            onclick="confirmAction(
                                                'Hapus foto profil @' + this.dataset.username + '?',
                                                'form-delavatar-{{ $user->id }}',
                                                { danger: true, okLabel: 'Ya, Hapus' }
                                            )">
                                            Hapus Foto Profil
                                        </button>
                                    @else
                                        <p class="no-avatar-text">User ini belum punya foto profil.</p>
                                    @endif
                                </div>

                                <div class="panel-section">
                                    <div class="panel-section-title">Zona Berbahaya</div>
                                    <form id="form-delete-user-{{ $user->id }}"
                                          action="{{ route('admin.delete', $user) }}"
                                          method="POST"
                                          style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button"
                                        class="btn-delete-user"
                                        data-username="{{ $user->username }}"
                                        onclick="confirmAction(
                                            'Hapus akun @' + this.dataset.username + '? Semua data workout akan ikut terhapus permanen.',
                                            'form-delete-user-{{ $user->id }}',
                                            { danger: true, okLabel: 'Ya, Hapus Akun' }
                                        )">
                                        Hapus Akun
                                    </button>
                                </div>

                            </div>
                        </div>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">Belum ada member terdaftar.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function togglePanel(userId) {
        const panel  = document.getElementById('panel-' + userId);
        const btn    = document.getElementById('kelola-btn-' + userId);
        const isOpen = panel.classList.contains('open');

        document.querySelectorAll('.expand-panel').forEach(p => p.classList.remove('open'));
        document.querySelectorAll('.btn-kelola').forEach(b => {
            b.classList.remove('active');
            b.textContent = 'Kelola Akun';
        });

        if (!isOpen) {
            panel.classList.add('open');
            btn.classList.add('active');
            btn.textContent = 'Tutup';
        }
    }

    function injectAndConfirm(inputId, formId, username, action) {
        const days = parseInt(document.getElementById(inputId).value, 10);
        if (!days || days < 1) {
            showToast('Masukkan jumlah hari terlebih dahulu (minimal 1).', 'error');
            return;
        }
        document.getElementById(formId).querySelector('input[name="days"]').value = days;

        if (action === 'add') {
            confirmAction(
                'Tambah ' + days + ' hari akses untuk @' + username + '?',
                formId,
                { okLabel: 'Ya, Tambah' }
            );
        } else {
            confirmAction(
                'Reset akses @' + username + '? Waktu akses akan direset.',
                formId,
                { danger: true, okLabel: 'Ya, Reset' }
            );
        }
    }

    function togglePanelPw(btn) {
        const wrap      = btn.closest('.panel-input-wrap');
        const input     = wrap.querySelector('input');
        const eyeOpen   = btn.querySelector('.eye-open');
        const eyeClosed = btn.querySelector('.eye-closed');
        if (input.type === 'password') {
            input.type              = 'text';
            eyeOpen.style.display   = 'none';
            eyeClosed.style.display = 'block';
        } else {
            input.type              = 'password';
            eyeOpen.style.display   = 'block';
            eyeClosed.style.display = 'none';
        }
    }
</script>
@endpush