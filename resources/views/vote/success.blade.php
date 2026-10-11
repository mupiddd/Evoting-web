<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suara Tercatat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container text-center" style="max-width: 500px;">
        <div class="card shadow border-0 p-4">
            <div class="card-body">
                <h1 class="display-3 text-success mb-3">✓</h1>
                <h3>Terima Kasih!</h3>
                <p class="text-muted">Suara Anda telah berhasil dicatat ke dalam sistem secara anonim.</p>
                <a href="{{ route('vote.token') }}" class="btn btn-secondary mt-3">Kembali ke Halaman Awal</a>
            </div>
        </div>
    </div>
</body>
</html>