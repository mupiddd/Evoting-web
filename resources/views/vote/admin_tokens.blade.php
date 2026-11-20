@extends('vote.layout')

@section('title', 'VoteForge — Panel Token Panitia')

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

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.15rem;
        margin-block-end: 1.75rem;
    }

    .stat-box {
        padding: 1.25rem 1.4rem;
        border-radius: 1.25rem;
        background: rgba(10, 31, 68, 0.55);
        border: 1px solid rgba(125, 211, 252, 0.25);
    }

    .stat-box span {
        display: block;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #93c5fd;
        font-weight: 600;
    }

    .stat-box strong {
        font-size: 1.8rem;
        font-weight: 800;
        color: #ffffff;
    }

    .generator-bar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1.25rem;
        flex-wrap: wrap;
        margin-block-end: 1.75rem;
    }

    .inline-generator-form {
        display: flex;
        align-items: flex-end;
        gap: 0.85rem;
        flex-wrap: wrap;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.28rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .status-available {
        background: rgba(6, 95, 70, 0.55);
        border: 1px solid rgba(52, 211, 153, 0.55);
        color: #6ee7b7;
    }

    .status-used {
        background: rgba(159, 18, 57, 0.5);
        border: 1px solid rgba(251, 113, 133, 0.55);
        color: #fda4af;
    }

    .token-code-chip {
        font-family: 'JetBrains Mono', ui-monospace, monospace;
        font-size: 0.98rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        color: var(--accent-cyan);
        background: rgba(3, 11, 23, 0.65);
        padding: 0.3rem 0.65rem;
        border-radius: 0.5rem;
        border: 1px solid rgba(56, 189, 248, 0.3);
    }

    .btn-mini-action {
        padding: 0.35rem 0.75rem;
        border-radius: 0.65rem;
        font-size: 0.78rem;
        font-weight: 600;
        background: rgba(15, 42, 82, 0.65);
        border: 1px solid rgba(125, 211, 252, 0.3);
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

    .btn-mini-danger:hover {
        background: rgba(190, 18, 60, 0.65);
    }

    /* Responsive table CSS variables for mobile stacked labels */
    .tokens-table {
        --label-1: "No";
        --label-2: "Kode Token";
        --label-3: "Status";
        --label-4: "Waktu Digunakan";
        --label-5: "Aksi";
    }
</style>
@endsection

@section('content')
<div class="admin-subnav">
    <div>
        <div class="eyebrow-label">
            <span>DASHBOARD ADMINISTRATOR</span>
        </div>
        <h1 class="hero-title" style="font-size: 2.1rem; margin-block-end: 0;">
            Panel Manajemen <span class="gradient-text" style="display: inline;">Token Pemilih</span>
        </h1>
    </div>

    <div class="admin-tabs">
        <a href="{{ route('tokens.index') }}" class="btn-pill-primary">
            <span>Manajemen Token ({{ $totalTokens }})</span>
        </a>
        <a href="{{ route('candidates.index') }}" class="btn-glass-secondary">
            <span>Manajemen Kandidat &amp; Foto ({{ $totalCandidates ?? 0 }})</span>
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

@if(session('error'))
    <div class="glass-alert glass-alert-error" role="alert">
        <span>{{ session('error') }}</span>
    </div>
@endif

<!-- Ringkasan Status Token -->
<div class="stats-row">
    <div class="stat-box">
        <span>Total Token</span>
        <strong>{{ $totalTokens }}</strong>
    </div>
    <div class="stat-box">
        <span>Token Tersedia</span>
        <strong style="color: var(--success);">{{ $availableTokens }}</strong>
    </div>
    <div class="stat-box">
        <span>Sudah Digunakan</span>
        <strong style="color: var(--danger);">{{ $usedTokens }}</strong>
    </div>
</div>

<!-- Form Generator & Filter -->
<section class="glass-panel generator-bar" aria-labelledby="generator-heading">
    <div>
        <h2 id="generator-heading" style="font-size: 1.15rem; font-weight: 800; margin-block-end: 0.75rem;">
            Buat Token Pemilih Massal
        </h2>
        <form action="{{ route('tokens.generate') }}" method="POST" class="inline-generator-form">
            @csrf
            <div>
                <label for="amount" class="form-label">Jumlah Token Baru (1&ndash;500)</label>
                <input
                    type="number"
                    name="amount"
                    id="amount"
                    class="glass-input"
                    style="width: 150px;"
                    value="10"
                    min="1"
                    max="500"
                    required
                >
            </div>
            <button type="submit" class="btn-pill-primary">
                <span>Generate Token</span>
                <span aria-hidden="true">+</span>
            </button>
        </form>
    </div>

    <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
        <a href="{{ route('tokens.index', ['status' => 'all']) }}"
           class="btn-mini-action"
           @if(($statusFilter ?? 'all') === 'all') style="border-color: var(--accent-cyan); background: rgba(56, 189, 248, 0.25);" @endif>
            Semua
        </a>
        <a href="{{ route('tokens.index', ['status' => 'available']) }}"
           class="btn-mini-action"
           @if(($statusFilter ?? 'all') === 'available') style="border-color: var(--accent-cyan); background: rgba(56, 189, 248, 0.25);" @endif>
            Tersedia
        </a>
        <a href="{{ route('tokens.index', ['status' => 'used']) }}"
           class="btn-mini-action"
           @if(($statusFilter ?? 'all') === 'used') style="border-color: var(--accent-cyan); background: rgba(56, 189, 248, 0.25);" @endif>
            Sudah Dipakai
        </a>

        @if($availableTokens > 0)
            <form action="{{ route('tokens.reset-unused') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn-mini-action btn-mini-danger">
                    Bersihkan Token Belum Terpakai
                </button>
            </form>
        @endif
    </div>
</section>

<!-- Daftar Token (Responsive Table with Sticky Header & Container Queries) -->
<section class="glass-panel" aria-labelledby="token-list-heading">
    <h2 id="token-list-heading" style="font-size: 1.15rem; font-weight: 800; margin-block-end: 1rem;">
        Daftar Token Pemilih
    </h2>

    <div class="table-wrapper">
        <table class="data-table tokens-table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Kode Token</th>
                    <th scope="col">Status</th>
                    <th scope="col">Waktu Digunakan</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tokens as $index => $item)
                    <tr>
                        <th scope="row">{{ $tokens->firstItem() + $index }}</th>
                        <td>
                            <code class="token-code-chip">{{ $item->token }}</code>
                        </td>
                        <td>
                            @if($item->is_used)
                                <span class="status-badge status-used">Sudah Dipakai</span>
                            @else
                                <span class="status-badge status-available">Tersedia</span>
                            @endif
                        </td>
                        <td style="color: var(--text-secondary);">
                            {{ $item->used_at ? $item->used_at : '-' }}
                        </td>
                        <td>
                            <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                <button
                                    type="button"
                                    class="btn-mini-action copy-token-btn"
                                    data-token="{{ $item->token }}"
                                    aria-label="Salin token {{ $item->token }}"
                                >
                                    Salin
                                </button>
                                @if(! $item->is_used)
                                    <form action="{{ route('tokens.destroy', $item) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn-mini-action btn-mini-danger"
                                            aria-label="Hapus token {{ $item->token }}"
                                        >
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <th scope="row">-</th>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                            Belum ada token pada kategori ini. Silakan generate token baru di atas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-block-start: 1.25rem;">
        {{ $tokens->links() }}
    </div>
</section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const copyButtons = document.querySelectorAll('.copy-token-btn');
        copyButtons.forEach((btn) => {
            btn.addEventListener('click', async () => {
                const token = btn.getAttribute('data-token');
                if (token && navigator.clipboard) {
                    await navigator.clipboard.writeText(token);
                    const original = btn.textContent;
                    btn.textContent = 'Tersalin!';
                    setTimeout(() => {
                        btn.textContent = original;
                    }, 1500);
                }
            });
        });
    });
</script>
@endsection
