<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voting - Masukkan Token</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container" style="max-width: 450px;">
        <div class="card shadow border-0">
            <div class="card-body p-4 text-center">
                <h3 class="mb-3">E-Voting Organisasi</h3>
                <p class="text-muted mb-4">Masukkan kode token unik yang telah diberikan panitia.</p>

                @if(session('error'))
                    <div class="alert alert-danger py-2">{{ session('error') }}</div>
                @endif

                <form action="{{ route('vote.token.verify') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="token" class="form-control form-control-lg text-center" 
                               placeholder="CONTOH: A8X19B" required maxlength="10" autofocus 
                               style="text-transform: uppercase; letter-spacing: 3px; font-weight: bold;">
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Buka Bilik Suara</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>