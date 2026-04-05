@extends('layouts.app')

@section('title', 'Profile')

@push('styles')
<style>
    .profile-wrap {
        max-width: 480px;
        margin: 0 auto;
    }

    .page-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 20px;
    }

    .card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 32px 28px;
    }

    .avatar-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        margin-bottom: 24px;
    }

    .avatar-circle {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid var(--yellow);
        background-color: var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .avatar-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .avatar-circle .avatar-placeholder {
        width: 48px;
        height: 48px;
        color: var(--text-muted);
    }

    .user-name     { font-size: 1.15rem; font-weight: 700; color: var(--text); margin-top: 4px; }
    .user-username { font-size: 0.875rem; color: var(--text-muted); }

    .badge-role {
        display: inline-block;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    .badge-admin { background-color: var(--yellow); color: #111; }
    .badge-user  { background-color: var(--border); color: var(--text-muted); }

    .upload-form {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        width: 100%;
    }

    .btn-choose-file {
        display: inline-block;
        background: none;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 7px 18px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--text-muted);
        cursor: pointer;
        transition: border-color 0.15s, color 0.15s;
    }

    .btn-choose-file:hover { border-color: var(--yellow); color: var(--text); }

    #avatarFileInput { display: none; }

    .file-chosen {
        font-size: 0.78rem;
        color: var(--text-muted);
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        text-align: center;
    }

    .btn-upload {
        background-color: var(--yellow);
        color: #111;
        border: none;
        border-radius: 8px;
        padding: 8px 22px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.15s;
    }

    .btn-upload:hover { background-color: #d4a900; }

    .field-error-small { font-size: 0.78rem; color: #f87171; text-align: center; }

    .divider {
        border: none;
        border-top: 1px solid var(--border);
        margin: 24px 0;
    }

    .section-label {
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 16px;
    }

    .form-group { margin-bottom: 16px; }

    .form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 6px;
    }

    .form-hint { font-size: 0.78rem; color: var(--text-muted); margin-top: 5px; }

    .input-wrap { position: relative; }

    .form-group input[type="text"],
    .form-group input[type="password"] {
        width: 100%;
        background-color: var(--bg);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 10px 13px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
        color: var(--text);
        outline: none;
        transition: border-color 0.15s;
    }

    .form-group input[type="password"] { padding-right: 40px; }
    .form-group input:focus { border-color: var(--yellow); }

    .toggle-pw {
        position: absolute;
        right: 11px;
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

    .toggle-pw:hover { color: var(--text); }

    .field-error { font-size: 0.8rem; color: #f87171; margin-top: 5px; }

    .btn-save {
        width: 100%;
        background-color: var(--yellow);
        color: #111;
        border: none;
        border-radius: 8px;
        padding: 11px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.15s;
        margin-top: 6px;
    }

    .btn-save:hover { background-color: #d4a900; }

    .alert-error {
        background-color: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 8px;
        padding: 11px 15px;
        font-size: 0.875rem;
        color: #f87171;
        margin-bottom: 16px;
    }

    @media (max-width: 768px) {
        .profile-wrap {
            max-width: 100%;
        }

        .page-title {
            font-size: 1.3rem;
        }

        .card {
            padding: 20px 16px;
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            min-height: 44px;
        }

        .toggle-pw {
            min-width: 44px;
            min-height: 44px;
            justify-content: center;
        }

        .btn-save,
        .btn-upload {
            min-height: 48px;
        }

        .btn-choose-file {
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="profile-wrap">

    <h1 class="page-title">Profile</h1>

    <div class="card">

        {{-- Avatar + Info --}}
        <div class="avatar-section">
            <div class="avatar-circle">
                @if (auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar">
                @else
                    <svg class="avatar-placeholder" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M12 2a5 5 0 1 1 0 10A5 5 0 0 1 12 2zm0 12c-5.33 0-8 2.67-8 4v1h16v-1c0-1.33-2.67-4-8-4z" clip-rule="evenodd"/>
                    </svg>
                @endif
            </div>

            <div class="user-name">{{ auth()->user()->name }}</div>
            <div class="user-username">{{ '@' . auth()->user()->username }}</div>

            @if (auth()->user()->role === 'admin')
                <span class="badge-role badge-admin">Admin</span>
            @else
                <span class="badge-role badge-user">User</span>
            @endif

            <form class="upload-form" action="{{ route('profile.avatar') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" id="avatarFileInput" name="avatar"
                       accept="image/*" onchange="onFileChosen(this)">
                <label for="avatarFileInput" class="btn-choose-file">Pilih Foto Baru</label>
                <div class="file-chosen" id="fileChosenLabel">Belum ada foto dipilih</div>
                @if ($errors->has('avatar'))
                    <div class="field-error-small">{{ $errors->first('avatar') }}</div>
                @endif
                <button type="submit" class="btn-upload">Upload</button>
            </form>
        </div>

        <hr class="divider">

        {{-- Ubah Nama --}}
        <div class="section-label">Ubah Nama</div>
        <form action="{{ route('profile.name') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nameInput">Nama Lengkap</label>
                <input type="text" id="nameInput" name="name"
                       value="{{ old('name', auth()->user()->name) }}"
                       placeholder="Masukkan nama lengkap" maxlength="50">
                <div class="form-hint">Username: {{ '@' . auth()->user()->username }} (tidak bisa diubah)</div>
                @if ($errors->has('name'))
                    <div class="field-error">{{ $errors->first('name') }}</div>
                @endif
            </div>
            <button type="submit" class="btn-save">Simpan Nama</button>
        </form>

        <hr class="divider">

        {{-- Ganti Password --}}
        <div class="section-label">Ganti Password</div>

        @if ($errors->has('current_password'))
            <div class="alert-error">{{ $errors->first('current_password') }}</div>
        @endif

        <form action="{{ route('profile.password') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Password Lama</label>
                <div class="input-wrap">
                    <input type="password" name="current_password"
                           placeholder="Masukkan password lama"
                           autocomplete="current-password">
                    <button type="button" class="toggle-pw" onclick="togglePw(this)" tabindex="-1">
                        <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label>Password Baru</label>
                <div class="input-wrap">
                    <input type="password" name="new_password"
                           placeholder="Minimal 6 karakter"
                           autocomplete="new-password">
                    <button type="button" class="toggle-pw" onclick="togglePw(this)" tabindex="-1">
                        <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                @if ($errors->has('new_password'))
                    <div class="field-error">{{ $errors->first('new_password') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label>Konfirmasi Password Baru</label>
                <div class="input-wrap">
                    <input type="password" name="new_password_confirmation"
                           placeholder="Ulangi password baru"
                           autocomplete="new-password">
                    <button type="button" class="toggle-pw" onclick="togglePw(this)" tabindex="-1">
                        <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-save">Simpan Password</button>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function onFileChosen(input) {
        document.getElementById('fileChosenLabel').textContent =
            input.files.length > 0 ? input.files[0].name : 'Belum ada foto dipilih';
    }

    function togglePw(btn) {
        const input     = btn.closest('.input-wrap').querySelector('input');
        const eyeOpen   = btn.querySelector('.eye-open');
        const eyeClosed = btn.querySelector('.eye-closed');
        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.style.display   = 'none';
            eyeClosed.style.display = 'block';
        } else {
            input.type = 'password';
            eyeOpen.style.display   = 'block';
            eyeClosed.style.display = 'none';
        }
    }
</script>
@endpush