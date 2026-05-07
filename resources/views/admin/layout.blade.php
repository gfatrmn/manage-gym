<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle ?? 'Dashboard Admin Arena Gym' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800|space-grotesk:500,700" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script>
            (() => {
                const savedTheme = localStorage.getItem('arena-gym-theme');
                const preferredTheme = savedTheme || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
                document.documentElement.setAttribute('data-theme', preferredTheme);
            })();
        </script>

        <style>
            :root {
                --app-bg: #060606;
                --surface: rgba(20, 20, 20, 0.92);
                --surface-soft: rgba(34, 34, 34, 0.94);
                --border: rgba(255, 255, 255, 0.08);
                --border-strong: rgba(255, 68, 68, 0.3);
                --text-main: #f5f5f5;
                --text-muted: #a1a1aa;
                --panel-glow: rgba(255, 59, 59, 0.14);
                --red: #ff3b3b;
                --red-deep: #a60f1f;
                --red-soft: #ff8a8a;
                --charcoal: #111111;
                --charcoal-2: #181818;
                --shadow: 0 28px 60px rgba(0, 0, 0, 0.45);
                --body-gradient:
                    radial-gradient(circle at top right, rgba(255, 59, 59, 0.22), transparent 28%),
                    radial-gradient(circle at bottom left, rgba(166, 15, 31, 0.28), transparent 24%),
                    linear-gradient(160deg, #050505 0%, #0c0c0c 48%, #15080a 100%);
                --grid-line: rgba(255,255,255,0.025);
                --sidebar-bg: linear-gradient(180deg, rgba(25, 25, 25, 0.96) 0%, rgba(10, 10, 10, 0.96) 100%);
                --sidebar-link-color: rgba(255,255,255,0.76);
                --sidebar-nav-label-color: rgba(255,255,255,0.56);
                --sidebar-extra-bg: linear-gradient(180deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.02) 100%);
                --hero-pill-bg: rgba(255,255,255,0.04);
                --hero-pill-border: rgba(255,255,255,0.08);
                --hero-pill-color: #d4d4d8;
                --hero-stat-bg: linear-gradient(180deg, rgba(42, 42, 42, 0.94) 0%, rgba(28, 28, 28, 0.96) 100%);
                --table-head-bg: rgba(255,255,255,0.03);
                --field-bg: rgba(255,255,255,0.04);
                --field-border: rgba(255,255,255,0.12);
                --field-placeholder: rgba(255,255,255,0.38);
                --field-focus-bg: rgba(255,255,255,0.06);
                --field-disabled-bg: rgba(255,255,255,0.08);
                --field-disabled-color: rgba(255,255,255,0.72);
                --modal-bg: linear-gradient(180deg, rgba(22, 22, 22, 0.98) 0%, rgba(12, 12, 12, 0.98) 100%);
                --modal-border: rgba(255,255,255,0.08);
                --toggle-bg: rgba(255,255,255,0.04);
                --toggle-border: rgba(255,255,255,0.12);
            }

            html[data-theme="light"] {
                --app-bg: #f7f4f2;
                --surface: rgba(255, 255, 255, 0.92);
                --surface-soft: rgba(255, 255, 255, 0.97);
                --border: rgba(15, 23, 42, 0.1);
                --border-strong: rgba(214, 40, 57, 0.22);
                --text-main: #18181b;
                --text-muted: #5b6270;
                --panel-glow: rgba(214, 40, 57, 0.12);
                --shadow: 0 24px 48px rgba(15, 23, 42, 0.08);
                --body-gradient:
                    radial-gradient(circle at top right, rgba(255, 111, 111, 0.16), transparent 26%),
                    radial-gradient(circle at bottom left, rgba(214, 40, 57, 0.1), transparent 24%),
                    linear-gradient(160deg, #fffdfc 0%, #f8f5f2 48%, #f2ece8 100%);
                --grid-line: rgba(15,23,42,0.04);
                --sidebar-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(247, 242, 239, 0.98) 100%);
                --sidebar-link-color: rgba(24,24,27,0.78);
                --sidebar-nav-label-color: rgba(24,24,27,0.48);
                --sidebar-extra-bg: linear-gradient(180deg, rgba(255,255,255,0.88) 0%, rgba(248,245,242,0.96) 100%);
                --hero-pill-bg: rgba(15,23,42,0.03);
                --hero-pill-border: rgba(15,23,42,0.08);
                --hero-pill-color: #3f3f46;
                --hero-stat-bg: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(245,240,236,0.98) 100%);
                --table-head-bg: rgba(15,23,42,0.04);
                --field-bg: rgba(255,255,255,0.85);
                --field-border: rgba(15,23,42,0.12);
                --field-placeholder: rgba(24,24,27,0.38);
                --field-focus-bg: rgba(255,255,255,0.96);
                --field-disabled-bg: rgba(15,23,42,0.05);
                --field-disabled-color: rgba(24,24,27,0.6);
                --modal-bg: linear-gradient(180deg, rgba(255,255,255,0.99) 0%, rgba(247,242,239,0.99) 100%);
                --modal-border: rgba(15,23,42,0.08);
                --toggle-bg: rgba(15,23,42,0.03);
                --toggle-border: rgba(15,23,42,0.1);
            }

            body.cashier-layout {
                --body-gradient:
                    radial-gradient(circle at top right, rgba(6, 182, 212, 0.22), transparent 24%),
                    radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.16), transparent 22%),
                    linear-gradient(160deg, #04131c 0%, #08212c 52%, #0c1720 100%);
                --surface: rgba(9, 27, 36, 0.96);
                --surface-soft: rgba(14, 38, 49, 0.98);
                --border: rgba(125, 211, 252, 0.18);
                --border-strong: rgba(34, 211, 238, 0.34);
                --panel-glow: rgba(34, 211, 238, 0.16);
                --shadow: 0 26px 52px rgba(2, 23, 33, 0.38);
                --sidebar-bg: linear-gradient(180deg, rgba(4, 24, 34, 0.99) 0%, rgba(6, 19, 27, 0.99) 100%);
                --sidebar-extra-bg: linear-gradient(180deg, rgba(34, 211, 238, 0.1) 0%, rgba(16, 185, 129, 0.06) 100%);
                --hero-pill-bg: rgba(34, 211, 238, 0.1);
                --hero-pill-border: rgba(103, 232, 249, 0.24);
                --hero-pill-color: #effcff;
                --hero-stat-bg: linear-gradient(180deg, rgba(13, 42, 54, 0.98) 0%, rgba(10, 31, 41, 0.98) 100%);
                --table-head-bg: rgba(34, 211, 238, 0.09);
                --field-bg: rgba(4, 30, 40, 0.9);
                --field-border: rgba(103, 232, 249, 0.18);
                --field-placeholder: rgba(220, 252, 255, 0.42);
                --field-focus-bg: rgba(10, 44, 57, 0.96);
                --field-disabled-bg: rgba(7, 30, 39, 0.82);
                --field-disabled-color: rgba(219, 243, 247, 0.66);
                --modal-bg: linear-gradient(180deg, rgba(8, 30, 39, 0.99) 0%, rgba(7, 21, 29, 0.99) 100%);
                --modal-border: rgba(103, 232, 249, 0.14);
                --toggle-bg: rgba(34, 211, 238, 0.08);
                --toggle-border: rgba(103, 232, 249, 0.18);
            }

            html[data-theme="light"] body.cashier-layout {
                --body-gradient:
                    radial-gradient(circle at top right, rgba(6, 182, 212, 0.12), transparent 24%),
                    radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.1), transparent 22%),
                    linear-gradient(160deg, #fbfeff 0%, #f0fbff 48%, #e8f8ff 100%);
                --surface: rgba(255, 255, 255, 0.98);
                --surface-soft: rgba(241, 251, 255, 0.99);
                --border: rgba(8, 145, 178, 0.16);
                --border-strong: rgba(6, 182, 212, 0.24);
                --text-main: #0f2430;
                --text-muted: #45606d;
                --panel-glow: rgba(6, 182, 212, 0.1);
                --shadow: 0 24px 44px rgba(8, 47, 73, 0.1);
                --sidebar-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.995) 0%, rgba(236, 249, 255, 0.995) 100%);
                --sidebar-link-color: rgba(15,36,48,0.84);
                --sidebar-nav-label-color: rgba(15,36,48,0.54);
                --sidebar-extra-bg: linear-gradient(180deg, rgba(236, 254, 255, 0.97) 0%, rgba(236, 253, 245, 0.99) 100%);
                --hero-pill-bg: rgba(6, 182, 212, 0.08);
                --hero-pill-border: rgba(8, 145, 178, 0.18);
                --hero-pill-color: #0f5f78;
                --hero-stat-bg: linear-gradient(180deg, rgba(255,255,255,1) 0%, rgba(235,248,252,1) 100%);
                --table-head-bg: rgba(8,145,178,0.08);
                --field-bg: rgba(255,255,255,0.98);
                --field-border: rgba(8,145,178,0.16);
                --field-placeholder: rgba(15,36,48,0.4);
                --field-focus-bg: rgba(243, 253, 255, 1);
                --field-disabled-bg: rgba(8,145,178,0.05);
                --field-disabled-color: rgba(15,36,48,0.62);
                --modal-bg: linear-gradient(180deg, rgba(255,255,255,1) 0%, rgba(238,249,253,1) 100%);
                --modal-border: rgba(8,145,178,0.1);
                --toggle-bg: rgba(8,145,178,0.06);
                --toggle-border: rgba(8,145,178,0.14);
            }

            body {
                font-family: 'Outfit', sans-serif;
                background: var(--body-gradient);
                color: var(--text-main);
                min-height: 100vh;
            }

            body::before {
                content: '';
                position: fixed;
                inset: 0;
                pointer-events: none;
                background-image:
                    linear-gradient(var(--grid-line) 1px, transparent 1px),
                    linear-gradient(90deg, var(--grid-line) 1px, transparent 1px);
                background-size: 42px 42px;
                mask-image: linear-gradient(180deg, rgba(0,0,0,.75), transparent);
            }

            .sidebar {
                width: min(360px, calc(100vw - 2rem));
                min-height: auto;
                background: var(--sidebar-bg);
                color: var(--text-main);
                border-radius: 1.5rem;
                border: 1px solid var(--border);
                box-shadow: var(--shadow);
                position: fixed;
                top: 1rem;
                left: 1rem;
                bottom: 1rem;
                z-index: 2050;
                overflow: hidden;
                overflow-y: auto;
                transform: translateX(calc(-100% - 1.5rem));
                transition: transform .28s ease;
            }

            body.sidebar-open .sidebar {
                transform: translateX(0);
            }

            .sidebar-inner {
                display: flex;
                flex-direction: column;
                min-height: 100%;
                gap: 1.25rem;
            }

            .sidebar-main {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .sidebar-bottom {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .sidebar-column {
                display: block;
                width: 0;
                flex: 0 0 0;
                padding: 0;
            }

            .sidebar-column .sidebar {
                flex: initial;
            }

            .sidebar-column.cashier-sidebar .sidebar {
                min-height: 100%;
            }

            .sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.42);
                backdrop-filter: blur(4px);
                opacity: 0;
                pointer-events: none;
                transition: opacity .2s ease;
                z-index: 2040;
            }

            body.sidebar-open .sidebar-overlay {
                opacity: 1;
                pointer-events: auto;
            }

            .sidebar-toggle {
                position: fixed;
                top: 50%;
                left: 1rem;
                transform: translateY(-50%);
                z-index: 2060;
                width: auto;
                height: auto;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0;
                border: 0;
                background: transparent;
                color: var(--text-main);
                box-shadow: none;
                transition: left .28s ease, transform .2s ease;
            }

            body.sidebar-open .sidebar-toggle {
                left: min(360px, calc(100vw - 2rem));
                transform: translate(0.5rem, -50%);
            }

            .sidebar-toggle:hover {
                color: var(--text-main);
                transform: translateY(calc(-50% - 2px));
            }

            body.sidebar-open .sidebar-toggle:hover {
                transform: translate(0.5rem, calc(-50% - 2px));
            }

            .sidebar-toggle-icon {
                font-size: 2rem;
                font-weight: 800;
                line-height: 1;
                text-shadow: 0 8px 18px rgba(0, 0, 0, 0.2);
            }

            .sidebar-stack {
                min-height: auto;
                display: flex;
                flex-direction: column;
            }

            .sidebar-column.cashier-sidebar .sidebar-stack {
                min-height: auto;
                flex: 1 1 auto;
            }

            .sidebar-extra-card {
                border: 1px solid rgba(255,255,255,0.08);
                border-radius: 1.1rem;
                background: var(--sidebar-extra-bg);
            }

            .sidebar::before {
                content: '';
                position: absolute;
                inset: 0 0 auto 0;
                height: 220px;
                background: radial-gradient(circle at top left, rgba(255, 59, 59, 0.22), transparent 60%);
                pointer-events: none;
            }

            body.cashier-layout .sidebar::before {
                background: radial-gradient(circle at top left, rgba(34, 211, 238, 0.18), transparent 60%);
            }

            .brand-mark {
                width: 4.4rem;
                height: 3rem;
                border-radius: 1rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0 0.6rem;
                background: linear-gradient(135deg, #ff2323 0%, #1d0909 100%);
                font-weight: 800;
                font-family: 'Space Grotesk', sans-serif;
                color: #fff;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                font-size: 0.8rem;
                box-shadow: 0 18px 32px rgba(255, 59, 59, 0.28);
                border: 1px solid rgba(255,255,255,0.08);
            }

            body.cashier-layout .brand-mark,
            body.cashier-layout .metric-icon,
            body.cashier-layout .table-avatar-placeholder {
                background: linear-gradient(135deg, #06b6d4 0%, #0f766e 100%);
                box-shadow: 0 16px 28px rgba(14, 116, 144, 0.28);
            }

            .sidebar-link {
                display: grid;
                grid-template-columns: auto minmax(0, 1fr);
                align-items: center;
                gap: 0.75rem;
                padding: 0.85rem 1rem;
                color: var(--sidebar-link-color);
                text-decoration: none;
                border-radius: 1rem;
                font-weight: 600;
                border: 1px solid transparent;
                transition: .2s ease;
                min-width: 0;
            }

            .sidebar-link:hover,
            .sidebar-link.active {
                color: var(--text-main);
                background: linear-gradient(90deg, rgba(255, 59, 59, 0.16), rgba(255,255,255,0.03));
                border-color: rgba(255, 59, 59, 0.2);
                transform: translateX(2px);
            }

            body.cashier-layout .sidebar-link:hover,
            body.cashier-layout .sidebar-link.active {
                background: linear-gradient(90deg, rgba(34, 211, 238, 0.18), rgba(255,255,255,0.04));
                border-color: rgba(34, 211, 238, 0.22);
                box-shadow: inset 0 1px 0 rgba(255,255,255,0.04);
            }

            .sidebar-nav-group + .sidebar-nav-group {
                margin-top: 1rem;
            }

            .sidebar-nav-label {
                margin-bottom: .7rem;
                padding-left: .2rem;
                font-size: .72rem;
                text-transform: uppercase;
                letter-spacing: .12em;
                color: var(--sidebar-nav-label-color);
                font-weight: 800;
            }

            .sidebar-nav-list {
                display: grid;
                gap: .55rem;
            }

            .sidebar-link-label {
                min-width: 0;
                overflow-wrap: anywhere;
                line-height: 1.3;
            }

            .sidebar-dot {
                width: .65rem;
                height: .65rem;
                border-radius: 999px;
                background: linear-gradient(135deg, #ff8a8a 0%, #ff3b3b 100%);
                box-shadow: 0 0 0 4px rgba(255, 59, 59, 0.1);
            }

            body.cashier-layout .sidebar-dot {
                background: linear-gradient(135deg, #67e8f9 0%, #06b6d4 100%);
                box-shadow: 0 0 0 4px rgba(34, 211, 238, 0.14);
            }

            .topbar-card,
            .panel-card,
            .metric-card {
                background: var(--surface);
                border: 1px solid var(--border);
                box-shadow: var(--shadow);
                backdrop-filter: blur(18px);
            }

            .topbar-card { border-radius: 1.5rem; }
            .panel-card, .metric-card { border-radius: 1.25rem; }

            .hero-shell {
                min-height: 320px;
                position: relative;
                overflow: hidden;
            }

            .hero-shell::before {
                content: '';
                position: absolute;
                inset: auto auto -60px -60px;
                width: 260px;
                height: 260px;
                border-radius: 999px;
                background: radial-gradient(circle, rgba(255, 59, 59, 0.22) 0%, transparent 68%);
                pointer-events: none;
            }

            .hero-title {
                font-size: clamp(2.2rem, 4vw, 3.4rem);
                line-height: 1.05;
                letter-spacing: -.03em;
                max-width: 12ch;
            }

            .hero-copy {
                max-width: 48rem;
                font-size: 1.05rem;
                line-height: 1.7;
            }

            .hero-pills {
                display: flex;
                flex-wrap: wrap;
                gap: .75rem;
                margin-top: 1.5rem;
            }

            .hero-pill {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: .7rem 1rem;
                border-radius: 999px;
                border: 1px solid var(--hero-pill-border);
                background: var(--hero-pill-bg);
                color: var(--hero-pill-color);
                font-size: .92rem;
                font-weight: 600;
                text-decoration: none;
                transition: .2s ease;
            }

            .hero-pill:hover {
                color: var(--text-main);
                border-color: rgba(255, 59, 59, 0.25);
                background: linear-gradient(135deg, rgba(255, 59, 59, 0.18) 0%, rgba(255,255,255,0.05) 100%);
                transform: translateY(-1px);
            }

            .hero-pill.active {
                color: #fff;
                border-color: rgba(255, 59, 59, 0.34);
                background: linear-gradient(135deg, rgba(255, 59, 59, 0.26) 0%, rgba(123, 8, 23, 0.34) 100%);
                box-shadow: 0 14px 24px rgba(120, 9, 23, 0.22);
            }

            body.cashier-layout .hero-pill:hover {
                border-color: rgba(34, 211, 238, 0.3);
                background: linear-gradient(135deg, rgba(34, 211, 238, 0.18) 0%, rgba(16, 185, 129, 0.12) 100%);
            }

            body.cashier-layout .hero-pill.active {
                border-color: rgba(34, 211, 238, 0.34);
                background: linear-gradient(135deg, rgba(34, 211, 238, 0.28) 0%, rgba(16, 185, 129, 0.18) 100%);
                box-shadow: 0 14px 24px rgba(8, 145, 178, 0.22);
            }

            .hero-stat-card {
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                min-height: 122px;
                padding: 1.15rem 1.2rem;
                border: 1px solid rgba(255,255,255,0.08);
                border-radius: 1.25rem;
                background: var(--hero-stat-bg);
            }

            .hero-stat-value {
                font-size: clamp(1.7rem, 2vw, 2.25rem);
                font-weight: 800;
                line-height: 1.1;
                margin-top: .55rem;
            }

            .section-label {
                font-size: .78rem;
                text-transform: uppercase;
                letter-spacing: .18em;
                color: var(--text-muted);
                font-weight: 700;
            }

            .metric-icon {
                width: 3rem;
                height: 3rem;
                border-radius: 1rem;
                color: #fff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-family: 'Space Grotesk', sans-serif;
            }

            .icon-teal,
            .icon-orange,
            .icon-navy,
            .icon-green {
                background: linear-gradient(135deg, #ff5a5a 0%, #8f0013 100%);
                box-shadow: 0 18px 26px rgba(166, 15, 31, 0.35);
            }

            body.cashier-layout .icon-teal,
            body.cashier-layout .icon-orange,
            body.cashier-layout .icon-navy,
            body.cashier-layout .icon-green {
                background: linear-gradient(135deg, #06b6d4 0%, #0f766e 100%);
                box-shadow: 0 18px 26px rgba(8, 145, 178, 0.26);
            }

            .list-card {
                border: 1px solid var(--border);
                border-radius: 1rem;
                background: var(--surface-soft);
            }

            .mini-progress {
                height: .55rem;
                background: rgba(255,255,255,0.08);
                border-radius: 999px;
                overflow: hidden;
            }

            .mini-progress-bar {
                height: 100%;
                border-radius: 999px;
                background: linear-gradient(90deg, #ff6a6a 0%, #ff3b3b 48%, #a60f1f 100%);
            }

            .status-badge {
                font-size: .75rem;
                font-weight: 700;
                border-radius: 999px;
                padding: .45rem .8rem;
            }

            .table-person {
                display: flex;
                align-items: center;
                gap: .75rem;
                min-width: 0;
            }

            .table-avatar {
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 50%;
                object-fit: cover;
                flex: 0 0 auto;
                border: 1px solid rgba(255,255,255,0.12);
                background: rgba(255,255,255,0.05);
            }

            .table-avatar-placeholder {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 50%;
                flex: 0 0 auto;
                background: linear-gradient(135deg, #ff6666 0%, #8f0013 100%);
                color: #fff;
                font-size: .82rem;
                font-weight: 800;
                letter-spacing: .04em;
            }

            .table-person-text {
                min-width: 0;
            }

            .badge-soft-teal {
                background: rgba(255, 59, 59, 0.14);
                color: #ff9b9b;
            }

            body.cashier-layout .badge-soft-teal,
            body.cashier-layout .badge-soft-green {
                background: rgba(34, 211, 238, 0.14);
                color: #cffafe;
            }

            .badge-soft-green {
                background: rgba(255, 59, 59, 0.14);
                color: #ff9b9b;
            }

            .dark-panel {
                background:
                    linear-gradient(180deg, rgba(34, 9, 11, 0.96) 0%, rgba(10, 10, 10, 0.98) 100%);
                color: #fff;
                border: 1px solid var(--border-strong);
                box-shadow: inset 0 1px 0 rgba(255,255,255,0.04);
            }

            .muted-copy {
                color: var(--text-muted);
            }

            .dark-panel .section-label,
            .dark-panel .muted-copy {
                color: rgba(255,255,255,.68);
            }

            .table > :not(caption) > * > * {
                padding: 1rem;
                border-bottom-color: var(--border);
                vertical-align: middle;
                background: transparent;
                color: var(--text-main);
            }

            .table thead th {
                font-size: .76rem;
                text-transform: uppercase;
                letter-spacing: .1em;
                color: var(--text-muted);
                font-weight: 800;
                background: var(--table-head-bg);
            }

            .modal-content {
                background: var(--modal-bg);
                color: var(--text-main);
                border: 1px solid var(--modal-border) !important;
                border-radius: 1.25rem;
                box-shadow: var(--shadow);
            }

            .modal-header,
            .modal-footer {
                border-color: var(--modal-border);
            }

            .modal-title,
            .form-label {
                color: var(--text-main);
                font-weight: 700;
            }

            .text-secondary {
                color: var(--text-muted) !important;
            }

            .form-control,
            .form-select,
            textarea.form-control {
                background: var(--field-bg);
                border: 1px solid var(--field-border);
                color: var(--text-main);
                border-radius: .9rem;
            }

            .form-control::placeholder,
            textarea.form-control::placeholder {
                color: var(--field-placeholder);
            }

            .form-control:focus,
            .form-select:focus,
            textarea.form-control:focus {
                background: var(--field-focus-bg);
                border-color: rgba(255, 59, 59, 0.45);
                color: var(--text-main);
                box-shadow: 0 0 0 .2rem rgba(255, 59, 59, 0.12);
            }

            .form-select option {
                background: var(--surface-soft);
                color: var(--text-main);
            }

            .form-control:disabled,
            .form-select:disabled {
                background: var(--field-disabled-bg);
                color: var(--field-disabled-color);
                border-color: var(--border);
                opacity: 1;
            }

            .btn-close {
                filter: invert(1) grayscale(1) brightness(1.4);
            }

            html[data-theme="light"] .btn-close {
                filter: none;
            }

            .btn-dark,
            .btn-outline-light:hover {
                background: linear-gradient(135deg, #ff4c4c 0%, #b10018 100%);
                border-color: #d62839;
                color: #fff;
                box-shadow: 0 18px 28px rgba(177, 0, 24, 0.35);
            }

            body.cashier-layout .btn-dark,
            body.cashier-layout .btn-outline-light:hover {
                background: linear-gradient(135deg, #06b6d4 0%, #0f766e 100%);
                border-color: #0891b2;
                box-shadow: 0 18px 28px rgba(8, 145, 178, 0.28);
            }

            body.cashier-layout main {
                max-width: 1480px;
                margin: 0 auto;
            }

            body.cashier-layout .topbar-card,
            body.cashier-layout .panel-card,
            body.cashier-layout .metric-card,
            body.cashier-layout .list-card,
            body.cashier-layout .hero-stat-card {
                box-shadow:
                    0 20px 42px rgba(2, 23, 33, 0.22),
                    inset 0 1px 0 rgba(255,255,255,0.03);
            }

            body.cashier-layout .topbar-card,
            body.cashier-layout .hero-stat-card {
                border-color: var(--border-strong);
            }

            body.cashier-layout .panel-card,
            body.cashier-layout .metric-card,
            body.cashier-layout .list-card {
                background:
                    linear-gradient(180deg, rgba(14, 38, 49, 0.98) 0%, rgba(9, 27, 36, 0.98) 100%);
            }

            html[data-theme="light"] body.cashier-layout .panel-card,
            html[data-theme="light"] body.cashier-layout .metric-card,
            html[data-theme="light"] body.cashier-layout .list-card {
                background:
                    linear-gradient(180deg, rgba(255,255,255,1) 0%, rgba(241,251,255,1) 100%);
            }

            body.cashier-layout .table-responsive {
                border: 1px solid var(--border);
                border-radius: 1rem;
                overflow: hidden;
                background: rgba(255,255,255,0.02);
            }

            body.cashier-layout .table tbody tr:hover {
                background: rgba(34, 211, 238, 0.06);
            }

            html[data-theme="light"] body.cashier-layout .table tbody tr:hover {
                background: rgba(8, 145, 178, 0.05);
            }

            body.cashier-layout .table thead th {
                color: #cffafe;
                font-size: .74rem;
            }

            html[data-theme="light"] body.cashier-layout .table thead th {
                color: #0f5f78;
            }

            body.cashier-layout .form-label {
                color: #f0fdff;
            }

            html[data-theme="light"] body.cashier-layout .form-label {
                color: #0f2430;
            }

            body.cashier-layout .form-control,
            body.cashier-layout .form-select,
            body.cashier-layout textarea.form-control {
                min-height: 3rem;
            }

            body.cashier-layout .form-control:focus,
            body.cashier-layout .form-select:focus,
            body.cashier-layout textarea.form-control:focus {
                border-color: rgba(34, 211, 238, 0.5);
                box-shadow: 0 0 0 .22rem rgba(34, 211, 238, 0.14);
            }

            body.cashier-layout .btn-outline-secondary {
                border-color: rgba(103, 232, 249, 0.24);
                color: #e6fbff;
                background: rgba(255,255,255,0.03);
            }

            body.cashier-layout .btn-outline-secondary:hover {
                border-color: rgba(34, 211, 238, 0.3);
                background: rgba(34, 211, 238, 0.12);
                color: #ffffff;
            }

            html[data-theme="light"] body.cashier-layout .btn-outline-secondary {
                border-color: rgba(8, 145, 178, 0.18);
                color: #0f5f78;
                background: rgba(8, 145, 178, 0.04);
            }

            html[data-theme="light"] body.cashier-layout .btn-outline-secondary:hover {
                background: rgba(8, 145, 178, 0.1);
                color: #083344;
            }

            body.cashier-layout .btn-outline-light {
                border-color: rgba(103, 232, 249, 0.22);
                color: #e6fbff;
                background: rgba(255,255,255,0.02);
            }

            body.cashier-layout .alert-success {
                background: rgba(16, 185, 129, 0.16);
                color: #d1fae5;
                border: 1px solid rgba(16, 185, 129, 0.22);
            }

            body.cashier-layout .alert-danger {
                background: rgba(239, 68, 68, 0.16);
                color: #fee2e2;
                border: 1px solid rgba(239, 68, 68, 0.22);
            }

            .btn-dark:hover {
                filter: brightness(1.05);
            }

            .btn-outline-light {
                border-color: rgba(255,255,255,0.16);
                color: var(--text-main);
            }

            .topbar-utility {
                display: flex;
                justify-content: flex-end;
                margin-bottom: .75rem;
                padding-left: 4rem;
            }

            .theme-toggle {
                display: inline-flex;
                align-items: center;
                border-radius: 999px;
                border: 1px solid var(--toggle-border);
                background: var(--toggle-bg);
                color: var(--text-main);
                padding: .38rem;
                font-weight: 600;
            }

            .theme-toggle:hover {
                border-color: rgba(255, 59, 59, 0.25);
                color: var(--text-main);
            }

            .theme-toggle-track {
                position: relative;
                width: 2.35rem;
                height: 1.35rem;
                border-radius: 999px;
                background: rgba(255, 59, 59, 0.18);
                border: 1px solid rgba(255, 59, 59, 0.2);
                transition: .2s ease;
            }

            .theme-toggle-thumb {
                position: absolute;
                top: 50%;
                left: .18rem;
                width: .9rem;
                height: .9rem;
                border-radius: 50%;
                background: linear-gradient(135deg, #ff4c4c 0%, #b10018 100%);
                transform: translateY(-50%);
                box-shadow: 0 8px 18px rgba(177, 0, 24, 0.28);
                transition: left .2s ease, background .2s ease, box-shadow .2s ease;
            }

            html[data-theme="light"] .theme-toggle-track {
                background: rgba(15, 23, 42, 0.08);
                border-color: rgba(15, 23, 42, 0.12);
            }

            html[data-theme="light"] .theme-toggle-thumb {
                left: 1.18rem;
                background: linear-gradient(135deg, #1f2937 0%, #475569 100%);
                box-shadow: 0 8px 18px rgba(15, 23, 42, 0.16);
            }

            @media (max-width: 991.98px) {
                .topbar-utility {
                    padding-left: 3.4rem;
                }
            }

            body.page-cashier-dashboard .topbar-utility {
                display: none;
            }

            html[data-theme="light"] .text-white-50 {
                color: rgba(24,24,27,0.55) !important;
            }

            html[data-theme="light"] .sidebar::before {
                background: radial-gradient(circle at top left, rgba(255, 59, 59, 0.12), transparent 60%);
            }

            html[data-theme="light"] .badge-soft-teal,
            html[data-theme="light"] .badge-soft-green {
                background: rgba(214, 40, 57, 0.1);
                color: #b42334;
            }

            html[data-theme="light"] .alert-success {
                background: rgba(34, 197, 94, 0.12);
                color: #166534;
            }

            html[data-theme="light"] .alert-danger {
                background: rgba(239, 68, 68, 0.12);
                color: #991b1b;
            }

            .text-warning-emphasis {
                color: #ff8a65 !important;
            }

            .container-fluid,
            .row,
            aside,
            main {
                position: relative;
            }

            .modal {
                z-index: 2000;
            }

            .modal-backdrop {
                z-index: 1990;
            }

            @media (min-width: 992px) and (max-width: 1440px) {
                .container-fluid {
                    padding-left: 1rem;
                    padding-right: 1rem;
                }

                .sidebar {
                    border-radius: 1.2rem;
                }

                .sidebar.p-lg-4 {
                    padding: 1rem !important;
                }

                .brand-mark {
                    width: 4.4rem;
                    height: 3rem;
                    border-radius: 1rem;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    padding: 0 0.6rem;
                    background: linear-gradient(135deg, #ff2323 0%, #1d0909 100%);
                    font-weight: 800;
                    font-family: 'Space Grotesk', sans-serif;
                    color: #fff;
                    letter-spacing: 0.06em;
                    text-transform: uppercase;
                    font-size: 0.8rem;
                    box-shadow: 0 18px 32px rgba(255, 59, 59, 0.28);
                    border: 1px solid rgba(255,255,255,0.08);
                }

                .sidebar-link {
                    gap: .55rem;
                    padding: .78rem .82rem;
                    font-size: .92rem;
                    line-height: 1.25;
                    border-radius: .9rem;
                }

                .sidebar-dot {
                    width: .52rem;
                    height: .52rem;
                }

                .topbar-card {
                    border-radius: 1.2rem;
                }

                .panel-card,
                .metric-card,
                .hero-stat-card {
                    border-radius: 1.05rem;
                }

                .hero-shell {
                    min-height: 280px;
                }

                .hero-title {
                    font-size: clamp(1.95rem, 2.5vw, 2.7rem);
                    max-width: 15ch;
                }

                .hero-copy {
                    font-size: .95rem;
                    line-height: 1.55;
                    max-width: 42rem;
                }

                .hero-pills {
                    gap: .55rem;
                    margin-top: 1rem;
                }

                .hero-pill {
                    padding: .58rem .85rem;
                    font-size: .82rem;
                }

                .hero-stat-card {
                    min-height: 108px;
                    padding: 1rem;
                }

                .hero-stat-value {
                    font-size: clamp(1.35rem, 1.8vw, 1.9rem);
                }

                .metric-card.p-4,
                .panel-card.p-4,
                .dark-panel.p-4 {
                    padding: 1rem !important;
                }

                .metric-icon {
                    width: 2.5rem;
                    height: 2.5rem;
                    border-radius: .85rem;
                    font-size: .9rem;
                }

                .section-label {
                    font-size: .68rem;
                    letter-spacing: .14em;
                }

                .status-badge {
                    font-size: .68rem;
                    padding: .35rem .6rem;
                }

                .fs-2 {
                    font-size: calc(1.1rem + .8vw) !important;
                }

                .h3,
                .h4,
                h3,
                h4 {
                    line-height: 1.2;
                }

                .table > :not(caption) > * > * {
                    padding: .7rem .75rem;
                    font-size: .9rem;
                    line-height: 1.35;
                }

                .table thead th {
                    font-size: .67rem;
                    letter-spacing: .08em;
                }

                .table-responsive {
                    overflow-x: auto;
                }

                .btn {
                    font-size: .88rem;
                }

                .btn.rounded-pill,
                .btn-sm.rounded-pill {
                    padding-top: .55rem;
                    padding-bottom: .55rem;
                }

                .rounded-4 {
                    border-radius: 1rem !important;
                }

                .small {
                    font-size: .81rem;
                }
            }

            @media (min-width: 992px) and (max-width: 1200px) {
                .hero-title {
                    font-size: 1.85rem;
                }

                .hero-copy {
                    font-size: .9rem;
                }

                .sidebar-link {
                    font-size: .88rem;
                }
            }

            @media (max-width: 991.98px) {
                .sidebar-column {
                    display: block;
                }

                .sidebar {
                    width: min(340px, calc(100vw - 1.25rem));
                    left: .625rem;
                    top: .625rem;
                    bottom: .625rem;
                }

                .sidebar-column.cashier-sidebar .sidebar,
                .sidebar-column.cashier-sidebar .sidebar-stack {
                    min-height: auto;
                }

                .hero-shell {
                    min-height: auto;
                }

                .sidebar-inner {
                    gap: 1rem;
                }

                .sidebar-toggle {
                    left: .625rem;
                }

                body.sidebar-open .sidebar-toggle {
                    left: min(340px, calc(100vw - 1.25rem));
                    transform: translate(.35rem, -50%);
                }
            }
        </style>
    </head>
    @php
        $authRole = session('auth.role');
        $isMasterAdmin = $authRole === 'master_admin';
        $isCashierArea = str_starts_with(($activePage ?? 'dashboard'), 'cashier');
    @endphp
    <body class="sidebar-hidden {{ $isCashierArea ? 'cashier-layout' : 'admin-layout' }} page-{{ str_replace('.', '-', $activePage ?? 'dashboard') }}">
        @php
            $adminNavigation = [
                ['key' => 'dashboard', 'label' => 'Dashboard', 'route' => route('admin.dashboard')],
                ['key' => 'members', 'label' => 'Member', 'route' => route('admin.members')],
                ['key' => 'non-members', 'label' => 'Non Member', 'route' => route('admin.non-members')],
                ['key' => 'products', 'label' => 'Produk', 'route' => route('admin.products')],
                ['key' => 'announcements', 'label' => 'Pengumuman', 'route' => route('admin.announcements')],
                ['key' => 'reports', 'label' => 'Laporan', 'route' => route('admin.reports')],
            ];
            $cashierNavigation = [
                ['key' => 'cashier.dashboard', 'label' => 'Dashboard Kasir', 'route' => route('cashier.dashboard')],
                ['key' => 'cashier.checkins', 'label' => 'Check-in', 'route' => route('cashier.checkins')],
                ['key' => 'cashier.transactions', 'label' => 'Pembayaran', 'route' => route('cashier.transactions')],
                ['key' => 'cashier.receipts', 'label' => 'Verifikasi & Bukti', 'route' => route('cashier.receipts')],
            ];
            $navigationGroups = $isMasterAdmin
                ? [
                    ['label' => 'Admin', 'items' => $adminNavigation],
                    ['label' => 'Kasir', 'items' => $cashierNavigation],
                ]
                : [
                    [
                        'label' => $isCashierArea ? 'Kasir' : 'Admin',
                        'items' => $isCashierArea ? $cashierNavigation : $adminNavigation,
                    ],
                ];
        @endphp
        <div class="container-fluid py-3 py-lg-4">
            <button type="button" class="btn sidebar-toggle" id="sidebarToggle" aria-label="Buka navigasi" aria-expanded="false" aria-controls="adminSidebar">
                <span class="sidebar-toggle-icon" id="sidebarToggleIcon">›</span>
            </button>
            <div class="sidebar-overlay" id="sidebarOverlay"></div>
            <div class="row g-3 g-lg-4">
                <aside class="col-12 col-lg-3 col-xl-3 sidebar-column {{ $isCashierArea ? 'cashier-sidebar' : '' }}">
                    <div class="sidebar p-3 p-lg-4" id="adminSidebar">
                        <div class="sidebar-stack sidebar-inner">
                            <div class="sidebar-main">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div class="brand-mark">Arena</div>
                                    <div>
                                        <div class="fw-bold">Arena Gym</div>
                                        <div class="small text-white-50">
                                            {{ $isMasterAdmin ? 'Master Admin Access' : ($isCashierArea ? 'Kasir Dashboard' : 'Admin Dashboard') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="small text-uppercase text-white-50 fw-bold mb-3" style="letter-spacing:.12em;">Navigation</div>
                                @foreach ($navigationGroups as $group)
                                    <div class="sidebar-nav-group">
                                        <div class="sidebar-nav-label">{{ $group['label'] }}</div>
                                        <nav class="sidebar-nav-list">
                                            @foreach ($group['items'] as $item)
                                                @php
                                                    $isActive = $item['key'] === 'cashier.receipts'
                                                        ? in_array(($activePage ?? ''), ['cashier.verifications', 'cashier.receipts'], true)
                                                        : ($item['key'] === 'cashier.transactions'
                                                            ? in_array(($activePage ?? ''), ['cashier.transactions', 'cashier.member-payments', 'cashier.daily-payments'], true)
                                                            : (($activePage ?? 'dashboard') === $item['key']));
                                                @endphp
                                                <a class="sidebar-link {{ $isActive ? 'active' : '' }}" href="{{ $item['route'] }}">
                                                    <span class="sidebar-dot"></span>
                                                    <span class="sidebar-link-label">{{ $item['label'] }}</span>
                                                </a>
                                            @endforeach
                                        </nav>
                                    </div>
                                @endforeach
                            </div>

                            <div class="sidebar-bottom">
                            @if (! empty($sidebarExtraSummary))
                                <div class="sidebar-extra-card p-3">
                                    <div class="section-label text-white-50">{{ $sidebarExtraSummary['label'] ?? 'Ringkasan' }}</div>
                                    <div class="h4 fw-bold mt-3 mb-1">{{ $sidebarExtraSummary['title'] ?? '' }}</div>
                                    <div class="small muted-copy">{{ $sidebarExtraSummary['note'] ?? '' }}</div>
                                </div>
                            @endif

                            @if (! empty($sidebarExtraItems))
                                <div class="sidebar-extra-card p-3">
                                    <div class="section-label text-white-50 mb-3">{{ $sidebarExtraItemsTitle ?? 'Prioritas' }}</div>
                                    <div class="d-grid gap-3">
                                        @foreach ($sidebarExtraItems as $item)
                                            <div>
                                                <div class="fw-semibold">{{ $item['title'] ?? '' }}</div>
                                                <div class="small muted-copy mt-1">{{ $item['note'] ?? '' }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="dark-panel rounded-4 p-3">
                                <div class="section-label text-white-50">Status Hari Ini</div>
                                <div class="h4 fw-bold mt-3 mb-1">{{ $sidebarStatusTitle ?? 'Operasional Stabil' }}</div>
                                <div class="small muted-copy">{{ $sidebarStatusNote ?? 'Pemantauan admin berjalan normal.' }}</div>
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-light w-100 rounded-pill">Logout</button>
                            </form>
                            </div>
                        </div>
                    </div>
                </aside>

                <main class="col-12">
                    @if (session('status'))
                        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-3">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-3">
                            <div class="fw-semibold mb-1">Data belum tersimpan.</div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const root = document.documentElement;
                const body = document.body;
                const toggleButton = document.getElementById('sidebarToggle');
                const toggleIcon = document.getElementById('sidebarToggleIcon');
                const overlay = document.getElementById('sidebarOverlay');
                const themeToggles = document.querySelectorAll('[data-theme-toggle]');

                const syncThemeToggle = () => {
                    const theme = root.getAttribute('data-theme') || 'dark';
                    themeToggles.forEach((themeToggle) => {
                        themeToggle.setAttribute('aria-label', theme === 'dark' ? 'Aktifkan light mode' : 'Aktifkan dark mode');
                    });
                };

                const syncSidebarState = () => {
                    const isOpen = body.classList.contains('sidebar-open');

                    toggleButton?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    toggleButton?.setAttribute('aria-label', isOpen ? 'Sembunyikan navigasi' : 'Buka navigasi');

                    if (toggleIcon) {
                        toggleIcon.textContent = isOpen ? '‹' : '›';
                    }
                };

                themeToggles.forEach((themeToggle) => {
                    themeToggle.addEventListener('click', function () {
                        const nextTheme = (root.getAttribute('data-theme') || 'dark') === 'dark' ? 'light' : 'dark';
                        root.setAttribute('data-theme', nextTheme);
                        localStorage.setItem('arena-gym-theme', nextTheme);
                        syncThemeToggle();
                    });
                });

                const openSidebar = () => {
                    body.classList.add('sidebar-open');
                    syncSidebarState();
                };

                const closeSidebar = () => {
                    body.classList.remove('sidebar-open');
                    syncSidebarState();
                };

                toggleButton?.addEventListener('click', function () {
                    if (body.classList.contains('sidebar-open')) {
                        closeSidebar();
                        return;
                    }

                    openSidebar();
                });

                overlay?.addEventListener('click', closeSidebar);

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeSidebar();
                    }
                });

                syncThemeToggle();
                syncSidebarState();
            });
        </script>
    </body>
</html>
