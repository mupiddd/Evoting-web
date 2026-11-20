@extends('vote.layout')

@section('title', 'VoteForge — Manajemen Kandidat & Foto')

@section('styles')
<style>
    .admin-subnav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-block-end: 1.75rem;
        flex-wrap: wrap;
    }

    .admin-tabs {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .candidates-admin-layout {
        display: grid;
        grid-template-columns: 0.95fr 1.05fr;
        gap: 1.75rem;
        align-items: start;
    }

    .admin-candidate-card {
        display: flex;
        gap: 1.15rem;
        padding: 1.25rem;
        border-radius: 1.25rem;
        background: rgba(4, 15, 35, 0.58);
        border: 1px solid rgba(125, 211, 252, 0.25);
        margin-block-end: 1.15rem;
        align-items: flex-start;
    }

    .admin-candidate-thumb {
        width: 96px;
        height: 105px;
        border-radius: 0.95rem;
        object-fit: cover;
        border: 1.5px solid rgba(56, 189, 248, 0.55);
        flex-shrink: 0;
        background: #071936;
    }

    .admin-candidate-info {
        flex-grow: 1;
    }

    .admin-candidate-actions {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin-block-top: 0.9rem;
        flex-wrap: wrap;
    }

    .btn-mini-action {
        padding: 0.42rem 0.9rem;
        border-radius: 0.7rem;
        font-size: 0.8rem;
        font-weight: 600;
        background: rgba(15, 42, 82, 0.65);
        border: 1px solid rgba(125, 211, 252, 0.35);
        color: #e0f2fe;
        cursor: pointer;
    }

    .btn-mini-action:hover {
        background: rgba(56, 189, 248, 0.25);
        border-color: var(--accent-cyan);
    }

    .btn-mini-danger {
        background: rgba(136, 19, 55, 0.45);
        border-color: rgba(251, 113, 133, 0.4);
        color: #fecdd3;
    }

    @media (max-width: 920px) {
        .candidates-admin-layout {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
@php
    $defaultAvatar = "data:image/svg+xml;utf8," . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 220" fill="none"><rect width="200" height="220" fill="#0c264e"/><circle cx="100" cy="80" r="35" fill="#38bdf8" fill-opacity="0.35"/><path d="M40 190 C40 135 160 135 160 190" fill="#38bdf8" fill-opacity="0.25"/></svg>');
@endphp

<div class="admin-subnav">
    <div>
        <div class="eyebrow-label">
            <span>MANAJEMEN KANDIDAT &amp; FOTO</span>
        </div>
        <h1 class="hero-title" style="font-size: 2.1rem; margin-block-end: 0;">
            Kelola Data &amp; <span class="gradient-text" style="display: inline;">Foto Kandidat</span>
        </h1>
    </div>

    <div class="admin-tabs">
        <a href="{{ route('tokens.index') }}" class="btn-glass-secondary">
            <span>Manajemen Token ({{ $totalTokens ?? 0 }})</span>
        </a>
        <a href="{{ route('candidates.index') }}" class="btn-pill-primary">
            <span>Manajemen Kandidat ({{ $candidates->count() }})</span>
        </a>
        <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn-glass-secondary" style="border-color: rgba(251, 113, 133, 0.45); color: #fecdd3;">
                <span>Keluar</span>
            </button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="glass-alert glass-alert-success" role="status">
        <span>{{ session('success') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="glass-alert glass-alert-error" role="alert">
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<div class="candidates-admin-layout">
    <!-- Left: Add New Candidate Form -->
    <section class="glass-panel" aria-labelledby="add-candidate-heading">
        <h2 id="add-candidate-heading" style="font-size: 1.25rem; font-weight: 800; margin-block-end: 0.4rem;">
            Tambah Pasangan Kandidat
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-block-end: 1.35rem;">
            Foto kandidat yang diunggah akan otomatis tampil di galeri kanan halaman utama dan di bilik suara.
        </p>

        <form action="{{ route('candidates.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="candidate-name" class="form-label">Nama Pasangan Calon (Wajib)</label>
                <input
                    type="text"
                    id="candidate-name"
                    name="name"
                    class="glass-input"
                    placeholder="Contoh: Kandidat 01 — Budi & Siti"
                    required
                    maxlength="255"
                    value="{{ old('name') }}"
                >
            </div>

            <div class="form-group">
                <label for="candidate-vision" class="form-label">Visi &amp; Misi (Wajib)</label>
                <textarea
                    id="candidate-vision"
                    name="vision_mission"
                    rows="4"
                    class="glass-textarea"
                    placeholder="Tuliskan visi dan misi unggulan pasangan calon..."
                    required
                >{{ old('vision_mission') }}</textarea>
            </div>

            <div class="form-group">
                <label for="candidate-photo-file" class="form-label">Unggah File Foto Kandidat (JPG/PNG/WEBP, maks 2MB)</label>
                <input
                    type="file"
                    id="candidate-photo-file"
                    name="photo_file"
                    class="glass-input"
                    accept="image/jpeg,image/png,image/webp"
                    aria-describedby="photo-file-help"
                >
                <span id="photo-file-help" class="form-hint">
                    File foto disimpan secara terenkripsi di database PostgreSQL sehingga aman di Vercel Serverless.
                </span>
            </div>

            <div class="form-group">
                <label for="candidate-photo-url" class="form-label">Atau Gunakan URL Foto Eksternal (Opsional)</label>
                <input
                    type="url"
                    id="candidate-photo-url"
                    name="photo_url"
                    class="glass-input"
                    placeholder="https://images.unsplash.com/..."
                    value="{{ old('photo_url') }}"
                >
            </div>

            <button type="submit" class="btn-pill-primary" style="width: 100%;">
                <span>Simpan Kandidat Baru</span>
                <span aria-hidden="true">+</span>
            </button>
        </form>
    </section>

    <!-- Right: Existing Candidates List -->
    <section class="glass-panel" aria-labelledby="list-candidate-heading">
        <h2 id="list-candidate-heading" style="font-size: 1.25rem; font-weight: 800; margin-block-end: 1.25rem;">
            Daftar Kandidat Terdaftar ({{ $candidates->count() }})
        </h2>

        @forelse($candidates as $index => $candidate)
            <article class="admin-candidate-card">
                <img
                    src="{{ $candidate->photo ?: $defaultAvatar }}"
                    alt="Foto {{ $candidate->name }}"
                    width="96"
                    height="105"
                    class="admin-candidate-thumb"
                    loading="lazy"
                >
                <div class="admin-candidate-info">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <span class="candidate-badge-pill">NO. URUT {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <span style="font-size: 0.78rem; color: var(--accent-cyan); font-weight: 700;">
                            {{ $candidate->votes_count }} Suara
                        </span>
                    </div>

                    <h3 style="font-size: 1.1rem; font-weight: 800; color: #ffffff; margin-block: 0.25rem;">
                        {{ $candidate->name }}
                    </h3>

                    <p style="font-size: 0.84rem; color: var(--text-secondary); white-space: pre-line;">
                        {{ Str::limit($candidate->vision_mission, 160) }}
                    </p>

                    <div class="admin-candidate-actions">
                        <button
                            type="button"
                            class="btn-mini-action"
                            commandfor="edit-candidate-dialog-{{ $candidate->id }}"
                            command="show-modal"
                        >
                            Edit Data &amp; Foto
                        </button>

                        <form action="{{ route('candidates.destroy', $candidate) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-mini-action btn-mini-danger">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </article>

            <!-- Modal Edit Candidate (<dialog closedby="any">) -->
            <dialog
                id="edit-candidate-dialog-{{ $candidate->id }}"
                class="glass-dialog"
                closedby="any"
                aria-labelledby="edit-title-{{ $candidate->id }}"
            >
                <h2 id="edit-title-{{ $candidate->id }}" style="font-size: 1.25rem; font-weight: 800; margin-block-end: 1rem;">
                    Edit Kandidat: {{ $candidate->name }}
                </h2>

                <form action="{{ route('candidates.update', $candidate) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="edit-name-{{ $candidate->id }}" class="form-label">Nama Pasangan Calon</label>
                        <input
                            type="text"
                            id="edit-name-{{ $candidate->id }}"
                            name="name"
                            class="glass-input"
                            value="{{ $candidate->name }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="edit-vision-{{ $candidate->id }}" class="form-label">Visi &amp; Misi</label>
                        <textarea
                            id="edit-vision-{{ $candidate->id }}"
                            name="vision_mission"
                            rows="4"
                            class="glass-textarea"
                            required
                        >{{ $candidate->vision_mission }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="edit-file-{{ $candidate->id }}" class="form-label">Ganti File Foto (Opsional)</label>
                        <input
                            type="file"
                            id="edit-file-{{ $candidate->id }}"
                            name="photo_file"
                            class="glass-input"
                            accept="image/jpeg,image/png,image/webp"
                        >
                    </div>

                    <div class="form-group">
                        <label for="edit-url-{{ $candidate->id }}" class="form-label">Atau Ganti URL Foto (Opsional)</label>
                        <input
                            type="url"
                            id="edit-url-{{ $candidate->id }}"
                            name="photo_url"
                            class="glass-input"
                            value="{{ Str::startsWith((string) $candidate->photo, 'http') ? $candidate->photo : '' }}"
                            placeholder="https://..."
                        >
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-block-start: 1.25rem;">
                        <button
                            type="button"
                            class="btn-glass-secondary"
                            commandfor="edit-candidate-dialog-{{ $candidate->id }}"
                            command="close"
                        >
                            Batal
                        </button>
                        <button type="submit" class="btn-pill-primary">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </dialog>
        @empty
            <p style="color: var(--text-muted); text-align: center; padding: 2rem;">
                Belum ada kandidat terdaftar. Silakan tambahkan kandidat baru melalui formulir di samping.
            </p>
        @endforelse
    </section>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Fallback for browsers without native closedby support (light-dismiss-a-dialog guide)
        const dialogs = document.querySelectorAll('dialog.glass-dialog');
        if (!('closedBy' in HTMLDialogElement.prototype)) {
            dialogs.forEach((dialog) => {
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
            });
        }
    });
</script>
@endsection
