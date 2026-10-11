<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Perolehan Suara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container" style="max-width: 800px;">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body text-center p-4">
                <h2 class="mb-1">Hasil Perolehan Suara</h2>
                <p class="text-muted mb-0">Total Suara Masuk: <strong>{{ $totalVotes }}</strong></p>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                @foreach($candidates as $candidate)
                    @php
                        $percentage = $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes) * 100, 1) : 0;
                    @endphp
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h5 class="mb-0">{{ $candidate->name }}</h5>
                            <span class="text-muted fw-bold">{{ $candidate->votes_count }} suara ({{ $percentage }}%)</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: {{ $percentage }}%;" 
                                 aria-valuenow="{{ $percentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $percentage }}%
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="text-center mt-4 pt-3 border-top">
                    <a href="{{ route('vote.token') }}" class="btn btn-outline-secondary">Kembali ke Bilik Suara</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>