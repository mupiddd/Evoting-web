@extends('vote.layout')

@section('title', 'VoteForge — Bilik Suara Pemilihan')

@section('styles')
<style>
    .booth-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1.5rem;
        margin-block-end: 2rem;
        flex-wrap: wrap;
    }

    .status-bar-pills {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        flex-wrap: wrap;
    }

    .token-pill,
    .timer-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.6rem 1.1rem;
        border-radius: 999px;
        background: rgba(12, 38, 78, 0.68);
        border: 1px solid rgba(125, 211, 252, 0.38);
        font-size: 0.85rem;
        font-weight: 600;
        color: #e0f2fe;
        box-shadow: 0 8px 20px rgba(2, 8, 23, 0.45);
    }

    .timer-pill code,
    .token-pill code {
        font-family: 'JetBrains Mono', ui-monospace, monospace;
        font-weight: 700;
        color: var(--accent-cyan);
        font-size: 0.95rem;
    }

    .timer-pill[data-warning="true"] {
        border-color: rgba(251, 113, 133, 0.75);
        background: rgba(136, 19, 55, 0.55);
    }

    .timer-pill[data-warning="true"] code {
        color: #fda4af;
    }

    /* Candidate Selection Grid */
    .candidates-fieldset {
        border: 0;
        margin: 0;
        padding: 0;
    }

    .candidates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.75rem;
        margin-block-end: 2rem;
    }

    .candidate-card {
        position: relative;
        display: flex;
        flex-direction: column;
        border-radius: 1.5rem;
        background: linear-gradient(155deg, rgba(16, 46, 92, 0.55) 0%, rgba(7, 22, 48, 0.72) 100%);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1.5px solid rgba(125, 211, 252, 0.25);
        overflow: hidden;
        transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    }

    .candidate-card:has(input[type="radio"]:checked) {
        border-color: var(--accent-cyan);
        box-shadow: 0 0 35px rgba(56, 189, 248, 0.35), inset 0 1px 2px rgba(255, 255, 255, 0.45);
        transform: translateY(-4px);
    }

    .candidate-photo-box {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 11;
        overflow: hidden;
        background: radial-gradient(circle at 50% 35%, #1e3a8a, #09152b);
    }

    .candidate-photo-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .candidate-number-tag {
        position: absolute;
        top: 1rem;
        left: 1rem;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        background: rgba(3, 11, 23, 0.78);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(125, 211, 252, 0.45);
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.1em;
        color: var(--accent-cyan);
    }

    .candidate-body {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .candidate-radio-label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        font-size: 1.2rem;
        font-weight: 800;
        color: #ffffff;
        margin-block-end: 0.75rem;
    }

    .candidate-radio-input {
        width: 1.35rem;
        height: 1.35rem;
        accent-color: var(--accent-cyan);
        cursor: pointer;
        flex-shrink: 0;
    }

    .candidate-vision-preview {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin-block-end: 1rem;
        white-space: pre-line;
    }

    /* Native Disclosure for full Visi & Misi (html guide section 5) */
    .vision-details {
        margin-block-end: 1.25rem;
        border-radius: 0.85rem;
        background: rgba(4, 15, 35, 0.5);
        border: 1px solid rgba(125, 211, 252, 0.18);
        padding: 0.65rem 0.95rem;
        font-size: 0.84rem;
    }

    .vision-details summary {
        cursor: pointer;
        font-weight: 600;
        color: #93c5fd;
    }

    .vision-details[open] summary {
        margin-block-end: 0.5rem;
        color: var(--accent-cyan);
    }

    .candidate-card-footer {
        margin-block-start: auto;
        padding-block-start: 0.75rem;
    }

    .booth-submit-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.25rem;
        flex-wrap: wrap;
        padding: 1.5rem 2rem;
    }

    .dialog-candidate-preview {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 1rem;
        background: rgba(4, 15, 35, 0.65);
        border: 1px solid rgba(125, 211, 252, 0.3);
        margin-block: 1.25rem;
    }

    .dialog-candidate-preview img {
        width: 68px;
        height: 68px;
        border-radius: 0.85rem;
        object-fit: cover;
        border: 1px solid rgba(56, 189, 248, 0.6);
        flex-shrink: 0;
    }

    .dialog-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.85rem;
        margin-block-start: 1.5rem;
        flex-wrap: wrap;
    }
</style>
@endsection

@section('content')
@php
    $defaultAvatar = "data:image/svg+xml;utf8," . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" fill="none"><rect width="600" height="400" fill="#091936"/><circle cx="300" cy="155" r="65" fill="#38bdf8" fill-opacity="0.28" stroke="#7dd3fc" stroke-width="3"/><path d="M170 360 C170 265 430 265 430 360" fill="#38bdf8" fill-opacity="0.22" stroke="#7dd3fc" stroke-width="3"/></svg>');
@endphp

<div class="booth-header">
    <div>
        <div class="eyebrow-label">
            <span>BILIK SUARA TERENKRIPSI</span>
        </div>
        <h1 class="hero-title" style="margin-block-end: 0.35rem;">
            Pilih Pasangan <span class="gradient-text" style="display: inline;">Kandidat Anda</span>
        </h1>
        <p class="subtitle-text" style="margin-block-end: 0;">
            Pilih tepat satu pasangan calon di bawah ini. Pilihan Anda bersifat rahasia, anonim, dan tidak dapat diubah setelah dikonfirmasi.
        </p>
    </div>

    <div class="status-bar-pills">
        <div class="token-pill">
            <span>Token Aktif:</span>
            <code>{{ session('voting_token') }}</code>
        </div>
        <div class="timer-pill" id="booth-timer-pill" data-remaining="{{ $remainingSeconds ?? 300 }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            <span>Sisa Waktu:</span>
            <code id="booth-timer-display">05:00</code>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="glass-alert glass-alert-error" role="alert">
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<div id="golput-warning" class="glass-alert glass-alert-error" role="alert" hidden>
    <span>Pencegahan Golput: Silakan pilih salah satu kandidat terlebih dahulu sebelum mengirim suara.</span>
</div>

<form action="{{ route('vote.submit') }}" method="POST" id="booth-vote-form">
    @csrf
    <fieldset class="candidates-fieldset">
        <legend class="visually-hidden">Daftar Pasangan Calon Pemilihan</legend>

        <div class="candidates-grid">
            @foreach($candidates as $index => $candidate)
                @php
                    $numStr = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
                    $photoSrc = $candidate->photo ?: $defaultAvatar;
                @endphp
                <article class="candidate-card">
                    <div class="candidate-photo-box">
                        <img
                            src="{{ $photoSrc }}"
                            alt="Foto {{ $candidate->name }}"
                            width="600"
                            height="412"
                            @if($index === 0) fetchpriority="high" @else loading="lazy" @endif
                        >
                        <span class="candidate-number-tag">NO. URUT {{ $numStr }}</span>
                    </div>

                    <div class="candidate-body">
                        <label for="candidate-radio-{{ $candidate->id }}" class="candidate-radio-label">
                            <input
                                type="radio"
                                id="candidate-radio-{{ $candidate->id }}"
                                name="candidate_id"
                                value="{{ $candidate->id }}"
                                class="candidate-radio-input"
                                data-candidate-name="{{ $candidate->name }}"
                                data-candidate-number="NO. URUT {{ $numStr }}"
                                data-candidate-photo="{{ $photoSrc }}"
                                required
                            >
                            <span>{{ $candidate->name }}</span>
                        </label>

                        <p class="candidate-vision-preview">{{ Str::limit($candidate->vision_mission, 140) }}</p>

                        <details class="vision-details" name="candidate-vision">
                            <summary>Baca Selengkapnya Visi &amp; Misi</summary>
                            <p style="white-space: pre-line; color: var(--text-secondary);">{{ $candidate->vision_mission }}</p>
                        </details>

                        <div class="candidate-card-footer">
                            <button
                                type="button"
                                class="btn-glass-secondary select-and-confirm-btn"
                                style="width: 100%;"
                                data-radio-id="candidate-radio-{{ $candidate->id }}"
                            >
                                <span>Pilih {{ $candidate->name }}</span>
                                <span aria-hidden="true">→</span>
                            </button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </fieldset>

    <div class="glass-panel booth-submit-bar">
        <div>
            <strong style="display: block; font-size: 1rem; color: #ffffff;" id="selected-candidate-status">
                Belum ada kandidat yang dipilih
            </strong>
            <span style="font-size: 0.85rem; color: var(--text-muted);">
                Pastikan pilihan Anda sudah sesuai sebelum menekan tombol konfirmasi.
            </span>
        </div>

        <button
            type="button"
            class="btn-pill-primary"
            id="open-confirm-modal-btn"
            commandfor="confirm-vote-dialog"
            command="show-modal"
        >
            <span>Konfirmasi &amp; Kirim Suara</span>
            <span aria-hidden="true">→</span>
        </button>
    </div>
</form>

<!-- Native Modal Dialog with closedby="any" (modern-web-guidance light-dismiss-a-dialog) -->
<dialog id="confirm-vote-dialog" class="glass-dialog" closedby="any" aria-labelledby="confirm-dialog-title">
    <div class="eyebrow-label" style="margin-block-end: 0.4rem;">
        <span>KONFIRMASI PILIHAN</span>
    </div>
    <h2 id="confirm-dialog-title" style="font-size: 1.4rem; font-weight: 800; color: #ffffff;">
        Kirim Suara Anda Sekarang?
    </h2>
    <p style="font-size: 0.9rem; color: var(--text-secondary); margin-block-top: 0.35rem;">
        Anda akan memberikan suara untuk pasangan kandidat berikut:
    </p>

    <div class="dialog-candidate-preview">
        <img
            id="dialog-candidate-img"
            src="{{ $defaultAvatar }}"
            alt="Pratinjau foto kandidat terpilih"
            width="68"
            height="68"
        >
        <div>
            <span class="candidate-badge-pill" id="dialog-candidate-number">KANDIDAT</span>
            <strong style="display: block; font-size: 1.05rem; color: #ffffff;" id="dialog-candidate-name">
                Silakan pilih kandidat
            </strong>
        </div>
    </div>

    <p style="font-size: 0.82rem; color: var(--text-muted);">
        Suara yang telah dikirim bersifat final, tercatat secara anonim di database PostgreSQL, dan kode token Anda akan langsung dinonaktifkan.
    </p>

    <div class="dialog-actions">
        <button
            type="button"
            class="btn-glass-secondary"
            id="cancel-dialog-btn"
            commandfor="confirm-vote-dialog"
            command="close"
        >
            Ubah Pilihan
        </button>
        <button
            type="submit"
            form="booth-vote-form"
            class="btn-pill-primary"
            id="final-vote-submit-btn"
        >
            <span>Ya, Kirim Suara Saya</span>
            <span aria-hidden="true">✓</span>
        </button>
    </div>
</dialog>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dialog = document.getElementById('confirm-vote-dialog');
        const radios = document.querySelectorAll('.candidate-radio-input');
        const quickSelectBtns = document.querySelectorAll('.select-and-confirm-btn');
        const openModalBtn = document.getElementById('open-confirm-modal-btn');
        const cancelDialogBtn = document.getElementById('cancel-dialog-btn');
        const statusText = document.getElementById('selected-candidate-status');
        const golputWarning = document.getElementById('golput-warning');
        const dialogImg = document.getElementById('dialog-candidate-img');
        const dialogName = document.getElementById('dialog-candidate-name');
        const dialogNumber = document.getElementById('dialog-candidate-number');
        const voteForm = document.getElementById('booth-vote-form');
        const finalSubmitBtn = document.getElementById('final-vote-submit-btn');

        // Fallback for browsers without native closedby support (light-dismiss-a-dialog guide)
        if (dialog && !('closedBy' in HTMLDialogElement.prototype)) {
            dialog.addEventListener('click', (event) => {
                if (event.target !== dialog) {
                    return;
                }
                const rect = dialog.getBoundingClientRect();
                const isDialogContent = (
                    rect.top <= event.clientY &&
                    event.clientY <= rect.top + rect.height &&
                    rect.left <= event.clientX &&
                    event.clientX <= rect.left + rect.width
                );
                if (isDialogContent) {
                    return;
                }
                dialog.close();
            });
        }

        const updateSelectedCandidateUI = (radio) => {
            if (!radio) {
                return;
            }
            const name = radio.getAttribute('data-candidate-name');
            const number = radio.getAttribute('data-candidate-number');
            const photo = radio.getAttribute('data-candidate-photo');

            if (statusText) {
                statusText.textContent = 'Pilihan Anda: ' + name + ' (' + number + ')';
            }
            if (dialogName) {
                dialogName.textContent = name;
            }
            if (dialogNumber) {
                dialogNumber.textContent = number;
            }
            if (dialogImg && photo) {
                dialogImg.src = photo;
            }
            if (golputWarning) {
                golputWarning.hidden = true;
            }
        };

        radios.forEach((radio) => {
            radio.addEventListener('change', () => {
                if (radio.checked) {
                    updateSelectedCandidateUI(radio);
                }
            });
        });

        quickSelectBtns.forEach((btn) => {
            btn.addEventListener('click', () => {
                const radioId = btn.getAttribute('data-radio-id');
                const radio = document.getElementById(radioId);
                if (radio) {
                    radio.checked = true;
                    updateSelectedCandidateUI(radio);
                    if (dialog && !dialog.open) {
                        dialog.showModal();
                    }
                }
            });
        });

        if (openModalBtn) {
            openModalBtn.addEventListener('click', (event) => {
                const checkedRadio = document.querySelector('.candidate-radio-input:checked');
                if (!checkedRadio) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    if (golputWarning) {
                        golputWarning.hidden = false;
                        golputWarning.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    if (dialog && dialog.open) {
                        dialog.close();
                    }
                    return;
                }
                updateSelectedCandidateUI(checkedRadio);
                if (dialog && !dialog.open) {
                    dialog.showModal();
                }
            });
        }

        if (cancelDialogBtn && dialog) {
            cancelDialogBtn.addEventListener('click', () => {
                if (dialog.open) {
                    dialog.close();
                }
            });
        }

        if (voteForm && finalSubmitBtn) {
            voteForm.addEventListener('submit', () => {
                if (voteForm.checkValidity()) {
                    finalSubmitBtn.disabled = true;
                    finalSubmitBtn.innerHTML = '<span>Menyimpan Suara...</span>';
                }
            });
        }

        // Countdown Timer Bilik Suara
        const timerPill = document.getElementById('booth-timer-pill');
        const timerDisplay = document.getElementById('booth-timer-display');
        if (timerPill && timerDisplay) {
            let remaining = parseInt(timerPill.getAttribute('data-remaining') || '300', 10);

            const renderTimer = () => {
                const mins = String(Math.floor(remaining / 60)).padStart(2, '0');
                const secs = String(remaining % 60).padStart(2, '0');
                timerDisplay.textContent = mins + ':' + secs;
                if (remaining <= 60) {
                    timerPill.setAttribute('data-warning', 'true');
                }
            };

            renderTimer();

            const intervalId = setInterval(() => {
                remaining -= 1;
                if (remaining <= 0) {
                    clearInterval(intervalId);
                    window.location.href = "{{ route('vote.token') }}";
                    return;
                }
                renderTimer();
            }, 1000);
        }
    });
</script>
@endsection
