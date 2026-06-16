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
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <style>
            :root {
                --bg: #090b10;
                --surface: rgba(16, 18, 27, 0.82);
                --surface-strong: #141722;
                --line: rgba(255, 255, 255, 0.12);
                --line-strong: rgba(255, 255, 255, 0.2);
                --text: #f7f8fb;
                --muted: #a9b1c2;
                --soft: #d8deea;
                --accent: #ff424a;
                --accent-2: #d90f28;
                --accent-soft: rgba(255, 66, 74, 0.14);
                --field: rgba(255, 255, 255, 0.075);
                --spot-x: 50%;
                --spot-y: 40%;
            }

            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            html {
                min-height: 100%;
                background: var(--bg);
            }

            body {
                min-height: 100vh;
                color: var(--text);
                font-family: 'Inter', system-ui, sans-serif;
                background:
                    radial-gradient(circle at var(--spot-x) var(--spot-y), rgba(255, 66, 74, 0.16), transparent 20rem),
                    linear-gradient(135deg, #11131c 0%, #090b10 54%, #150911 100%);
            }

            .page {
                position: relative;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: clamp(18px, 4vw, 48px);
                overflow: hidden;
                background:
                    linear-gradient(180deg, rgba(7, 9, 14, 0.46), rgba(7, 9, 14, 0.88)),
                    linear-gradient(115deg, rgba(7, 9, 14, 0.78), rgba(89, 12, 28, 0.28), rgba(7, 9, 14, 0.82)),
                    url('https://images.unsplash.com/photo-1571902943202-507ec2618e8f?auto=format&fit=crop&w=1800&q=85') center/cover no-repeat;
            }

            .page::before {
                content: '';
                position: absolute;
                inset: 0;
                background:
                    linear-gradient(rgba(255,255,255,0.035) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,0.035) 1px, transparent 1px);
                background-size: 48px 48px;
                opacity: 0.34;
                pointer-events: none;
            }

            .page::after {
                content: '';
                position: absolute;
                inset: 0;
                background: radial-gradient(circle at 50% 45%, transparent 0 16rem, rgba(0, 0, 0, 0.36) 38rem);
                pointer-events: none;
            }

            .auth-shell {
                position: relative;
                z-index: 1;
                width: min(460px, 100%);
            }

            .visual {
                display: contents;
            }

            .visual::after {
                display: none;
            }

            .visual-copy,
            .session-card {
                position: relative;
                z-index: 1;
            }

            .mark {
                width: 44px;
                height: 44px;
                border-radius: 8px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                background: linear-gradient(135deg, var(--accent), var(--accent-2));
                box-shadow: 0 16px 35px rgba(217, 15, 40, 0.3);
            }

            .visual-copy {
                display: none;
            }

            .visual-copy span {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 16px;
                color: #ffd2d5;
                font-size: 0.78rem;
                font-weight: 800;
                text-transform: uppercase;
            }

            .visual-copy span::before {
                content: '';
                width: 28px;
                height: 2px;
                background: var(--accent);
            }

            .visual-copy h1 {
                max-width: 430px;
                font-size: clamp(2.2rem, 5vw, 4.6rem);
                line-height: 0.98;
                font-weight: 800;
                letter-spacing: 0;
            }

            .visual-copy p {
                max-width: 420px;
                margin-top: 18px;
                color: #e2e7f0;
                font-size: 1rem;
                line-height: 1.75;
            }

            .session-card {
                display: none;
            }

            .session-card strong {
                display: block;
                margin-bottom: 4px;
                font-size: 0.96rem;
            }

            .session-card small {
                color: var(--muted);
                line-height: 1.5;
            }

            .pulse {
                width: 42px;
                height: 42px;
                border-radius: 8px;
                display: grid;
                place-items: center;
                color: #4ade80;
                background: rgba(74, 222, 128, 0.1);
                border: 1px solid rgba(74, 222, 128, 0.24);
            }

            .panel {
                padding: 0;
                background:
                    radial-gradient(circle at 100% 0, rgba(255, 66, 74, 0.12), transparent 14rem),
                    linear-gradient(180deg, rgba(18, 21, 31, 0.16), rgba(10, 12, 19, 0.2));
            }

            .login-card {
                width: 100%;
                padding: clamp(24px, 5vw, 38px);
                border: 1px solid rgba(255, 255, 255, 0.18);
                border-radius: 8px;
                background: rgba(9, 11, 17, 0.48);
                box-shadow: 0 26px 80px rgba(0, 0, 0, 0.48);
                backdrop-filter: blur(22px) saturate(1.18);
            }

            .card-head {
                text-align: center;
                margin-bottom: 30px;
            }

            .card-brand {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
                margin-bottom: 24px;
                color: #fff;
                font-size: 0.96rem;
                font-weight: 800;
            }

            .brand-logo {
                width: 60px;
                height: auto;
                display: block;
                object-fit: contain;
            }

            .card-head h2 {
                margin-bottom: 10px;
                font-size: clamp(1.75rem, 3vw, 2.45rem);
                line-height: 1.1;
            }

            .card-head p {
                max-width: 330px;
                margin: 0 auto;
                color: var(--muted);
                font-size: 1rem;
                line-height: 1.65;
            }

            .alert {
                margin-bottom: 20px;
                padding: 13px 14px;
                border: 1px solid rgba(255, 66, 74, 0.34);
                border-radius: 8px;
                background: rgba(255, 66, 74, 0.11);
                color: #ffe3e5;
                font-size: 0.92rem;
                line-height: 1.5;
            }

            .form-group {
                margin-bottom: 18px;
            }

            label {
                display: block;
                margin-bottom: 9px;
                color: var(--soft);
                font-size: 0.9rem;
                font-weight: 700;
            }

            .input-wrap {
                position: relative;
            }

            .input-wrap input {
                width: 100%;
                min-height: 54px;
                padding: 0 48px;
                border: 1px solid var(--line);
                border-radius: 8px;
                outline: 0;
                color: var(--text);
                background: rgba(255, 255, 255, 0.09);
                font: inherit;
                transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, transform 0.2s ease;
            }

            .input-wrap input::placeholder {
                color: rgba(169, 177, 194, 0.68);
            }

            .input-wrap input:focus {
                border-color: rgba(255, 66, 74, 0.72);
                background: rgba(255, 255, 255, 0.1);
                box-shadow: 0 0 0 4px var(--accent-soft);
            }

            .input-wrap:focus-within input {
                transform: translateY(-1px);
            }

            .field-icon {
                position: absolute;
                top: 50%;
                left: 17px;
                transform: translateY(-50%);
                color: #929bad;
                pointer-events: none;
                transition: color 0.2s ease;
            }

            .input-wrap:focus-within .field-icon {
                color: #ff9aa0;
            }

            .password-toggle {
                position: absolute;
                top: 50%;
                right: 9px;
                width: 36px;
                height: 36px;
                transform: translateY(-50%);
                border: 0;
                border-radius: 8px;
                color: #bdc6d6;
                background: transparent;
                cursor: pointer;
                transition: background 0.2s ease, color 0.2s ease;
            }

            .password-toggle:hover,
            .password-toggle:focus {
                color: #fff;
                background: rgba(255, 255, 255, 0.08);
                outline: 0;
            }

            .row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 14px;
                margin: 20px 0 24px;
            }

            .remember {
                display: inline-flex;
                align-items: center;
                gap: 9px;
                color: var(--muted);
                font-size: 0.9rem;
                cursor: pointer;
            }

            .remember input {
                width: 16px;
                height: 16px;
                accent-color: var(--accent);
            }

            .role-badge {
                min-height: 32px;
                padding: 7px 10px;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                border-radius: 8px;
                color: #ffd5d8;
                background: rgba(255, 66, 74, 0.1);
                border: 1px solid rgba(255, 66, 74, 0.24);
                font-size: 0.8rem;
                font-weight: 800;
                white-space: nowrap;
            }

            .submit {
                width: 100%;
                min-height: 56px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
                border: 0;
                border-radius: 8px;
                color: #fff;
                background: linear-gradient(135deg, #ff4b53, #d80f27);
                box-shadow: 0 18px 36px rgba(216, 15, 39, 0.28);
                cursor: pointer;
                font-size: 0.98rem;
                font-weight: 800;
                transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            }

            .submit:hover {
                transform: translateY(-2px);
                box-shadow: 0 24px 48px rgba(216, 15, 39, 0.38);
                filter: saturate(1.08);
            }

            .submit:active {
                transform: translateY(0);
            }

            .foot {
                margin-top: 26px;
                padding-top: 18px;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                color: var(--muted);
                font-size: 0.82rem;
                line-height: 1.7;
                text-align: center;
            }

            @media (max-width: 1020px) {
                .page {
                    overflow: auto;
                }
            }

            @media (max-width: 640px) {
                .page {
                    min-height: 100svh;
                    padding: 18px;
                }

                .panel {
                    padding: 0;
                }

                .login-card {
                    padding: 22px 18px;
                }

                .card-head {
                    margin-bottom: 24px;
                }

                .card-brand {
                    margin-bottom: 20px;
                }

                .row {
                    align-items: flex-start;
                    flex-direction: column;
                }
            }
        </style>
    </head>
    <body>
        @php
            $selectedRole = old('role', request('role', 'admin'));
            if (is_array($selectedRole)) {
                $selectedRole = 'member';
            }
            $loginValue = old('login', '');
            if (is_array($loginValue)) {
                $loginValue = '';
            }
            $emailValue = old('email', '');
            if (is_array($emailValue)) {
                $emailValue = '';
            }
            $errorMessage = $errors->any() ? $errors->first() : '';
            if (is_array($errorMessage)) {
                $errorMessage = implode(' ', array_map('strval', $errorMessage));
            }
            $roleLabel = $selectedRole === 'member' ? 'Member Portal' : 'Staff Portal';
        @endphp

        <main class="page">
            <section class="auth-shell">
                <div class="visual" aria-label="Arena Fitness">
                    <div class="visual-copy">
                        <span>{{ $roleLabel }}</span>
                        <h1>Login ke akun Anda</h1>
                        <p>Masuk untuk mengakses dashboard dan melanjutkan pengelolaan operasional gym.</p>
                    </div>

                    <div class="session-card">
                        <div>
                            <strong>Akses aman</strong>
                            <small>Gunakan akun yang sudah terdaftar untuk masuk.</small>
                        </div>
                        <div class="pulse"><i class="fas fa-signal"></i></div>
                    </div>
                </div>

                <div class="panel">
                    <div class="login-card">
                        <div class="card-head">
                            <div class="card-brand">
                                <img src="{{ asset('images/arena-fitness-logo.jpg') }}" alt="Arena Fitness" class="brand-logo">
                                <span>ARENA FITNESS</span>
                            </div>
                            <h2>Login</h2>
                            <p>Masukkan username dan password untuk masuk ke sistem.</p>
                        </div>

                        @if ($errorMessage)
                            <div class="alert">{{ $errorMessage }}</div>
                        @endif

                        <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                            @csrf
                            <input type="hidden" name="role" id="roleInput" value="{{ $selectedRole }}">

                            @if($selectedRole === 'member')
                                <div class="form-group" id="emailField">
                                    <label for="email">Email Member</label>
                                    <div class="input-wrap">
                                        <input type="email" id="email" name="email" value="{{ $emailValue }}" placeholder="member@arena.id" required>
                                        <i class="fas fa-envelope field-icon"></i>
                                    </div>
                                </div>
                            @else
                                <div class="form-group" id="loginField">
                                    <label for="login">Username</label>
                                    <div class="input-wrap">
                                        <input type="text" id="login" name="login" value="{{ $loginValue }}" placeholder="Masukkan ID Staff" required>
                                        <i class="fas fa-user-shield field-icon"></i>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-wrap">
                                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                                    <i class="fas fa-key field-icon"></i>
                                    <button class="password-toggle" type="button" aria-label="Tampilkan password" data-password-toggle>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="row">
                                <label class="remember">
                                    <input type="checkbox" name="remember"> Ingat sesi saya
                                </label>
                                <span class="role-badge">
                                    <i class="fas fa-shield-alt"></i>
                                    {{ $roleLabel }}
                                </span>
                            </div>

                            <button type="submit" class="submit">
                                <span>Masuk ke Sistem</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>

                        <div class="foot">
                            Sistem Manajemen Gym v2.0<br>
                            &copy; {{ date('Y') }} Hak Cipta Dilindungi.
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <script>
            const passwordToggle = document.querySelector('[data-password-toggle]');
            const passwordInput = document.getElementById('password');

            if (passwordToggle && passwordInput) {
                passwordToggle.addEventListener('click', () => {
                    const isHidden = passwordInput.type === 'password';
                    passwordInput.type = isHidden ? 'text' : 'password';
                    passwordToggle.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
                    passwordToggle.innerHTML = isHidden ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
                });
            }

            window.addEventListener('pointermove', (event) => {
                const x = Math.round((event.clientX / window.innerWidth) * 100);
                const y = Math.round((event.clientY / window.innerHeight) * 100);
                document.documentElement.style.setProperty('--spot-x', `${x}%`);
                document.documentElement.style.setProperty('--spot-y', `${y}%`);
            });
        </script>
    </body>
</html>
