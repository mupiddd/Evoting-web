<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Token;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class EvotingTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_token_form_and_candidates_showcase(): void
    {
        $candidate = Candidate::factory()->create([
            'name' => 'Kandidat 01 - Budi & Siti',
            'photo' => 'https://example.com/candidate1.jpg',
        ]);

        $response = $this->get(route('vote.token'));

        $response->assertOk();
        $response->assertSee('Kandidat 01 - Budi & Siti');
        $response->assertSee('https://example.com/candidate1.jpg');
    }

    public function test_voter_can_verify_token_and_cast_vote(): void
    {
        $candidate = Candidate::factory()->create();
        $token = Token::factory()->create(['token' => 'ABC123', 'is_used' => false]);

        $verifyResponse = $this->post(route('vote.token.verify'), [
            'token' => 'abc123',
        ]);

        $verifyResponse->assertRedirect(route('vote.booth'));
        $this->assertEquals('ABC123', session('voting_token'));

        $boothResponse = $this->get(route('vote.booth'));
        $boothResponse->assertOk();
        $boothResponse->assertSee($candidate->name);

        $voteResponse = $this->post(route('vote.submit'), [
            'candidate_id' => $candidate->id,
        ]);

        $voteResponse->assertRedirect(route('vote.success'));
        $this->assertDatabaseHas('votes', ['candidate_id' => $candidate->id]);
        $this->assertDatabaseHas('tokens', ['id' => $token->id, 'is_used' => true]);
    }

    public function test_used_token_cannot_be_verified_again(): void
    {
        Token::factory()->used()->create(['token' => 'USED99']);

        $response = $this->post(route('vote.token.verify'), [
            'token' => 'USED99',
        ]);

        $response->assertSessionHas('error');
        $this->assertNull(session('voting_token'));
    }

    public function test_results_page_shows_vote_counts_and_participation(): void
    {
        $candidate = Candidate::factory()->create(['name' => 'Paslon Unggulan']);
        Token::factory()->used()->create();
        Token::factory()->create();
        Vote::factory()->create(['candidate_id' => $candidate->id]);

        $response = $this->get(route('vote.results'));

        $response->assertOk();
        $response->assertSee('Paslon Unggulan');
        $response->assertSee('50%');
    }

    public function test_admin_pages_require_authentication(): void
    {
        $this->get(route('tokens.index'))->assertRedirect(route('admin.login'));
        $this->get(route('candidates.index'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_login_manage_tokens_and_candidates(): void
    {
        config(['app.admin_password' => 'secret2026']);

        $loginResponse = $this->post(route('admin.login.submit'), [
            'password' => 'secret2026',
        ]);

        $loginResponse->assertRedirect(route('tokens.index'));
        $this->assertTrue((bool) session('is_admin_authenticated'));

        // Generate tokens
        $this->post(route('tokens.generate'), ['amount' => 5])
            ->assertSessionHas('success');
        $this->assertEquals(5, Token::count());

        // Create candidate with uploaded photo converted to Base64
        $file = UploadedFile::fake()->image('candidate.jpg', 300, 300);

        $this->post(route('candidates.store'), [
            'name' => 'Paslon Baru 01',
            'vision_mission' => 'Visi dan Misi Baru',
            'photo_file' => $file,
        ])->assertSessionHas('success');

        $candidate = Candidate::where('name', 'Paslon Baru 01')->firstOrFail();
        $this->assertStringStartsWith('data:image/', (string) $candidate->photo);

        // Update candidate
        $this->put(route('candidates.update', $candidate), [
            'name' => 'Paslon Baru 01 Updated',
            'vision_mission' => 'Visi Misi Diperbarui',
            'photo_url' => 'https://example.com/new-photo.jpg',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('candidates', [
            'id' => $candidate->id,
            'name' => 'Paslon Baru 01 Updated',
            'photo' => 'https://example.com/new-photo.jpg',
        ]);

        // Delete candidate
        $this->delete(route('candidates.destroy', $candidate))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('candidates', ['id' => $candidate->id]);
    }
}
