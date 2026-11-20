@extends('vote.layout')

@section('title', 'VoteForge — Suara Berhasil Dicatat')

@section('styles')
<style>
    .success-stage {
        max-width: 580px;
        margin: 2rem auto;
        text-align: center;
    }

    .check-orb {
        width: 84px;
        height: 84px;
        border-radius: 50%;
        margin: 0 auto 1.5rem;
        background: radial-gradient(circle at 30% 30%, rgba(52, 211, 153, 0.35), rgba(14, 165, 233, 0.2));
        border: 1.5px solid rgba(52, 211, 153, 0.65);
        box-shadow: 0 0 35px rgba(52, 211, 153, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--success);
    }

    .success-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
        margin-block-start: 1.75rem;
    }
</style>
@endsection

@section('content')
<section class="glass-panel success-stage" aria-labelledby="success-heading">
    <div class="check-orb" aria-hidden="true">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
    </div>

    <div class="eyebrow-label" style="justify-content: center;">
        <span>TRANSAKSI SUARA TERVERIFIKASI</span>
    </div>

    <h1 id="success-heading" class="hero-title" style="font-size: 2.2rem;">
        Terima Kasih atas <span class="gradient-text">Partisipasi Anda!</span>
    </h1>

    <p class="subtitle-text" style="margin-inline: auto;">
        Suara Anda telah berhasil dienkripsi dan dicatat ke dalam database PostgreSQL secara anonim. Kode token Anda kini telah dinonaktifkan.
    </p>

    <div class="success-actions">
        <a href="{{ route('vote.results') }}" class="btn-pill-primary">
            <span>Lihat Hasil Perolehan Suara</span>
            <span aria-hidden="true">→</span>
        </a>
        <a href="{{ route('vote.token') }}" class="btn-glass-secondary">
            <span>Kembali ke Beranda</span>
        </a>
    </div>
</section>
@endsection
