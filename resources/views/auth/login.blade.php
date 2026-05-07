<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @php
            $pageTitle = $pageTitle ?? 'Management Login | Gym System';
            if (is_array($pageTitle)) {
                $pageTitle = 'Management Login | Gym System';
            }
        @endphp
        <title>{{ $pageTitle }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <style>
            :root {
                --primary: #ff3b3b;
                --primary-soft: rgba(255, 59, 59, 0.12);
                --bg-dark: #0b1120;
                --card: rgba(22, 23, 35, 0.94);
                --border: rgba(255, 255, 255, 0.08);
                --text: #f8fafc;
                --muted: #94a3b8;
                --field-bg: #111827;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Inter', sans-serif;
            }

            body {
                min-height: 100vh;
                background-color: var(--bg-dark);
                display: flex;
                align-items: center;
                justify-content: center;
                background-image:
                    linear-gradient(rgba(11, 17, 36, 0.9), rgba(11, 17, 36, 0.9)),
                    url('https://images.unsplash.com/photo-1540497077202-7c8a3999166f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
                background-size: cover;
                background-position: center;
            }

            .login-card {
                width: 100%;
                max-width: 420px;
                background: var(--card);
                padding: 40px;
                border-radius: 24px;
                border: 1px solid var(--border);
                box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
            }

            .login-card .header {
                text-align: center;
                margin-bottom: 35px;
            }

            .login-card .header i {
                font-size: 3rem;
                color: var(--primary);
                margin-bottom: 15px;
            }

            .login-card .header h1 {
                color: var(--text);
                font-size: 1.5rem;
                font-weight: 700;
                letter-spacing: 1px;
                margin-bottom: 5px;
            }

            .login-card .header p {
                color: var(--muted);
                font-size: 0.85rem;
                text-transform: uppercase;
                letter-spacing: 2px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                color: #cbd5e1;
                font-size: 0.85rem;
                margin-bottom: 8px;
                font-weight: 500;
            }

            .input-wrapper {
                position: relative;
                display: flex;
                align-items: center;
            }

            .input-wrapper i {
                position: absolute;
                left: 14px;
                color: #64748b;
                transition: 0.3s;
            }

            .input-wrapper input {
                width: 100%;
                padding: 12px 12px 12px 42px;
                background: var(--field-bg);
                border: 1px solid #334155;
                border-radius: 14px;
                color: var(--text);
                font-size: 0.95rem;
                transition: all 0.3s ease;
                outline: none;
            }

            .input-wrapper input:focus {
                border-color: var(--primary);
                box-shadow: 0 0 0 4px rgba(255, 59, 59, 0.18);
            }

            .input-wrapper input:focus + i {
                color: var(--primary);
            }

            .options {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 25px;
            }

            .remember-me {
                display: flex;
                align-items: center;
                gap: 8px;
                color: var(--muted);
                font-size: 0.8rem;
                cursor: pointer;
            }

            .remember-me input {
                accent-color: var(--primary);
            }

            .btn-login {
                width: 100%;
                padding: 12px;
                background: linear-gradient(135deg, #ff4c4c 0%, #b70f1f 100%);
                color: #fff;
                border: none;
                border-radius: 14px;
                font-size: 1rem;
                font-weight: 700;
                cursor: pointer;
                transition: 0.3s ease;
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 10px;
            }

            .btn-login:hover {
                background: #ff6b6b;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(255, 75, 75, 0.3);
            }

            .btn-login:active {
                transform: translateY(0);
            }

            .footer-note {
                text-align: center;
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #334155;
                color: var(--muted);
                font-size: 0.75rem;
            }

            .alert {
                border-radius: 14px;
                border: 1px solid rgba(255, 75, 75, 0.2);
                background: rgba(255, 75, 75, 0.08);
                color: #ffe4e4;
                margin-bottom: 24px;
            }

            @media (max-width: 450px) {
                .login-card {
                    margin: 20px;
                    padding: 30px 20px;
                }
            }
        </style>
    </head>
    <body>
        <div class="login-card">
            <div class="header">
                <i class="fas fa-dumbbell"></i>
                <h1>GYM MASTER</h1>
                <p>Staff Portal</p>
            </div>

            @php
                $selectedRole = old('role', request('role', 'admin'));
                if (is_array($selectedRole)) {
                    $selectedRole = 'admin';
                }
                $loginValue = old('login', '');
                if (is_array($loginValue)) {
                    $loginValue = '';
                }
                $errorMessage = $errors->any() ? $errors->first() : '';
                if (is_array($errorMessage)) {
                    $errorMessage = implode(' ', array_map('strval', $errorMessage));
                }
            @endphp

            @if ($errorMessage)
                <div class="alert">{{ $errorMessage }}</div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <input type="hidden" name="role" value="{{ $selectedRole }}">
                <div class="form-group">
                    <label for="login">Username</label>
                    <div class="input-wrapper">
                        <input type="text" id="login" name="login" value="{{ $loginValue }}" placeholder="Masukkan ID Staff" required>
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <i class="fas fa-key"></i>
                    </div>
                </div>

                <div class="options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> Ingat sesi saya
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    <span>MASUK KE SISTEM</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <div class="footer-note">
                Sistem Manajemen Gym v2.0<br>
                &copy; 2023 Hak Cipta Dilindungi.
            </div>
        </div>
    </body>
</html>
