<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Candidate;
use App\Models\Token;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat 2 kandidat contoh
        Candidate::create([
            'name' => 'Kandidat 01 - Budi & Siti',
            'vision_mission' => 'Mewujudkan lingkungan organisasi yang inklusif, adaptif, dan berprestasi.',
            'photo' => null
        ]);

        Candidate::create([
            'name' => 'Kandidat 02 - Andi & Dewi',
            'vision_mission' => 'Mendorong inovasi teknologi dan kolaborasi aktif antar anggota.',
            'photo' => null
        ]);

        // 2. Buat 5 token acak unik (panjang 6 karakter)
        for ($i = 0; $i < 5; $i++) {
            Token::create([
                'token' => strtoupper(Str::random(6)),
                'is_used' => false
            ]);
        }
    }
}