<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Token;
use App\Models\Vote;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoteController extends Controller
{
    private const BOOTH_DURATION_SECONDS = 300;

    public function showTokenForm(): View
    {
        $candidates = Candidate::orderBy('id')->get();
        $totalVotes = Vote::count();

        return view('vote.token', compact('candidates', 'totalVotes'));
    }

    public function checkToken(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string', 'max:10'],
        ]);

        $tokenInput = strtoupper(trim((string) $request->input('token')));
        $token = Token::where('token', $tokenInput)->first();

        if (! $token) {
            return back()->with('error', 'Token tidak ditemukan. Periksa kembali kode token Anda.');
        }

        if ($token->is_used) {
            return back()->with('error', 'Token ini sudah pernah digunakan untuk memilih.');
        }

        session([
            'voting_token' => $token->token,
            'voting_expires_at' => now()->addSeconds(self::BOOTH_DURATION_SECONDS)->timestamp,
        ]);

        return redirect()->route('vote.booth');
    }

    public function showBooth(): View|RedirectResponse
    {
        if (! session()->has('voting_token')) {
            return redirect()->route('vote.token')->with('error', 'Silakan masukkan token terlebih dahulu.');
        }

        $expiresAt = (int) session('voting_expires_at', now()->addSeconds(self::BOOTH_DURATION_SECONDS)->timestamp);
        $remainingSeconds = max(0, $expiresAt - now()->timestamp);

        if ($remainingSeconds <= 0) {
            session()->forget(['voting_token', 'voting_expires_at']);

            return redirect()->route('vote.token')->with('error', 'Waktu sesi bilik suara telah habis. Silakan masukkan kembali token Anda.');
        }

        $candidates = Candidate::orderBy('id')->get();

        return view('vote.booth', compact('candidates', 'remainingSeconds'));
    }

    public function submitVote(Request $request): RedirectResponse
    {
        $request->validate([
            'candidate_id' => ['required', 'exists:candidates,id'],
        ], [
            'candidate_id.required' => 'Anda wajib memilih salah satu kandidat sebelum mengirim suara (Golput tidak diperbolehkan).',
            'candidate_id.exists' => 'Kandidat yang dipilih tidak valid.',
        ]);

        $sessionToken = session('voting_token');
        $expiresAt = (int) session('voting_expires_at', 0);

        if (! $sessionToken || ($expiresAt > 0 && now()->timestamp > $expiresAt)) {
            session()->forget(['voting_token', 'voting_expires_at']);

            return redirect()->route('vote.token')->with('error', 'Sesi pemilihan telah berakhir. Silakan masukkan kembali token Anda.');
        }

        $usedSuccess = DB::transaction(function () use ($request, $sessionToken): bool {
            $token = Token::where('token', $sessionToken)
                ->where('is_used', false)
                ->lockForUpdate()
                ->first();

            if (! $token) {
                return false;
            }

            Vote::create([
                'candidate_id' => (int) $request->input('candidate_id'),
            ]);

            $token->update([
                'is_used' => true,
                'used_at' => now(),
            ]);

            return true;
        });

        session()->forget(['voting_token', 'voting_expires_at']);

        if (! $usedSuccess) {
            return redirect()->route('vote.token')->with('error', 'Token sudah digunakan atau tidak valid.');
        }

        return redirect()->route('vote.success');
    }

    public function success(): View
    {
        return view('vote.success');
    }

    public function results(): View
    {
        $candidates = Candidate::withCount('votes')
            ->orderByDesc('votes_count')
            ->orderBy('id')
            ->get();

        $totalVotes = Vote::count();
        $totalTokens = Token::count();
        $usedTokens = Token::where('is_used', true)->count();
        $participationRate = $totalTokens > 0 ? round(($usedTokens / $totalTokens) * 100, 1) : 0.0;

        return view('vote.results', compact(
            'candidates',
            'totalVotes',
            'totalTokens',
            'usedTokens',
            'participationRate'
        ));
    }

    public function showAdminLogin(): View|RedirectResponse
    {
        if ($this->isAdminAuthenticated()) {
            return redirect()->route('tokens.index');
        }

        return view('vote.admin_login');
    }

    public function adminLogin(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $expectedPassword = (string) config('app.admin_password', 'admin123');
        $inputPassword = (string) $request->input('password');

        if (! hash_equals($expectedPassword, $inputPassword)) {
            return back()->with('error', 'Kata sandi panitia tidak sesuai.');
        }

        session(['is_admin_authenticated' => true]);

        return redirect()->route('tokens.index')->with('success', 'Selamat datang di Panel Panitia E-Voting.');
    }

    public function adminLogout(): RedirectResponse
    {
        session()->forget('is_admin_authenticated');

        return redirect()->route('vote.token');
    }

    public function adminTokens(Request $request): View|RedirectResponse
    {
        if (! $this->isAdminAuthenticated()) {
            return redirect()->route('admin.login')->with('error', 'Silakan masuk sebagai panitia terlebih dahulu.');
        }

        $statusFilter = (string) $request->query('status', 'all');
        $query = Token::orderByDesc('id');

        if ($statusFilter === 'available') {
            $query->where('is_used', false);
        } elseif ($statusFilter === 'used') {
            $query->where('is_used', true);
        }

        $tokens = $query->paginate(20)->withQueryString();
        $totalTokens = Token::count();
        $usedTokens = Token::where('is_used', true)->count();
        $availableTokens = Token::where('is_used', false)->count();
        $totalCandidates = Candidate::count();

        return view('vote.admin_tokens', compact(
            'tokens',
            'totalTokens',
            'usedTokens',
            'availableTokens',
            'totalCandidates',
            'statusFilter'
        ));
    }

    public function generateTokens(Request $request): RedirectResponse
    {
        if (! $this->isAdminAuthenticated()) {
            return redirect()->route('admin.login')->with('error', 'Silakan masuk sebagai panitia terlebih dahulu.');
        }

        $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        $amount = (int) $request->input('amount');
        $newTokens = [];
        $generatedCodes = [];

        for ($i = 0; $i < $amount; $i++) {
            $code = strtoupper(Str::random(6));

            while (isset($generatedCodes[$code]) || Token::where('token', $code)->exists()) {
                $code = strtoupper(Str::random(6));
            }

            $generatedCodes[$code] = true;
            $newTokens[] = [
                'token' => $code,
                'is_used' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Token::insert($newTokens);

        return back()->with('success', "Berhasil membuat {$amount} token pemilih baru!");
    }

    public function destroyToken(Token $token): RedirectResponse
    {
        if (! $this->isAdminAuthenticated()) {
            return redirect()->route('admin.login')->with('error', 'Silakan masuk sebagai panitia terlebih dahulu.');
        }

        if ($token->is_used) {
            return back()->with('error', 'Token yang sudah digunakan tidak dapat dihapus demi integritas data suara.');
        }

        $token->delete();

        return back()->with('success', "Token {$token->token} berhasil dihapus.");
    }

    public function resetUnusedTokens(): RedirectResponse
    {
        if (! $this->isAdminAuthenticated()) {
            return redirect()->route('admin.login')->with('error', 'Silakan masuk sebagai panitia terlebih dahulu.');
        }

        $deletedCount = Token::where('is_used', false)->delete();

        return back()->with('success', "Berhasil menghapus {$deletedCount} token yang belum terpakai.");
    }

    public function adminCandidates(): View|RedirectResponse
    {
        if (! $this->isAdminAuthenticated()) {
            return redirect()->route('admin.login')->with('error', 'Silakan masuk sebagai panitia terlebih dahulu.');
        }

        $candidates = Candidate::withCount('votes')->orderBy('id')->get();
        $totalTokens = Token::count();

        return view('vote.admin_candidates', compact('candidates', 'totalTokens'));
    }

    public function storeCandidate(Request $request): RedirectResponse
    {
        if (! $this->isAdminAuthenticated()) {
            return redirect()->route('admin.login')->with('error', 'Silakan masuk sebagai panitia terlebih dahulu.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'vision_mission' => ['required', 'string', 'max:5000'],
            'photo_url' => ['nullable', 'url', 'max:2048'],
            'photo_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $photoValue = $this->resolveCandidatePhoto(
            $request->file('photo_file'),
            $validated['photo_url'] ?? null,
            null
        );

        Candidate::create([
            'name' => $validated['name'],
            'vision_mission' => $validated['vision_mission'],
            'photo' => $photoValue,
        ]);

        return back()->with('success', "Kandidat {$validated['name']} berhasil ditambahkan!");
    }

    public function updateCandidate(Request $request, Candidate $candidate): RedirectResponse
    {
        if (! $this->isAdminAuthenticated()) {
            return redirect()->route('admin.login')->with('error', 'Silakan masuk sebagai panitia terlebih dahulu.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'vision_mission' => ['required', 'string', 'max:5000'],
            'photo_url' => ['nullable', 'url', 'max:2048'],
            'photo_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $photoValue = $this->resolveCandidatePhoto(
            $request->file('photo_file'),
            $validated['photo_url'] ?? null,
            $candidate->photo
        );

        $candidate->update([
            'name' => $validated['name'],
            'vision_mission' => $validated['vision_mission'],
            'photo' => $photoValue,
        ]);

        return back()->with('success', "Data kandidat {$candidate->name} berhasil diperbarui.");
    }

    public function destroyCandidate(Candidate $candidate): RedirectResponse
    {
        if (! $this->isAdminAuthenticated()) {
            return redirect()->route('admin.login')->with('error', 'Silakan masuk sebagai panitia terlebih dahulu.');
        }

        $candidateName = $candidate->name;
        $candidate->delete();

        return back()->with('success', "Kandidat {$candidateName} berhasil dihapus.");
    }

    private function isAdminAuthenticated(): bool
    {
        return (bool) session('is_admin_authenticated', false);
    }

    private function resolveCandidatePhoto(?UploadedFile $uploadedFile, ?string $photoUrl, ?string $existingPhoto): ?string
    {
        if ($uploadedFile instanceof UploadedFile && $uploadedFile->isValid()) {
            $mimeType = $uploadedFile->getMimeType() ?: 'image/jpeg';
            $contents = file_get_contents($uploadedFile->getRealPath());

            if ($contents !== false) {
                return 'data:'.$mimeType.';base64,'.base64_encode($contents);
            }
        }

        if ($photoUrl !== null && trim($photoUrl) !== '') {
            return trim($photoUrl);
        }

        return $existingPhoto;
    }
}
