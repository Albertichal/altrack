<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALtrack — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --yellow: #f5c518;
            --yellow-dark: #d4a900;
            --bg: #0f0f0f;
            --bg-card: #1a1a1a;
            --border: #2a2a2a;
            --text: #f0f0f0;
            --text-muted: #888888;
            --input-bg: #111111;
            --radius: 14px; /* Disamakan dengan dashboard */
        }

        html.light {
            --bg: #f0f0f0;
            --bg-card: #ffffff;
            --border: #ddd;
            --text: #111111;
            --text-muted: #666666;
            --input-bg: #f8f8f8;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 400px;
        }

        /* Logo Styling */
        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-logo h1 {
            font-size: 2.2rem;
            font-weight: 800; /* Lebih bold agar 'serius' */
            color: var(--yellow);
            letter-spacing: -1.5px;
            text-transform: none; /* Menjaga case sensitivitas */
        }

        /* Bagian 'track' menjadi putih */
        .login-logo h1 span {
            color: var(--text);
        }

        .login-logo p {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-top: 4px;
            letter-spacing: 0.2px;
        }

        /* Card */
        .login-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 32px;
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.72rem; /* Ukuran stat-label di dashboard */
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.55px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        .form-group input {
            width: 100%;
            background-color: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            color: var(--text);
            outline: none;
            transition: border-color 0.15s;
        }

        .form-group input:focus {
            border-color: var(--yellow);
        }

        .form-group input.has-toggle {
            padding-right: 44px;
        }

        /* Eye toggle button */
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
            padding: 2px;
            transition: color 0.15s;
        }

        .toggle-pw:hover {
            color: var(--text);
        }

        /* Error message */
        .error-msg {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.875rem;
            color: #f87171;
            margin-bottom: 20px;
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            background-color: var(--yellow);
            color: #111111;
            border: none;
            border-radius: 8px;
            padding: 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.15s, transform 0.1s;
            margin-top: 8px;
        }

        .btn-submit:hover {
            background-color: var(--yellow-dark);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        @media (max-width: 768px) {
            body {
                padding: 16px;
                align-items: center;
            }

            .login-card {
                padding: 24px;
            }
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <div class="login-logo">
            <h1>AL<span>track</span></h1>
            <p>Gym tracker</p>
        </div>

        <div class="login-card">

            @if ($errors->has('username'))
                <div class="error-msg">
                    {{ $errors->first('username') }}
                </div>
            @endif

            <form action="/login" method="POST">
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                        placeholder="Masukkan username" autocomplete="username" autofocus required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <input type="password" id="password" name="password" placeholder="Masukkan password"
                            class="has-toggle" autocomplete="current-password" required>
                        <button type="button" class="toggle-pw" onclick="togglePassword()"
                            title="Tampilkan/sembunyikan password">
                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                <line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Masuk</button>
            </form>

        </div>
    </div>

    <script>
        // Restore tema dari localStorage
        (function() {
            const saved = localStorage.getItem('altrack-theme');
            if (saved === 'light') {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            }
        })();

        function togglePassword() {
            const input = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');

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

</body>

</html>