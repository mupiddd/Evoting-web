@extends('vote.layout')

@section('title', 'VoteForge — Hasil & Visualisasi Perolehan Suara')

@section('styles')
<style>
    .results-top {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1.5rem;
        margin-block-end: 2rem;
        flex-wrap: wrap;
    }

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-block-end: 2rem;
    }

    .kpi-card {
        padding: 1.35rem 1.5rem;
        border-radius: 1.35rem;
        background: rgba(10, 31, 68, 0.55);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(125, 211, 252, 0.25);
        box-shadow: 0 15px 30px rgba(2, 8, 23, 0.45);
    }

    .kpi-label {
        display: block;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #93c5fd;
        margin-block-end: 0.35rem;
        font-weight: 600;
    }

    .kpi-value {
        font-size: 1.85rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.2;
    }

    .kpi-sub {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-block-start: 0.25rem;
    }

    .results-layout {
        display: grid;
        grid-template-columns: 1.35fr 0.65fr;
        gap: 1.75rem;
        align-items: start;
    }

    .candidate-bar-item {
        padding: 1.25rem;
        border-radius: 1.25rem;
        background: rgba(4, 15, 35, 0.55);
        border: 1px solid rgba(125, 211, 252, 0.2);
        margin-block-end: 1.15rem;
    }

    .candidate-bar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-block-end: 0.85rem;
        flex-wrap: wrap;
    }

    .candidate-bar-identity {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .candidate-bar-avatar {
        width: 50px;
        height: 50px;
        border-radius: 0.85rem;
        object-fit: cover;
        border: 1.5px solid rgba(56, 189, 248, 0.55);
    }

    .candidate-bar-stats {
        text-align: right;
    }

    .vote-pct-badge {
        font-family: 'JetBrains Mono', ui-monospace, monospace;
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--accent-cyan);
    }

    .vote-count-text {
        display: block;
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    /* CSS Custom Property driven bar (--bar-width) per html guide Section 9 */
    .bar-track {
        width: 100%;
        height: 14px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.85);
        border: 1px solid rgba(125, 211, 252, 0.18);
        overflow: hidden;
    }

    .bar-fill {
        height: 100%;
        width: var(--bar-width, 0%);
        border-radius: 999px;
        background: linear-gradient(90deg, #2563eb 0%, #38bdf8 60%, #a78bfa 100%);
        box-shadow: 0 0 16px rgba(56, 189, 248, 0.65);
        transition: width 0.8s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .chart-side-card {
        text-align: center;
    }

    .participation-ring-wrap {
        position: relative;
        width: 180px;
        height: 180px;
        margin: 1.25rem auto;
    }

    .ring-center-label {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 920px) {
        .results-layout {
            grid-template-columns: 1fr;
        }
    }

    @media print {
        .top-navbar,
        .stage-footer,
        .no-print {
            display: none !important;
        }
        body {
            background: #040d1a !important;
        }
    }
</style>
@endsection

@section('content')
@php
    $defaultAvatar = "data:image/svg+xml;utf8," . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" fill="none"><rect width="200" height="200" fill="#0c264e"/><circle cx="100" cy="75" r="35" fill="#38bdf8" fill-opacity="0.35"/><path d="M40 175 C40 125 160 125 160 175" fill="#38bdf8" fill-opacity="0.25"/></svg>');
    $leadingCandidate = $totalVotes > 0 ? $candidates->first() : null;
    $participationRate = $participationRate ?? 0;
@endphp

<div class="results-top">
    <div>
        <div class="eyebrow-label">
            <span>REAL-TIME LIVE COUNT</span>
        </div>
        <h1 class="hero-title" style="margin-block-end: 0.25rem;">
            Hasil &amp; Visualisasi <span class="gradient-text" style="display: inline;">Perolehan Suara</span>
        </h1>
        <p class="subtitle-text" style="margin-block-end: 0;">
            Rekapitulasi suara masuk secara langsung dari database PostgreSQL.
        </p>
    </div>

    <div class="no-print" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <button type="button" class="btn-glass-secondary" id="print-report-btn">
            <span>Cetak Rekapitulasi</span>
        </button>
        <a href="{{ route('vote.token') }}" class="btn-pill-primary">
            <span>Kembali ke Bilik Suara</span>
            <span aria-hidden="true">→</span>
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="kpi-grid">
    <div class="kpi-card">
        <span class="kpi-label">Total Suara Masuk</span>
        <div class="kpi-value">{{ $totalVotes }}</div>
        <p class="kpi-sub">Suara sah tercatat</p>
    </div>

    <div class="kpi-card">
        <span class="kpi-label">Tingkat Partisipasi</span>
        <div class="kpi-value">{{ $participationRate }}%</div>
        <p class="kpi-sub">{{ $usedTokens ?? 0 }} dari {{ $totalTokens ?? 0 }} token digunakan</p>
    </div>

    <div class="kpi-card">
        <span class="kpi-label">Jumlah Pasangan Calon</span>
        <div class="kpi-value">{{ $candidates->count() }}</div>
        <p class="kpi-sub">Kandidat terdaftar</p>
    </div>

    <div class="kpi-card">
        <span class="kpi-label">Posisi Teratas Saat Ini</span>
        <div class="kpi-value" style="font-size: 1.15rem; color: var(--accent-cyan);">
            {{ $leadingCandidate ? $leadingCandidate->name : 'Belum Ada Suara' }}
        </div>
        <p class="kpi-sub">
            {{ $leadingCandidate ? $leadingCandidate->votes_count . ' suara terkumpul' : 'Menunggu suara pertama' }}
        </p>
    </div>
</div>

<div class="results-layout">
    <!-- Left: Interactive Candidate Progress Bars -->
    <section class="glass-panel" aria-labelledby="chart-section-heading">
        <h2 id="chart-section-heading" style="font-size: 1.25rem; font-weight: 800; margin-block-end: 1.35rem;">
            Grafik Perolehan Suara Kandidat
        </h2>

        @forelse($candidates as $index => $candidate)
            @php
                $percentage = $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes) * 100, 1) : 0;
            @endphp
            <div class="candidate-bar-item">
                <div class="candidate-bar-header">
                    <div class="candidate-bar-identity">
                        <img
                            src="{{ $candidate->photo ?: $defaultAvatar }}"
                            alt="Foto {{ $candidate->name }}"
                            width="50"
                            height="50"
                            class="candidate-bar-avatar"
                            loading="lazy"
                        >
                        <div>
                            <span class="candidate-badge-pill" style="margin-block-end: 0.2rem;">
                                PERINGKAT #{{ $index + 1 }}
                            </span>
                            <h3 style="font-size: 1.08rem; font-weight: 700; color: #ffffff;">
                                {{ $candidate->name }}
                            </h3>
                        </div>
                    </div>

                    <div class="candidate-bar-stats">
                        <span class="vote-pct-badge">{{ $percentage }}%</span>
                        <span class="vote-count-text">{{ $candidate->votes_count }} suara</span>
                    </div>
                </div>

                <div
                    class="bar-track"
                    role="progressbar"
                    aria-label="Persentase suara {{ $candidate->name }}"
                    aria-valuenow="{{ $percentage }}"
                    aria-valuemin="0"
                    aria-valuemax="100"
                >
                    <div class="bar-fill" style="--bar-width: {{ $percentage }}%;"></div>
                </div>
            </div>
        @empty
            <p style="color: var(--text-muted); text-align: center; padding: 2rem;">
                Belum ada kandidat yang terdaftar.
            </p>
        @endforelse
    </section>

    <!-- Right: Participation Donut Visualization -->
    <aside class="glass-panel chart-side-card" aria-labelledby="turnout-heading">
        <h2 id="turnout-heading" style="font-size: 1.15rem; font-weight: 800;">
            Rasio Penggunaan Token
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-block-top: 0.25rem;">
            Persentase pemilih yang telah menggunakan hak suara
        </p>

        @php
            $circumference = 2 * 3.14159 * 70;
            $dashOffset = $circumference - (($participationRate / 100) * $circumference);
        @endphp

        <div class="participation-ring-wrap">
            <svg width="180" height="180" viewBox="0 0 180 180" aria-hidden="true">
                <circle
                    cx="90"
                    cy="90"
                    r="70"
                    fill="none"
                    stroke="rgba(15, 23, 42, 0.85)"
                    stroke-width="16"
                ></circle>
                <circle
                    cx="90"
                    cy="90"
                    r="70"
                    fill="none"
                    stroke="url(#cyanGradient)"
                    stroke-width="16"
                    stroke-linecap="round"
                    stroke-dasharray="{{ $circumference }}"
                    stroke-dashoffset="{{ $dashOffset }}"
                    transform="rotate(-90 90 90)"
                ></circle>
                <defs>
                    <linearGradient id="cyanGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#38bdf8" />
                        <stop offset="100%" stop-color="#a78bfa" />
                    </linearGradient>
                </defs>
            </svg>
            <div class="ring-center-label">
                <strong style="font-size: 1.65rem; font-weight: 800; color: #ffffff;">{{ $participationRate }}%</strong>
                <span style="font-size: 0.75rem; color: #93c5fd;">Partisipasi</span>
            </div>
        </div>

        <div style="text-align: left; font-size: 0.85rem; border-block-start: 1px solid rgba(125, 211, 252, 0.2); padding-block-start: 1rem;">
            <div style="display: flex; justify-content: space-between; margin-block-end: 0.45rem;">
                <span style="color: var(--text-secondary);">Token Sudah Dipakai:</span>
                <strong style="color: var(--success);">{{ $usedTokens ?? 0 }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-secondary);">Token Belum Dipakai:</span>
                <strong style="color: var(--accent-cyan);">{{ max(0, ($totalTokens ?? 0) - ($usedTokens ?? 0)) }}</strong>
            </div>
        </div>
    </aside>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const printBtn = document.getElementById('print-report-btn');
        if (printBtn) {
            printBtn.addEventListener('click', () => {
                window.print();
            });
        }
    });
</script>
@endsection
