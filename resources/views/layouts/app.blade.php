<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ALtrack — @yield('title', 'Gym Tracker')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --yellow: #f5c518;
            --yellow-dark: #d4a900;
            --bg: #0f0f0f;
            --bg-card: #1a1a1a;
            --bg-nav: #111111;
            --border: #2a2a2a;
            --text: #f0f0f0;
            --text-muted: #888888;
            --radius: 10px;
        }

        html.light {
            --bg: #f4f4f4;
            --bg-card: #ffffff;
            --bg-nav: #ffffff;
            --border: #e0e0e0;
            --text: #111111;
            --text-muted: #666666;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
            transition: background-color 0.2s, color 0.2s;
        }

        /* ── NAVBAR ── */
        .navbar {
            background-color: var(--bg-nav);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
            height: 60px;
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .navbar-logo {
            font-size: 1.25rem;
            font-weight: 700;
            color: inherit;
            text-decoration: none;
            letter-spacing: -0.5px;
            margin-right: 8px;
        }

        .navbar-logo-al {
            color: #f5c518;
            font-weight: 700;
        }

        .navbar-logo-track {
            color: var(--text);
            font-weight: 700;
        }

        .navbar-links {
            display: flex;
            align-items: center;
            gap: 4px;
            flex: 1;
        }

        .navbar-links a {
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 8px;
            transition: color 0.15s, background-color 0.15s;
        }

        .navbar-links a:hover,
        .navbar-links a.active {
            color: var(--text);
            background-color: var(--border);
        }

        .navbar-links a.active { color: var(--yellow); }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-icon {
            background: none;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 7px;
            cursor: pointer;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s, border-color 0.15s, background-color 0.15s;
        }

        .btn-icon:hover {
            color: var(--text);
            border-color: var(--text-muted);
            background-color: var(--border);
        }

        .navbar-avatar {
            display: flex;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid var(--yellow);
            background-color: var(--border);
            align-items: center;
            justify-content: center;
            text-decoration: none;
            flex-shrink: 0;
            transition: opacity 0.15s;
        }

        .navbar-avatar:hover { opacity: 0.85; }
        .navbar-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .navbar-avatar svg { width: 20px; height: 20px; color: var(--text-muted); }

        .btn-logout {
            background: none;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 6px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
            cursor: pointer;
            transition: color 0.15s, border-color 0.15s, background-color 0.15s;
        }

        .btn-logout:hover {
            color: #ef4444;
            border-color: #ef4444;
            background-color: rgba(239, 68, 68, 0.06);
        }

        .main-content {
            max-width: 1100px;
            margin: 0 auto;
            padding: 32px 20px;
        }

        .nav-hamburger {
            display: none;
            min-width: 44px;
            min-height: 44px;
            font-size: 1.35rem;
            line-height: 1;
            padding: 0;
        }

        .nav-mobile-menu {
            display: none;
            width: 100%;
            flex-direction: column;
            background-color: var(--bg-nav);
            border-bottom: 1px solid var(--border);
        }

        .nav-mobile-menu.open {
            display: flex;
        }

        .nav-mobile-menu a,
        .nav-mobile-menu .nav-mobile-link {
            display: flex;
            align-items: center;
            min-height: 48px;
            padding: 12px 20px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 500;
            border-top: 1px solid var(--border);
            transition: background-color 0.15s, color 0.15s;
        }

        .nav-mobile-menu a:hover,
        .nav-mobile-menu a.active {
            color: var(--text);
            background-color: var(--border);
        }

        .nav-mobile-menu a.active { color: var(--yellow); }

        .nav-mobile-menu form {
            border-top: 1px solid var(--border);
            padding: 10px 20px 14px;
        }

        .nav-mobile-menu .btn-logout-mobile {
            width: 100%;
            min-height: 48px;
            background: none;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 16px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            transition: color 0.15s, border-color 0.15s, background-color 0.15s;
        }

        .nav-mobile-menu .btn-logout-mobile:hover {
            color: #ef4444;
            border-color: #ef4444;
            background-color: rgba(239, 68, 68, 0.06);
        }

        @media (max-width: 768px) {
            .navbar {
                display: flex;
                flex-direction: column;
                align-items: stretch;
            }

            .navbar-inner {
                height: auto;
                min-height: 56px;
                padding: 8px 16px;
                gap: 12px;
            }

            .navbar-links {
                display: none;
            }

            .nav-logout-desktop {
                display: none !important;
            }

            .nav-hamburger {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .btn-icon#themeToggle {
                min-width: 44px;
                min-height: 44px;
            }

            .navbar-avatar {
                width: 40px;
                height: 40px;
                min-width: 44px;
                min-height: 44px;
                box-sizing: border-box;
                padding: 2px;
            }

            .main-content {
                padding: 20px 16px;
            }

            #toast-container {
                left: 16px;
                right: 16px;
                bottom: 16px;
            }

            .toast {
                min-width: 0;
                max-width: none;
            }

            #confirm-cancel,
            #confirm-ok {
                min-height: 44px;
            }
        }

        /* ══════════════════════════════════════
           TOAST NOTIFICATION
        ══════════════════════════════════════ */
        #toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .toast {
            display: flex;
            align-items: center;
            gap: 12px;
            background-color: #1a1a1a;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 12px 16px 12px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.875rem;
            color: #f0f0f0;
            min-width: 260px;
            max-width: 360px;
            pointer-events: all;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
            animation: toastIn 0.25s ease forwards;
        }

        html.light .toast {
            background-color: #ffffff;
            border-color: #e0e0e0;
            color: #111111;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .toast.hiding {
            animation: toastOut 0.3s ease forwards;
        }

        @keyframes toastIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes toastOut {
            from { opacity: 1; transform: translateY(0); }
            to   { opacity: 0; transform: translateY(8px); }
        }

        .toast-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .toast-dot.success { background-color: #22c55e; }
        .toast-dot.error   { background-color: #ef4444; }

        .toast-msg {
            flex: 1;
            line-height: 1.4;
        }

        .toast-close {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 1rem;
            line-height: 1;
            padding: 0 0 0 4px;
            flex-shrink: 0;
            transition: color 0.15s;
        }

        .toast-close:hover { color: #f0f0f0; }
        html.light .toast-close:hover { color: #111; }

        /* ══════════════════════════════════════
           CONFIRM MODAL
        ══════════════════════════════════════ */
        #confirm-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(0,0,0,0.6);
            z-index: 9998;
            align-items: center;
            justify-content: center;
        }

        #confirm-overlay.open {
            display: flex;
        }

        #confirm-modal {
            background-color: #1a1a1a;
            border: 1px solid #333;
            border-radius: 14px;
            padding: 28px;
            width: 100%;
            max-width: 380px;
            margin: 16px;
            text-align: center;
            animation: modalIn 0.2s ease forwards;
        }

        html.light #confirm-modal {
            background-color: #ffffff;
            border-color: #e0e0e0;
        }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95); }
            to   { opacity: 1; transform: scale(1); }
        }

        #confirm-icon {
            font-size: 2rem;
            margin-bottom: 12px;
        }

        #confirm-message {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            color: #f0f0f0;
            line-height: 1.5;
            margin-bottom: 24px;
        }

        html.light #confirm-message { color: #111; }

        .confirm-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        #confirm-cancel {
            flex: 1;
            background: transparent;
            border: 1px solid #444;
            border-radius: 8px;
            padding: 10px 16px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            color: #aaa;
            cursor: pointer;
            transition: border-color 0.15s, color 0.15s;
        }

        #confirm-cancel:hover { border-color: #888; color: #f0f0f0; }
        html.light #confirm-cancel { color: #555; border-color: #ccc; }
        html.light #confirm-cancel:hover { border-color: #888; color: #111; }

        #confirm-ok {
            flex: 1;
            background-color: #f5c518;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 700;
            color: #111;
            cursor: pointer;
            transition: background-color 0.15s;
        }

        #confirm-ok:hover { background-color: #d4a900; }

        #confirm-ok.danger {
            background-color: #ef4444;
            color: #fff;
        }

        #confirm-ok.danger:hover { background-color: #dc2626; }
    </style>
    @stack('styles')
</head>
<body>

<nav class="navbar">
    <div class="navbar-inner">
        <a href="/dashboard" class="navbar-logo"><span class="navbar-logo-al">AL</span><span class="navbar-logo-track">track</span></a>

        <div class="navbar-links">
            <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                <span>Dashboard</span>
            </a>
            <a href="/progress" class="{{ request()->is('progress') ? 'active' : '' }}">
                <span>Progress</span>
            </a>
            <a href="/log" class="{{ request()->is('log') ? 'active' : '' }}">
                <span>Log Workout</span>
            </a>
            @if(auth()->user()->isAdmin())
            <a href="/admin" class="{{ request()->is('admin') ? 'active' : '' }}">
                <span>Admin</span>
            </a>
            @endif
        </div>

        <div class="navbar-right">
            <button class="btn-icon" id="themeToggle" onclick="toggleTheme()" title="Toggle dark/light mode">
                <svg id="iconMoon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
                <svg id="iconSun" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                    <circle cx="12" cy="12" r="5"/>
                    <line x1="12" y1="1" x2="12" y2="3"/>
                    <line x1="12" y1="21" x2="12" y2="23"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="1" y1="12" x2="3" y2="12"/>
                    <line x1="21" y1="12" x2="23" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
            </button>

            <a href="/profile" class="navbar-avatar" title="Profile — {{ auth()->user()->name }}">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M12 2a5 5 0 1 1 0 10A5 5 0 0 1 12 2zm0 12c-5.33 0-8 2.67-8 4v1h16v-1c0-1.33-2.67-4-8-4z" clip-rule="evenodd"/>
                    </svg>
                @endif
            </a>

            <form action="/logout" method="POST" class="nav-logout-desktop" id="form-logout-desktop" style="display:inline;">
                @csrf
                <button type="button" class="btn-logout"
                    onclick="confirmAction('Yakin mau keluar?', 'form-logout-desktop', { okLabel: 'Keluar' })">
                    <span>Keluar</span>
                </button>
            </form>

            <button type="button" class="btn-icon nav-hamburger" id="navMenuBtn" aria-expanded="false" aria-controls="navMobileMenu" aria-label="Buka menu navigasi">
                ☰
            </button>
        </div>
    </div>

    <div class="nav-mobile-menu" id="navMobileMenu" aria-hidden="true">
        <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="/progress" class="{{ request()->is('progress') ? 'active' : '' }}">Progress</a>
        <a href="/log" class="{{ request()->is('log') ? 'active' : '' }}">Log Workout</a>
        @if(auth()->user()->isAdmin())
        <a href="/admin" class="{{ request()->is('admin') || request()->is('admin/*') ? 'active' : '' }}">Admin</a>
        @endif
        <form action="/logout" method="POST" id="form-logout-mobile">
            @csrf
            <button type="button" class="btn-logout-mobile"
                onclick="confirmAction('Yakin mau keluar?', 'form-logout-mobile', { okLabel: 'Keluar' })">Keluar</button>
        </form>
    </div>
</nav>

<main class="main-content">
    @yield('content')
</main>

{{-- ── TOAST CONTAINER ── --}}
<div id="toast-container"></div>

{{-- ── CONFIRM MODAL ── --}}
<div id="confirm-overlay" onclick="handleOverlayClick(event)">
    <div id="confirm-modal">
        <div id="confirm-icon">⚠️</div>
        <div id="confirm-message">Kamu yakin?</div>
        <div class="confirm-actions">
            <button id="confirm-cancel" onclick="closeConfirm()">Batal</button>
            <button id="confirm-ok" onclick="submitConfirm()">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

{{-- ── BLADE SESSION → JS ── --}}
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        showToast({{ Js::from(session('success')) }}, 'success');
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        showToast({{ Js::from(session('error')) }}, 'error');
    });
</script>
@endif

@if($errors->has('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        showToast({{ Js::from($errors->first('error')) }}, 'error');
    });
</script>
@endif

<script>
    /* ── THEME ── */
    (function () {
        const saved = localStorage.getItem('altrack-theme');
        if (saved === 'light') {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
        }
    })();

    function toggleTheme() {
        const html = document.documentElement;
        const moon = document.getElementById('iconMoon');
        const sun  = document.getElementById('iconSun');
        if (html.classList.contains('dark')) {
            html.classList.replace('dark', 'light');
            localStorage.setItem('altrack-theme', 'light');
            moon.style.display = 'none';
            sun.style.display  = 'block';
        } else {
            html.classList.replace('light', 'dark');
            localStorage.setItem('altrack-theme', 'dark');
            moon.style.display = 'block';
            sun.style.display  = 'none';
        }
    }

    (function () {
        const saved = localStorage.getItem('altrack-theme');
        if (saved === 'light') {
            document.getElementById('iconMoon').style.display = 'none';
            document.getElementById('iconSun').style.display  = 'block';
        }
    })();

    /* ── TOAST ── */
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');

        const toast = document.createElement('div');
        toast.className = 'toast';

        const dot = document.createElement('div');
        dot.className = 'toast-dot ' + type;

        const msg = document.createElement('div');
        msg.className = 'toast-msg';
        msg.textContent = message;

        const close = document.createElement('button');
        close.className = 'toast-close';
        close.innerHTML = '&times;';
        close.onclick = () => dismissToast(toast);

        toast.appendChild(dot);
        toast.appendChild(msg);
        toast.appendChild(close);
        container.appendChild(toast);

        // Auto dismiss setelah 3 detik
        setTimeout(() => dismissToast(toast), 3000);
    }

    function dismissToast(toast) {
        if (!toast || toast.classList.contains('hiding')) return;
        toast.classList.add('hiding');
        toast.addEventListener('animationend', () => toast.remove(), { once: true });
    }

    /* ── CONFIRM MODAL ── */
    let _confirmFormId   = null;
    let _confirmCallback = null;

    function confirmAction(message, formId, options = {}) {
        _confirmFormId   = formId;
        _confirmCallback = null;

        document.getElementById('confirm-message').textContent = message;
        document.getElementById('confirm-icon').textContent =
            options.danger ? '🗑️' : '⚠️';

        const okBtn = document.getElementById('confirm-ok');
        okBtn.textContent = options.okLabel || 'Ya, Lanjutkan';
        if (options.danger) {
            okBtn.classList.add('danger');
        } else {
            okBtn.classList.remove('danger');
        }

        document.getElementById('confirm-overlay').classList.add('open');
    }

    function closeConfirm() {
        document.getElementById('confirm-overlay').classList.remove('open');
        _confirmFormId   = null;
        _confirmCallback = null;
    }

    function submitConfirm() {
        if (_confirmFormId) {
            const form = document.getElementById(_confirmFormId);
            if (form) form.submit();
        }
        closeConfirm();
    }

    function handleOverlayClick(e) {
        if (e.target === document.getElementById('confirm-overlay')) {
            closeConfirm();
        }
    }

    // Tutup modal dengan Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeConfirm();
    });

    /* ── Mobile nav menu ── */
    (function () {
        const btn = document.getElementById('navMenuBtn');
        const menu = document.getElementById('navMobileMenu');
        if (!btn || !menu) return;

        function setOpen(open) {
            menu.classList.toggle('open', open);
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            menu.setAttribute('aria-hidden', open ? 'false' : 'true');
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            setOpen(!menu.classList.contains('open'));
        });

        menu.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () {
                setOpen(false);
            });
        });

        const menuForm = menu.querySelector('form');
        if (menuForm) {
            menuForm.addEventListener('submit', function () {
                setOpen(false);
            });
        }

        document.addEventListener('click', function (e) {
            if (!menu.classList.contains('open')) return;
            if (e.target.closest('#navMenuBtn') || e.target.closest('#navMobileMenu')) return;
            if (e.target.closest('.navbar-inner')) return;
            setOpen(false);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && menu.classList.contains('open')) setOpen(false);
        });
    })();
</script>

@stack('scripts')
</body>
</html>