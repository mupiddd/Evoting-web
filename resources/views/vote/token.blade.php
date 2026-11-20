@extends('vote.layout')

@section('title', 'VoteForge — Masukkan Token Pemilihan')

@section('styles')
<style>
    .hero-split {
        display: grid;
        grid-template-columns: 1.08fr 0.92fr;
        gap: 2.5rem;
        align-items: center;
        padding-block: 0.75rem;
    }

    .token-input-box {
        font-family: 'JetBrains Mono', ui-monospace, monospace;
        font-size: 1.25rem;
        font-weight: 700;
        letter-spacing: 0.25em;
        text-transform: uppercase;
        text-align: center;
        background: rgba(3, 12, 28, 0.75);
        border: 1.5px solid rgba(56, 189, 248, 0.45);
        box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.5), 0 0 20px rgba(56, 189, 248, 0.12);
    }

    .hero-actions {
        display: flex;
        align-items: center;
        gap: 1.15rem;
        flex-wrap: wrap;
        margin-block-top: 0.5rem;
    }

    /* Right Side Candidate Showcase matching reference right side */
    .candidate-showcase {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    /* Glowing blue aura behind candidate portrait */
    .portrait-aura {
        position: absolute;
        width: 340px;
        height: 340px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.45) 0%, rgba(37, 99, 235, 0.25) 50%, transparent 72%);
        filter: blur(28px);
        z-index: 0;
        pointer-events: none;
    }

    /* Floating top-right glass callout badge (like "Great Ideas Build Great Brands" in reference) */
    .floating-callout {
        position: absolute;
        top: 0.25rem;
        right: 0.25rem;
        z-index: 3;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.7rem 1.1rem;
        border-radius: 1rem;
        background: rgba(12, 38, 78, 0.65);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(125, 211, 252, 0.4);
        box-shadow: 0 12px 30px rgba(2, 8, 23, 0.6), 0 0 20px rgba(56, 189, 248, 0.2);
        max-width: 245px;
    }

    .callout-bolt {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(56, 189, 248, 0.18);
        border: 1px solid rgba(125, 211, 252, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent-cyan);
        flex-shrink: 0;
    }

    .callout-text strong {
        display: block;
        font-size: 0.76rem;
        color: #e0f2fe;
        font-weight: 700;
        line-height: 1.25;
    }

    .callout-text span {
        display: block;
        font-size: 0.7rem;
        color: #93c5fd;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 165px;
    }

    /* Main Candidate Portrait Frame */
    .portrait-card {
        position: relative;
        z-index: 1;
        width: min(100%, 380px);
        border-radius: 1.75rem;
        overflow: hidden;
        background: linear-gradient(160deg, rgba(15, 42, 84, 0.72), rgba(5, 18, 40, 0.85));
        border: 1.5px solid rgba(125, 211, 252, 0.42);
        box-shadow:
            0 25px 55px rgba(2, 8, 23, 0.85),
            0 0 35px rgba(56, 189, 248, 0.28),
            inset 0 1px 2px rgba(255, 255, 255, 0.4);
        transition: transform 0.35s ease;
    }

    .portrait-image-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 4.3;
        overflow: hidden;
        background: radial-gradient(circle at 50% 35%, #1e3a8a, #09152b);
    }

    .portrait-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.45s ease, opacity 0.3s ease;
    }

    .portrait-overlay {
        position: absolute;
        inset: auto 0 0 0;
        padding: 1.5rem 1.35rem 1.25rem;
        background: linear-gradient(to top, rgba(3, 11, 23, 0.96) 15%, rgba(7, 25, 54, 0.72) 65%, transparent 100%);
    }

    .candidate-badge-pill {
        display: inline-block;
        padding: 0.22rem 0.75rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        background: rgba(56, 189, 248, 0.22);
        border: 1px solid rgba(125, 211, 252, 0.5);
        color: #7dd3fc;
        margin-block-end: 0.4rem;
    }

    .candidate-spot-name {
        font-size: 1.25rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.25;
        margin-block-end: 0.3rem;
    }

    .candidate-spot-vision {
        font-size: 0.82rem;
        color: var(--text-secondary);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Right-edge vertical floating circular glass buttons (matching reference image) */
    .vertical-glass-controls {
        position: absolute;
        right: -0.5rem;
        top: 52%;
        transform: translateY(-50%);
        z-index: 3;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }

    .circle-glass-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: rgba(12, 38, 78, 0.72);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(125, 211, 252, 0.4);
        color: #e0f2fe;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-family: inherit;
        font-size: 0.8rem;
        font-weight: 700;
        box-shadow: 0 8px 20px rgba(2, 8, 23, 0.6);
        transition: all 0.2s ease;
    }

    .circle-glass-btn:hover,
    .circle-glass-btn[aria-pressed="true"] {
        background: linear-gradient(135deg, #2563eb, #38bdf8);
        border-color: #ffffff;
        color: #ffffff;
        box-shadow: 0 0 18px rgba(56, 189, 248, 0.75);
        transform: scale(1.08);
    }

    /* Thumbnail Strip of All Candidates below the main portrait */
    .candidate-thumb-strip {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        margin-block-start: 1.15rem;
        flex-wrap: wrap;
    }

    .candidate-thumb-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.35rem 0.75rem 0.35rem 0.35rem;
        border-radius: 999px;
        background: rgba(10, 29, 59, 0.65);
        border: 1px solid rgba(125, 211, 252, 0.28);
        color: var(--text-secondary);
        cursor: pointer;
        font-family: inherit;
        font-size: 0.78rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .candidate-thumb-btn:hover,
    .candidate-thumb-btn[aria-pressed="true"] {
        background: rgba(37, 99, 235, 0.45);
        border-color: var(--accent-cyan);
        color: #ffffff;
        box-shadow: 0 0 16px rgba(56, 189, 248, 0.35);
    }

    .candidate-thumb-img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid rgba(125, 211, 252, 0.6);
    }

    @media (max-width: 960px) {
        .hero-split {
            grid-template-columns: 1fr;
        }

        .vertical-glass-controls {
            right: 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="hero-split">
    <!-- Left Column: Glassmorphism Main Card matching PixelForge reference -->
    <section class="glass-panel" aria-labelledby="hero-heading">
        <div class="eyebrow-label">
            <span>SISTEM E-VOTING ORGANISASI</span>
        </div>

        <h1 id="hero-heading" class="hero-title">
            Suara Anda untuk
            <span class="gradient-text">Pemimpin Masa Depan</span>
        </h1>

        <p class="subtitle-text">
            Kami menghadirkan sistem pemilihan elektronik modern, cepat, dan terenkripsi. Masukkan kode token unik dari panitia untuk membuka bilik suara Anda.
        </p>

        @if(session('error'))
            <div class="glass-alert glass-alert-error" role="alert">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="glass-alert glass-alert-success" role="status">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('vote.token.verify') }}" method="POST" id="token-verify-form">
            @csrf
            <div class="form-group">
                <label for="voting-token-input" class="form-label">Kode Token Pemilih (Wajib)</label>
                <input
                    type="text"
                    id="voting-token-input"
                    name="token"
                    class="glass-input token-input-box"
                    placeholder="CONTOH: A8X19B"
                    required
                    minlength="4"
                    maxlength="10"
                    autofocus
                    autocorrect="off"
                    autocapitalize="characters"
                    spellcheck="false"
                    enterkeyhint="go"
                    aria-describedby="token-help"
                    value="{{ old('token') }}"
                >
                <span id="token-help" class="form-hint">
                    Masukkan 6&ndash;10 karakter kode token alfanumerik yang diberikan oleh panitia pemilihan.
                </span>
            </div>

            <div class="hero-actions">
                <button type="submit" class="btn-pill-primary" id="token-submit-btn">
                    <span>Buka Bilik Suara</span>
                    <span aria-hidden="true">→</span>
                </button>

                <a href="{{ route('vote.results') }}" class="btn-play-link">
                    <span class="play-circle" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                        </svg>
                    </span>
                    <span>Pantau Hasil ({{ $totalVotes }} Suara)</span>
                </a>
            </div>
        </form>

        <!-- Bottom 4 Mini Features matching the reference glass card -->
        <div class="feature-grid">
            <div class="feature-item">
                <div class="feature-icon-box" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                <div class="feature-meta">
                    <strong>Token Unik</strong>
                    <span>1x Pakai Aman</span>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon-box" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>
                <div class="feature-meta">
                    <strong>Privasi Penuh</strong>
                    <span>100% Anonim</span>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon-box" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
                <div class="feature-meta">
                    <strong>Real-Time</strong>
                    <span>Hitung Otomatis</span>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon-box" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                        <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                        <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                    </svg>
                </div>
                <div class="feature-meta">
                    <strong>PostgreSQL</strong>
                    <span>Cloud Terjamin</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Right Column: Interactive Candidate Photo Showcase (as requested by user) -->
    <aside class="candidate-showcase" aria-label="Galeri Foto Kandidat">
        <div class="portrait-aura" aria-hidden="true"></div>

        @php
            $firstCandidate = $candidates->first();
            $defaultAvatar = "data:image/svg+xml;utf8," . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 650" fill="none"><defs><linearGradient id="bg" x1="0" y1="0" x2="600" y2="650"><stop offset="0%" stop-color="#0f2952"/><stop offset="100%" stop-color="#051024"/></linearGradient><radialGradient id="glow" cx="50%" cy="40%" r="50%"><stop offset="0%" stop-color="#38bdf8" stop-opacity="0.45"/><stop offset="100%" stop-color="#1d4ed8" stop-opacity="0"/></radialGradient></defs><rect width="600" height="650" fill="url(#bg)"/><circle cx="300" cy="250" r="240" fill="url(#glow)"/><circle cx="300" cy="230" r="95" fill="#38bdf8" fill-opacity="0.25" stroke="#7dd3fc" stroke-width="3"/><path d="M140 530 C140 410 460 410 460 530" fill="#38bdf8" fill-opacity="0.2" stroke="#7dd3fc" stroke-width="3"/></svg>');
        @endphp

        <!-- Top-right floating glass callout matching reference image -->
        <div class="floating-callout">
            <div class="callout-bolt" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
            </div>
            <div class="callout-text">
                <strong id="callout-badge-title">
                    {{ $firstCandidate ? 'Kandidat #01' : 'Kandidat Pemilihan' }}
                </strong>
                <span id="callout-badge-sub">
                    {{ $firstCandidate ? $firstCandidate->name : 'Siap Memimpin Perubahan' }}
                </span>
            </div>
        </div>

        @if($candidates->isNotEmpty())
            <div class="portrait-card" id="active-portrait-card">
                <div class="portrait-image-wrap">
                    <img
                        id="showcase-candidate-img"
                        src="{{ $firstCandidate->photo ?: $defaultAvatar }}"
                        alt="Foto {{ $firstCandidate->name }}"
                        width="600"
                        height="650"
                        fetchpriority="high"
                    >
                    <div class="portrait-overlay">
                        <span class="candidate-badge-pill" id="showcase-candidate-number">KANDIDAT 01</span>
                        <h2 class="candidate-spot-name" id="showcase-candidate-name">{{ $firstCandidate->name }}</h2>
                        <p class="candidate-spot-vision" id="showcase-candidate-vision">{{ $firstCandidate->vision_mission }}</p>
                    </div>
                </div>
            </div>

            <!-- Right-edge vertical floating circular glass buttons -->
            <div class="vertical-glass-controls" role="group" aria-label="Pilih Foto Kandidat">
                @foreach($candidates as $index => $candidate)
                    <button
                        type="button"
                        class="circle-glass-btn candidate-switch-trigger"
                        data-index="{{ $index }}"
                        data-number="{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}"
                        data-name="{{ $candidate->name }}"
                        data-vision="{{ $candidate->vision_mission }}"
                        data-photo="{{ $candidate->photo ?: $defaultAvatar }}"
                        aria-pressed="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-label="Tampilkan foto {{ $candidate->name }}"
                    >
                        {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                    </button>
                @endforeach
            </div>

            <!-- Bottom Thumbnail Strip of All Candidates -->
            <div class="candidate-thumb-strip" role="group" aria-label="Daftar Pasangan Calon">
                @foreach($candidates as $index => $candidate)
                    <button
                        type="button"
                        class="candidate-thumb-btn candidate-switch-trigger"
                        data-index="{{ $index }}"
                        data-number="{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}"
                        data-name="{{ $candidate->name }}"
                        data-vision="{{ $candidate->vision_mission }}"
                        data-photo="{{ $candidate->photo ?: $defaultAvatar }}"
                        aria-pressed="{{ $index === 0 ? 'true' : 'false' }}"
                    >
                        <img
                            src="{{ $candidate->photo ?: $defaultAvatar }}"
                            alt=""
                            width="30"
                            height="30"
                            class="candidate-thumb-img"
                            loading="lazy"
                        >
                        <span>{{ Str::limit($candidate->name, 22) }}</span>
                    </button>
                @endforeach
            </div>
        @else
            <div class="portrait-card">
                <div class="portrait-image-wrap">
                    <img
                        src="{{ $defaultAvatar }}"
                        alt="Ilustrasi Kandidat Pemilihan"
                        width="600"
                        height="650"
                        fetchpriority="high"
                    >
                    <div class="portrait-overlay">
                        <span class="candidate-badge-pill">KANDIDAT PEMILIHAN</span>
                        <h2 class="candidate-spot-name">Belum Ada Kandidat</h2>
                        <p class="candidate-spot-vision">Panitia dapat menambahkan data dan foto kandidat melalui menu Panel Panitia.</p>
                    </div>
                </div>
            </div>
        @endif
    </aside>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Form validation & anti-double-submit (modern-web-guidance forms)
        const tokenInput = document.getElementById('voting-token-input');
        const tokenForm = document.getElementById('token-verify-form');
        const submitBtn = document.getElementById('token-submit-btn');

        if (tokenInput) {
            tokenInput.addEventListener('invalid', () => {
                tokenInput.setCustomValidity('Silakan masukkan kode token pemilih yang valid (4-10 karakter).');
            });

            tokenInput.addEventListener('input', () => {
                tokenInput.setCustomValidity('');
            });

            tokenInput.addEventListener('blur', () => {
                if (tokenInput.value.trim().length > 0 && tokenInput.value.trim().length < 4) {
                    tokenInput.setCustomValidity('Kode token minimal terdiri dari 4 karakter.');
                }
            });
        }

        if (tokenForm && submitBtn) {
            tokenForm.addEventListener('submit', () => {
                if (tokenForm.checkValidity()) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span>Memverifikasi Token...</span>';
                }
            });
        }

        // Interactive Candidate Photo Switcher on Right Column
        const triggers = document.querySelectorAll('.candidate-switch-trigger');
        const imgEl = document.getElementById('showcase-candidate-img');
        const numEl = document.getElementById('showcase-candidate-number');
        const nameEl = document.getElementById('showcase-candidate-name');
        const visionEl = document.getElementById('showcase-candidate-vision');
        const calloutTitle = document.getElementById('callout-badge-title');
        const calloutSub = document.getElementById('callout-badge-sub');

        if (triggers.length > 0 && imgEl) {
            const activateCandidate = (btn) => {
                const index = btn.getAttribute('data-index');
                const num = btn.getAttribute('data-number');
                const name = btn.getAttribute('data-name');
                const vision = btn.getAttribute('data-vision');
                const photo = btn.getAttribute('data-photo');

                imgEl.src = photo;
                imgEl.alt = 'Foto ' + name;
                if (numEl) {
                    numEl.textContent = 'KANDIDAT ' + num;
                }
                if (nameEl) {
                    nameEl.textContent = name;
                }
                if (visionEl) {
                    visionEl.textContent = vision;
                }
                if (calloutTitle) {
                    calloutTitle.textContent = 'Kandidat #' + num;
                }
                if (calloutSub) {
                    calloutSub.textContent = name;
                }

                triggers.forEach((item) => {
                    item.setAttribute('aria-pressed', item.getAttribute('data-index') === index ? 'true' : 'false');
                });
            };

            triggers.forEach((btn) => {
                btn.addEventListener('click', () => {
                    activateCandidate(btn);
                });
            });
        }
    });
</script>
@endsection
