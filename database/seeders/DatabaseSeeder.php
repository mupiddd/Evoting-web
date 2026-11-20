<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Token;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (Candidate::count() === 0) {
            Candidate::create([
                'name' => 'Kandidat 01 — Budi Santoso & Siti Rahma',
                'vision_mission' => "Visi: Mewujudkan ekosistem organisasi yang inklusif, transparan, dan berdaya saing global berbasis transformasi digital.\nMisi: 1. Digitalisasi layanan dan aspirasi anggota secara terpadu. 2. Mengembangkan program inkubasi talenta dan kepemimpinan. 3. Memperkuat kolaborasi lintas komunitas yang berkelanjutan.",
                'photo' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=800&q=80',
            ]);

            Candidate::create([
                'name' => 'Kandidat 02 — Andi Pratama & Dewi Lestari',
                'vision_mission' => "Visi: Mendorong inovasi teknologi, sinergi kreatif, dan tata kelola organisasi yang progresif serta akuntabel.\nMisi: 1. Membangun ruang kolaborasi terbuka bagi seluruh anggota. 2. Meningkatkan kualitas riset, kompetisi, dan karya inovatif. 3. Menghadirkan pelaporan program kerja yang transparan dan real-time.",
                'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=800&q=80',
            ]);

            Candidate::create([
                'name' => 'Kandidat 03 — Reza Mahendra & Nadia Putri',
                'vision_mission' => "Visi: Menjadikan organisasi sebagai rumah aspirasi yang harmonis, berdampak sosial tinggi, dan berorientasi masa depan.\nMisi: 1. Memperluas jejaring kemitraan strategis dengan industri dan akademisi. 2. Mengoptimalkan kesejahteraan serta pengembangan karier anggota. 3. Menyelenggarakan kegiatan sosial dan pengabdian berdampak nyata.",
                'photo' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=800&q=80',
            ]);
        }

        if (Token::count() === 0) {
            for ($i = 0; $i < 10; $i++) {
                Token::create([
                    'token' => strtoupper(Str::random(6)),
                    'is_used' => false,
                ]);
            }
        }
    }
}
