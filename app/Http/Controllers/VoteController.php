<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Token;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoteController extends Controller
{
    // Halaman awal untuk memasukkan token
    public function showTokenForm()
    {
        return view('vote.token');
    }

    // Memeriksa token dan menyimpannya di session
    public function checkToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $tokenInput = strtoupper(trim($request->token));
        $token = Token::where('token', $tokenInput)->first();

        if (!$token) {
            return back()->with('error', 'Token tidak ditemukan.');
        }

        if ($token->is_used) {
            return back()->with('error', 'Token ini sudah pernah digunakan.');
        }

        // Simpan token di session agar pemilih bisa mengakses bilik suara
        session(['voting_token' => $token->token]);

        return redirect()->route('vote.booth');
    }

    // Halaman bilik suara (daftar kandidat)
    public function showBooth()
    {
        if (!session()->has('voting_token')) {
            return redirect()->route('vote.token')->with('error', 'Silakan masukkan token terlebih dahulu.');
        }

        $candidates = Candidate::all();
        return view('vote.booth', compact('candidates'));
    }

    // Eksekusi pemilihan secara aman (transaksi database)
    public function submitVote(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id'
        ]);

        $sessionToken = session('voting_token');

        if (!$sessionToken) {
            return redirect()->route('vote.token')->with('error', 'Sesi pemilihan telah berakhir.');
        }

        DB::transaction(function () use ($request, $sessionToken) {
            $token = Token::where('token', $sessionToken)
                ->where('is_used', false)
                ->lockForUpdate()
                ->firstOrFail();

            // 1. Simpan suara
            Vote::create([
                'candidate_id' => $request->candidate_id
            ]);

            // 2. Tandai token sudah dipakai
            $token->update([
                'is_used' => true,
                'used_at' => now()
            ]);
        });

        // Hapus token dari session
        session()->forget('voting_token');

        return redirect()->route('vote.success');
    }

    // Halaman terima kasih setelah memilih
    public function success()
    {
        return view('vote.success');
    }

    // Halaman hasil perolehan suara
    public function results()
    {
        $candidates = Candidate::withCount('votes')->get();
        $totalVotes = Vote::count();

        return view('vote.results', compact('candidates', 'totalVotes'));
    }

    // Menampilkan halaman generator dan daftar token
    public function adminTokens()
    {
        $tokens = Token::orderBy('id', 'desc')->paginate(20);
        $totalTokens = Token::count();
        $usedTokens = Token::where('is_used', true)->count();
        $availableTokens = Token::where('is_used', false)->count();

        return view('vote.admin_tokens', compact('tokens', 'totalTokens', 'usedTokens', 'availableTokens'));
    }

    // Menghasilkan token acak unik secara massal
    public function generateTokens(Request $request)
    {
    $request->validate([
        'amount' => 'required|integer|min:1|max:500'
    ]);

    $amount = $request->amount;
    $newTokens = [];

    for ($i = 0; $i < $amount; $i++) {
        // Buat kode 6 digit alfanumerik kapital
        $code = strtoupper(Str::random(6));

        // Hindari duplikasi kode yang sudah ada
        while (Token::where('token', $code)->exists()) {
            $code = strtoupper(Str::random(6));
        }

        $newTokens[] = [
            'token' => $code,
            'is_used' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];

    }

    // Insert massal agar proses cepat
    Token::insert($newTokens);

    return back()->with('success', "Berhasil membuat {$amount} token baru!");
    }
}
