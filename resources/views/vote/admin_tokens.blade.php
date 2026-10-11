<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Token Pemilihan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container" style="max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-0">Panel Token Panitia</h3>
                <p class="text-muted mb-0">Kelola dan hasilkan token pemilih secara otomatis.</p>
            </div>
            <div>
                <a href="{{ route('vote.results') }}" class="btn btn-outline-primary me-2">Lihat Hasil</a>
                <a href="{{ route('vote.token') }}" class="btn btn-outline-secondary">Bilik Suara</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Ringkasan Status -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 text-center">
                    <span class="text-muted">Total Token</span>
                    <h3 class="fw-bold mb-0">{{ $totalTokens }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 text-center">
                    <span class="text-muted">Tersedia</span>
                    <h3 class="fw-bold text-success mb-0">{{ $availableTokens }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 text-center">
                    <span class="text-muted">Sudah Digunakan</span>
                    <h3 class="fw-bold text-danger mb-0">{{ $usedTokens }}</h3>
                </div>
            </div>
        </div>

        <!-- Form Generator -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="card-title mb-3">Buat Token Baru</h5>
                <form action="{{ route('tokens.generate') }}" method="POST" class="row g-3 align-items-center">
                    @csrf
                    <div class="col-auto">
                        <label for="amount" class="col-form-label">Jumlah Token:</label>
                    </div>
                    <div class="col-auto">
                        <input type="number" name="amount" id="amount" class="form-control" value="10" min="1" max="500" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Generate Token</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Daftar Token -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="card-title mb-3">Daftar Token</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Kode Token</th>
                                <th>Status</th>
                                <th>Waktu Digunakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tokens as $index => $item)
                                <tr>
                                    <td>{{ $tokens->firstItem() + $index }}</td>
                                    <td>
                                        <code class="fs-6 fw-bold text-dark">{{ $item->token }}</code>
                                    </td>
                                    <td>
                                        @if($item->is_used)
                                            <span class="badge bg-danger">Sudah Dipakai</span>
                                        @else
                                            <span class="badge bg-success">Tersedia</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">
                                        {{ $item->used_at ? $item->used_at : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Belum ada token. Silakan generate di atas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $tokens->links() }}
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>