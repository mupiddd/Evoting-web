<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VoteForge — Sistem E-Voting Digital')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-deep: #030b17;
            --bg-navy: #071936;
            --bg-sapphire: #0c2b59;
            --glass-bg: rgba(13, 38, 76, 0.45);
            --glass-bg-strong: rgba(10, 29, 59, 0.72);
            --glass-border: rgba(125, 211, 252, 0.28);
            --glass-border-subtle: rgba(148, 163, 184, 0.18);
            --accent-cyan: #38bdf8;
            --accent-blue: #2563eb;
            --accent-indigo: #818cf8;
            --accent-purple: #a78bfa;
            --text-primary: #f8fafc;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --success: #34d399;
            --danger: #fb7185;
            --warning: #fbbf24;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, sans-serif;
            background-color: var(--bg-deep);
            background-image:
                radial-gradient(circle at 18% 22%, rgba(37, 99, 235, 0.36) 0%, transparent 45%),
                radial-gradient(circle at 76% 35%, rgba(56, 189, 248, 0.28) 0%, transparent 48%),
                radial-gradient(circle at 52% 85%, rgba(14, 116, 144, 0.3) 0%, transparent 50%),
                linear-gradient(135deg, #020813 0%, #071a38 48%, #041022 100%);
            color: var(--text-primary);
            min-height: 100dvh;
            line-height: 1.6;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient bokeh orbs matching reference artwork */
        .ambient-canvas {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .bokeh-orb {
            position: absolute;
            border-radius: 9999px;
            filter: blur(45px);
            opacity: 0.45;
        }

        .bokeh-1 {
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.45), transparent 70%);
            top: -90px;
            left: 25%;
        }

        .bokeh-2 {
            width: 460px;
            height: 460px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.5), transparent 70%);
            top: 18%;
            right: 5%;
        }

        .bokeh-3 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(129, 140, 248, 0.3), transparent 70%);
            bottom: -60px;
            left: 10%;
        }

        .sparkle-dots {
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(2px 2px at 15% 25%, rgba(186, 230, 253, 0.7), transparent),
                radial-gradient(2.5px 2.5px at 46% 18%, rgba(255, 255, 255, 0.85), transparent),
                radial-gradient(2px 2px at 82% 28%, rgba(125, 211, 252, 0.75), transparent),
                radial-gradient(3px 3px at 68% 72%, rgba(56, 189, 248, 0.65), transparent),
                radial-gradient(2px 2px at 28% 78%, rgba(186, 230, 253, 0.55), transparent);
            opacity: 0.7;
        }

        /* Main Stage Wrapper */
        .stage-shell {
            position: relative;
            z-index: 1;
            max-width: 1280px;
            margin: 0 auto;
            padding: 1.5rem 1.5rem 2.5rem;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Top Glass Navbar matching PixelForge reference */
        .top-navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.85rem 1.25rem;
            border-radius: 1.25rem;
            background: rgba(7, 25, 54, 0.45);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(125, 211, 252, 0.18);
            margin-block-end: 2rem;
            flex-wrap: wrap;
        }

        .brand-link {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 700;
            font-size: 1.2rem;
            letter-spacing: -0.02em;
        }

        .brand-orb {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1d4ed8 0%, #38bdf8 55%, #e0f2fe 100%);
            box-shadow: 0 0 18px rgba(56, 189, 248, 0.65), inset -4px -4px 8px rgba(15, 23, 42, 0.6);
            position: relative;
            display: inline-block;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 1.75rem;
            list-style: none;
            flex-wrap: wrap;
        }

        .nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 500;
            padding: 0.35rem 0.15rem;
            position: relative;
            transition: color 0.2s ease;
        }

        .nav-link:hover,
        .nav-link[aria-current="page"] {
            color: #ffffff;
        }

        .nav-link[aria-current="page"]::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -3px;
            height: 2px;
            border-radius: 2px;
            background: linear-gradient(90deg, #38bdf8, #818cf8);
            box-shadow: 0 0 10px rgba(56, 189, 248, 0.9);
        }

        .nav-cta {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 1.2rem;
            border-radius: 9999px;
            font-size: 0.88rem;
            font-weight: 600;
            color: #ffffff;
            text-decoration: none;
            background: rgba(56, 189, 248, 0.16);
            border: 1px solid rgba(125, 211, 252, 0.45);
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.2), inset 0 1px 1px rgba(255, 255, 255, 0.35);
            transition: all 0.25s ease;
        }

        .nav-cta:hover {
            background: rgba(56, 189, 248, 0.28);
            border-color: rgba(186, 230, 253, 0.75);
            transform: translateY(-1px);
            box-shadow: 0 0 25px rgba(56, 189, 248, 0.4);
        }

        /* Frosted Glass Panel with Specular Top Edge Flare */
        .glass-panel {
            position: relative;
            background: linear-gradient(145deg, rgba(18, 52, 99, 0.52) 0%, rgba(8, 25, 52, 0.62) 100%);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(125, 211, 252, 0.32);
            border-radius: 1.75rem;
            box-shadow:
                0 25px 50px -12px rgba(2, 8, 23, 0.75),
                0 0 35px rgba(56, 189, 248, 0.12),
                inset 0 1px 1px rgba(255, 255, 255, 0.3);
            padding: 2.5rem;
        }

        /* Specular top light flare exactly like PixelForge card */
        .glass-panel::before {
            content: "";
            position: absolute;
            top: -1px;
            left: 18%;
            right: 18%;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(186, 230, 253, 0.95), #ffffff, rgba(56, 189, 248, 0.9), transparent);
            box-shadow: 0 0 18px 2px rgba(125, 211, 252, 0.85);
            border-radius: 999px;
            pointer-events: none;
        }

        /* Eyebrow label with trailing line */
        .eyebrow-label {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.22em;
            color: #93c5fd;
            margin-block-end: 0.9rem;
        }

        .eyebrow-label::after {
            content: "";
            display: inline-block;
            width: 48px;
            height: 1px;
            background: linear-gradient(90deg, rgba(147, 197, 253, 0.7), transparent);
        }

        /* Display Typography */
        .hero-title {
            font-size: clamp(2rem, 4vw, 3.1rem);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.025em;
            margin-block-end: 1.1rem;
            color: #ffffff;
        }

        .gradient-text {
            display: block;
            background: linear-gradient(90deg, #38bdf8 0%, #60a5fa 48%, #a78bfa 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .subtitle-text {
            color: var(--text-secondary);
            font-size: 0.98rem;
            max-width: 52ch;
            margin-block-end: 1.75rem;
        }

        /* Buttons */
        .btn-pill-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            padding: 0.85rem 1.75rem;
            min-height: 48px;
            border-radius: 9999px;
            border: 1px solid rgba(186, 230, 253, 0.5);
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 55%, #38bdf8 100%);
            color: #ffffff;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.55), inset 0 1px 1px rgba(255, 255, 255, 0.45);
            transition: all 0.25s ease;
        }

        .btn-pill-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px -5px rgba(56, 189, 248, 0.75), inset 0 1px 2px rgba(255, 255, 255, 0.65);
        }

        .btn-pill-primary:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
        }

        .btn-glass-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            padding: 0.75rem 1.35rem;
            min-height: 48px;
            border-radius: 9999px;
            border: 1px solid rgba(125, 211, 252, 0.32);
            background: rgba(15, 42, 82, 0.48);
            color: var(--text-primary);
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-glass-secondary:hover {
            background: rgba(30, 64, 118, 0.65);
            border-color: rgba(125, 211, 252, 0.65);
        }

        .btn-play-link {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 600;
            padding: 0.35rem 0.5rem;
        }

        .play-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(15, 42, 82, 0.65);
            border: 1px solid rgba(125, 211, 252, 0.45);
            box-shadow: 0 0 16px rgba(56, 189, 248, 0.25);
            transition: all 0.2s ease;
        }

        .btn-play-link:hover .play-circle {
            background: rgba(56, 189, 248, 0.25);
            transform: scale(1.06);
        }

        /* Form Controls (following modern-web-guidance forms) */
        .form-group {
            margin-block-end: 1.35rem;
        }

        .form-label {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            color: #e2e8f0;
            margin-block-end: 0.45rem;
        }

        .glass-input,
        .glass-textarea,
        .glass-select {
            width: 100%;
            font-family: inherit;
            font-size: 1rem;
            min-height: 48px;
            padding: 0.75rem 1.1rem;
            border-radius: 0.95rem;
            background: rgba(4, 15, 35, 0.62);
            border: 1px solid rgba(125, 211, 252, 0.32);
            color: #ffffff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .glass-input::placeholder,
        .glass-textarea::placeholder {
            color: rgba(148, 163, 184, 0.65);
        }

        .glass-input:focus-visible,
        .glass-textarea:focus-visible,
        .glass-select:focus-visible,
        button:focus-visible,
        a:focus-visible {
            outline: 3px solid var(--accent-cyan);
            outline-offset: 2px;
        }

        .glass-input:invalid:user-invalid,
        .glass-textarea:invalid:user-invalid {
            border-color: var(--danger);
            box-shadow: 0 0 0 3px rgba(251, 113, 133, 0.25);
        }

        .form-hint {
            display: block;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-block-start: 0.35rem;
        }

        /* Visually hidden utility (canonical clip-path recipe from forms guide) */
        .visually-hidden {
            position: absolute;
            clip-path: inset(50%);
            overflow: hidden;
            width: 1px;
            height: 1px;
            margin: -1px;
            padding: 0;
            border: 0;
            white-space: nowrap;
        }

        /* Alerts */
        .glass-alert {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.9rem 1.15rem;
            border-radius: 1rem;
            margin-block-end: 1.35rem;
            font-size: 0.92rem;
            font-weight: 500;
            backdrop-filter: blur(12px);
        }

        .glass-alert-error {
            background: rgba(159, 18, 57, 0.42);
            border: 1px solid rgba(251, 113, 133, 0.55);
            color: #ffe4e6;
        }

        .glass-alert-success {
            background: rgba(6, 95, 70, 0.45);
            border: 1px solid rgba(52, 211, 153, 0.55);
            color: #d1fae5;
        }

        /* Mini 4-column Feature Bar inside Hero Card */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.85rem;
            padding-block-start: 1.5rem;
            margin-block-start: 1.75rem;
            border-block-start: 1px solid rgba(125, 211, 252, 0.18);
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .feature-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 0.65rem;
            background: rgba(56, 189, 248, 0.14);
            border: 1px solid rgba(125, 211, 252, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-cyan);
            flex-shrink: 0;
        }

        .feature-meta strong {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: #f1f5f9;
            line-height: 1.2;
        }

        .feature-meta span {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        /* Modal Dialog with ::backdrop (light-dismiss-a-dialog guide) */
        dialog.glass-dialog {
            margin: auto;
            width: min(92vw, 480px);
            background: linear-gradient(155deg, rgba(12, 38, 78, 0.95) 0%, rgba(5, 18, 40, 0.98) 100%);
            border: 1px solid rgba(125, 211, 252, 0.45);
            border-radius: 1.5rem;
            color: var(--text-primary);
            padding: 2rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.85), 0 0 40px rgba(56, 189, 248, 0.25);
        }

        dialog.glass-dialog::backdrop {
            background-color: rgba(2, 8, 23, 0.72);
            backdrop-filter: blur(6px);
        }

        /* Responsive Table (following responsive-table guide) */
        .table-wrapper {
            overflow: auto;
            max-inline-size: 100%;
            max-block-size: min(540px, 80vh);
            container-type: inline-size;
            border-radius: 1rem;
            border: 1px solid rgba(125, 211, 252, 0.22);
            background: rgba(4, 15, 35, 0.45);
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            text-align: left;
            font-size: 0.92rem;
        }

        .data-table thead {
            position: sticky;
            inset-block-start: 0;
            z-index: 2;
            background: rgba(10, 31, 68, 0.96);
            backdrop-filter: blur(12px);
        }

        .data-table th,
        .data-table td {
            padding: 0.9rem 1.15rem;
            border-block-end: 1px solid rgba(125, 211, 252, 0.12);
        }

        .data-table thead th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #93c5fd;
            font-weight: 700;
        }

        .data-table tbody th {
            position: sticky;
            inset-inline-start: 0;
            background: rgba(7, 24, 52, 0.95);
            font-weight: 700;
            color: #ffffff;
        }

        .data-table tbody tr:hover td {
            background: rgba(56, 189, 248, 0.06);
        }

        @container (width < 600px) {
            .data-table,
            .data-table thead,
            .data-table tbody,
            .data-table tr,
            .data-table th,
            .data-table td {
                display: block;
            }

            .data-table thead {
                display: none;
            }

            .data-table tr {
                margin-block-end: 1rem;
                border: 1px solid rgba(125, 211, 252, 0.25);
                border-radius: 0.85rem;
                overflow: hidden;
            }

            .data-table tbody th {
                position: sticky;
                inset-block-start: 0;
                z-index: 2;
                background: rgba(14, 42, 88, 0.95);
            }

            .data-table td:nth-child(2)::before {
                content: var(--label-2) ": " / var(--label-2);
                font-weight: 600;
                color: #93c5fd;
            }

            .data-table td:nth-child(3)::before {
                content: var(--label-3) ": " / var(--label-3);
                font-weight: 600;
                color: #93c5fd;
            }

            .data-table td:nth-child(4)::before {
                content: var(--label-4) ": " / var(--label-4);
                font-weight: 600;
                color: #93c5fd;
            }

            .data-table td:nth-child(5)::before {
                content: var(--label-5) ": " / var(--label-5);
                font-weight: 600;
                color: #93c5fd;
            }
        }

        /* Stage Footer Bar matching reference image bottom corners */
        .stage-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding-block-start: 2rem;
            font-size: 0.78rem;
            color: var(--text-muted);
            flex-wrap: wrap;
        }

        .scroll-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            color: var(--text-secondary);
        }

        .mouse-pill {
            width: 20px;
            height: 30px;
            border-radius: 999px;
            border: 1.5px solid rgba(147, 197, 253, 0.6);
            display: inline-flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 5px;
        }

        .mouse-pill::after {
            content: "";
            width: 3px;
            height: 6px;
            border-radius: 99px;
            background: var(--accent-cyan);
        }

        .tagline-spaced {
            letter-spacing: 0.25em;
            text-transform: uppercase;
            font-weight: 600;
            color: rgba(186, 230, 253, 0.75);
        }

        @media (max-width: 900px) {
            .feature-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .glass-panel {
                padding: 1.75rem;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="ambient-canvas" aria-hidden="true">
        <div class="bokeh-orb bokeh-1"></div>
        <div class="bokeh-orb bokeh-2"></div>
        <div class="bokeh-orb bokeh-3"></div>
        <div class="sparkle-dots"></div>
    </div>

    <div class="stage-shell">
        <header>
            <nav class="top-navbar" aria-label="Navigasi Utama">
                <a href="{{ route('vote.token') }}" class="brand-link">
                    <span class="brand-orb" aria-hidden="true"></span>
                    <span>VoteForge</span>
                </a>

                <ul class="nav-menu">
                    <li>
                        <a href="{{ route('vote.token') }}"
                           class="nav-link"
                           @if(request()->routeIs('vote.token')) aria-current="page" @endif>
                            Beranda
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('vote.booth') }}"
                           class="nav-link"
                           @if(request()->routeIs('vote.booth')) aria-current="page" @endif>
                            Bilik Suara
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('vote.results') }}"
                           class="nav-link"
                           @if(request()->routeIs('vote.results')) aria-current="page" @endif>
                            Hasil Suara
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tokens.index') }}"
                           class="nav-link"
                           @if(request()->routeIs('tokens.*') || request()->routeIs('candidates.*') || request()->routeIs('admin.*')) aria-current="page" @endif>
                            Panel Panitia
                        </a>
                    </li>
                </ul>

                <div>
                    @if(session('is_admin_authenticated'))
                        <a href="{{ route('candidates.index') }}" class="nav-cta">
                            <span>Kelola Kandidat</span>
                            <span aria-hidden="true">→</span>
                        </a>
                    @else
                        <a href="{{ route('vote.results') }}" class="nav-cta">
                            <span>Live Count</span>
                            <span aria-hidden="true">→</span>
                        </a>
                    @endif
                </div>
            </nav>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="stage-footer">
            <div class="scroll-indicator">
                <span class="mouse-pill" aria-hidden="true"></span>
                <span>Sistem Pemilihan Elektronik Terenkripsi &mdash;&mdash;&mdash;</span>
            </div>
            <div class="tagline-spaced">
                JUJUR &nbsp;/&nbsp; RAHASIA &nbsp;/&nbsp; TRANSPARAN
            </div>
        </footer>
    </div>

    <script type="module">
        if (!('commandForElement' in HTMLButtonElement.prototype)) {
            import('https://esm.run/invokers-polyfill');
        }
    </script>
    @yield('scripts')
</body>
</html>
