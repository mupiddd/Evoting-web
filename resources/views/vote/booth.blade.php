<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilik Suara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container" style="max-width: 900px;">
        <div class="text-center mb-5">
            <h2>Bilik Suara Pemilihan</h2>
            <p class="text-muted">Pilih satu pasangan calon di bawah ini. Pilihan bersifat rahasia dan tidak dapat diulang.</p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($candidates as $candidate)
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column text-center p-4">
                            <h4 class="card-title mb-3">{{ $candidate->name }}</h4>
                            <p class="card-text text-muted mb-4">{{ $candidate->vision_mission }}</p>

                            <form action="{{ route('vote.submit') }}" method="POST" class="mt-auto">
                                @csrf
                                <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
                                <button type="submit" class="btn btn-outline-primary btn-lg w-100" 
                                        onclick="return confirm('Apakah Anda yakin memilih {{ $candidate->name }}?')">
                                    Pilih Kandidat Ini
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>