@extends('layouts.app')

@section('title', 'Buat Akun Baru')

@push('styles')
    <style>
        .page-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 7px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
            text-decoration: none;
            transition: border-color 0.15s, color 0.15s;
        }

        .btn-back:hover {
            border-color: var(--text-muted);
            color: var(--text);
        }

        .page-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text);
        }

        .form-wrap {
            max-width: 480px;
            margin: 0 auto;
        }

        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 32px 28px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 7px;
        }

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

        .form-group input[type="password"] {
            padding-right: 42px;
        }

        .form-group input:focus {
            border-color: var(--yellow);
        }

        .form-group input.is-error {
            border-color: #ef4444;
        }

        .input-wrap {
            position: relative;
        }

        .toggle-pw {
            position: absolute;
            right: 12px;
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

        .toggle-pw:hover {
            color: var(--text);
        }

        .field-hint {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 5px;
        }

        .field-error {
            font-size: 0.8rem;
            color: #f87171;
            margin-top: 5px;
        }

        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 24px 0;
        }

        /* Role options */
        .role-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .role-option {
            position: relative;
        }

        .role-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .role-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 14px 12px;
            background-color: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            transition: border-color 0.15s, background-color 0.15s;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 0;
        }

        .role-option label .role-icon {
            font-size: 1.4rem;
        }

        .role-option input[type="radio"]:checked+label {
            border-color: var(--yellow);
            background-color: rgba(245, 197, 24, 0.06);
            color: var(--text);
        }

        .role-option label:hover {
            border-color: var(--yellow);
            color: var(--text);
        }

        .btn-row {
            display: flex;
            gap: 10px;
            margin-top: 8px;
        }

        .btn-submit {
            flex: 1;
            background-color: var(--yellow);
            color: #111;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.15s;
        }

        .btn-submit:hover {
            background-color: #d4a900;
        }

        .btn-cancel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            transition: border-color 0.15s, color 0.15s;
        }

        .btn-cancel:hover {
            border-color: var(--text-muted);
            color: var(--text);
        }

        @media (max-width: 768px) {
            .form-wrap {
                max-width: 100%;
            }

            .card {
                padding: 24px 16px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 20px;
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

            .btn-submit,
            .btn-cancel {
                min-height: 48px;
            }

            .btn-back {
                min-height: 44px;
            }

            .role-option label {
                min-height: 48px;
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')

    <div class="page-header">
        <a href="{{ route('admin') }}" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6" />
            </svg>
            Admin Panel
        </a>
        <h1 class="page-title">Buat Akun Baru</h1>
    </div>

    <div class="form-wrap">
        <div class="card">
            <form action="{{ route('admin.store-user') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        placeholder="Contoh: Rizky Pratama" maxlength="50"
                        class="{{ $errors->has('name') ? 'is-error' : '' }}" autofocus>
                    @if ($errors->has('name'))
                        <div class="field-error">{{ $errors->first('name') }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                        placeholder="Contoh: rizky123" maxlength="30"
                        class="{{ $errors->has('username') ? 'is-error' : '' }}" autocomplete="off">
                    <div class="field-hint">Huruf, angka, strip (-), dan underscore (_) saja. Minimal 3 karakter.</div>
                    @if ($errors->has('username'))
                        <div class="field-error">{{ $errors->first('username') }}</div>
                    @endif
                </div>

                <hr class="divider">

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <input type="password" id="password" name="password" placeholder="Minimal 6 karakter"
                            class="{{ $errors->has('password') ? 'is-error' : '' }}" autocomplete="new-password">
                        <button type="button" class="toggle-pw" onclick="togglePw(this)" tabindex="-1">
                            <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                <line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                        </button>
                    </div>
                    @if ($errors->has('password'))
                        <div class="field-error">{{ $errors->first('password') }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <div class="input-wrap">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            placeholder="Ulangi password" autocomplete="new-password">
                        <button type="button" class="toggle-pw" onclick="togglePw(this)" tabindex="-1">
                            <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                <line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                        </button>
                    </div>
                </div>

                <hr class="divider">

                <div class="form-group">
                    <label>Role</label>
                    <div class="role-options">
                        <div class="role-option">
                            <input type="radio" id="role-user" name="role" value="user"
                                {{ old('role', 'user') === 'user' ? 'checked' : '' }}>
                            <label for="role-user">
                                <span class="role-icon">🏋️</span>
                                User
                            </label>
                        </div>
                        <div class="role-option">
                            <input type="radio" id="role-admin" name="role" value="admin"
                                {{ old('role') === 'admin' ? 'checked' : '' }}>
                            <label for="role-admin">
                                <span class="role-icon">⚙️</span>
                                Admin
                            </label>
                        </div>
                    </div>
                    @if ($errors->has('role'))
                        <div class="field-error" style="margin-top:8px;">{{ $errors->first('role') }}</div>
                    @endif
                </div>

                <div class="btn-row">
                    <a href="{{ route('admin') }}" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-submit">Buat Akun</button>
                </div>

            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function togglePw(btn) {
            const input = btn.closest('.input-wrap').querySelector('input');
            const eyeOpen = btn.querySelector('.eye-open');
            const eyeClosed = btn.querySelector('.eye-closed');
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
            } else {
                input.type = 'password';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
            }
        }
    </script>
@endpush
