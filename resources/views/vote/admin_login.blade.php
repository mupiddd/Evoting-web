@extends('vote.layout')

@section('title', 'VoteForge — Login Panel Panitia')

@section('styles')
<style>
    .login-card-wrap {
        max-width: 480px;
        margin: 1.5rem auto;
    }

    .password-field-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .password-toggle-btn {
        position: absolute;
        right: 0.75rem;
        background: rgba(15, 42, 82, 0.75);
        border: 1px solid rgba(125, 211, 252, 0.35);
        color: #93c5fd;
        border-radius: 0.65rem;
        padding: 0.35rem 0.65rem;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
    }

    .password-toggle-btn:hover {
        color: #ffffff;
        border-color: var(--accent-cyan);
    }
</style>
@endsection

@section('content')
<section class="glass-panel login-card-wrap" aria-labelledby="admin-login-heading">
    <div class="eyebrow-label">
        <span>KEAMANAN PANEL PANITIA</span>
    </div>

    <h1 id="admin-login-heading" class="hero-title" style="font-size: 2rem;">
        Otorisasi <span class="gradient-text" style="display: inline;">Panitia Pemilihan</span>
    </h1>

    <p class="subtitle-text">
        Halaman manajemen token dan data kandidat dilindungi kata sandi panitia untuk menjaga integritas pemilihan.
    </p>

    @if(session('error'))
        <div class="glass-alert glass-alert-error" role="alert">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.login.submit') }}" method="POST" id="admin-login-form">
        @csrf
        <div class="form-group">
            <label for="admin-password" class="form-label">Kata Sandi Panitia (Wajib)</label>
            <div class="password-field-wrap">
                <input
                    type="password"
                    id="admin-password"
                    name="password"
                    class="glass-input"
                    style="padding-inline-end: 5.5rem;"
                    autocomplete="current-password"
                    required
                    autofocus
                    aria-describedby="password-help"
                    placeholder="Masukkan kata sandi panitia"
                >
                <button
                    type="button"
                    id="toggle-password-btn"
                    class="password-toggle-btn"
                    aria-pressed="false"
                    aria-label="Tampilkan kata sandi"
                    aria-describedby="toggle-warning"
                >
                    Lihat
                </button>
                <span id="toggle-warning" class="visually-hidden">
                    Peringatan: tindakan ini akan menampilkan kata sandi di layar.
                </span>
            </div>
            <span id="password-help" class="form-hint">
                Gunakan kata sandi panitia yang dikonfigurasi pada variabel <code>ADMIN_PASSWORD</code>.
            </span>
        </div>

        <button type="submit" class="btn-pill-primary" style="width: 100%;" id="admin-login-submit">
            <span>Masuk ke Panel Panitia</span>
            <span aria-hidden="true">→</span>
        </button>
    </form>
</section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const pwdInput = document.getElementById('admin-password');
        const toggleBtn = document.getElementById('toggle-password-btn');
        const loginForm = document.getElementById('admin-login-form');
        const submitBtn = document.getElementById('admin-login-submit');

        if (pwdInput && toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                const isRevealed = pwdInput.type === 'text';
                pwdInput.type = isRevealed ? 'password' : 'text';
                toggleBtn.setAttribute('aria-pressed', isRevealed ? 'false' : 'true');
                toggleBtn.textContent = isRevealed ? 'Lihat' : 'Sembunyi';
            });
        }

        if (loginForm && submitBtn) {
            loginForm.addEventListener('submit', () => {
                if (loginForm.checkValidity()) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span>Memverifikasi...</span>';
                }
            });
        }
    });
</script>
@endsection
