<?php

namespace Tests\Feature\Annuaire;

use App\Models\Profil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAnnuaireTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_annuaire_index_retourne_enveloppe_standard(): void
    {
        Profil::factory()->publie()->count(3)->create();

        $response = $this->getJson('/api/annuaire');

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'slug', 'nom', 'prenom', 'type_profil'],
            ],
            'meta' => ['total', 'per_page', 'current_page', 'last_page'],
        ]);
        $response->assertJsonPath('success', true);
    }

    public function test_api_omet_email_si_masquer_email_true(): void
    {
        Profil::factory()->publie()->masque()->create([
            'email' => 'secret@example.com',
            'tel' => '+221770000000',
        ]);

        $response = $this->getJson('/api/annuaire');

        $response->assertOk();
        $payload = $response->json('data.0');
        $this->assertArrayNotHasKey('email', $payload);
        $this->assertArrayNotHasKey('tel', $payload);
    }

    public function test_api_inclut_email_si_non_masque(): void
    {
        Profil::factory()->publie()->create([
            'email' => 'public@example.com',
            'masquer_email' => false,
            'masquer_tel' => false,
        ]);

        $response = $this->getJson('/api/annuaire');

        $response->assertOk();
        $this->assertEquals('public@example.com', $response->json('data.0.email'));
    }

    public function test_api_ne_renvoie_jamais_champs_administratifs(): void
    {
        Profil::factory()->publie()->create();

        $response = $this->getJson('/api/annuaire');

        $payload = $response->json('data.0');
        $this->assertArrayNotHasKey('etat_publication', $payload);
        $this->assertArrayNotHasKey('legacy_sans_consentement', $payload);
    }

    public function test_api_show_retourne_404_si_non_publie(): void
    {
        $profil = Profil::factory()->enAttente()->create(['slug' => 'detail-en-attente']);

        $response = $this->getJson('/api/annuaire/'.$profil->slug);

        $response->assertNotFound();
    }

    public function test_api_filtre_par_type_profil(): void
    {
        Profil::factory()->publie()->create(['type_profil' => 'expert']);
        Profil::factory()->publie()->create(['type_profil' => 'etudiant']);

        $response = $this->getJson('/api/annuaire?type=expert');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('expert', $response->json('data.0.type_profil'));
    }

    public function test_api_show_retourne_bio_longue(): void
    {
        $profil = Profil::factory()->publie()->create([
            'slug' => 'detail-publie',
            'bio_longue' => 'Biographie très longue pour le détail.',
        ]);

        $response = $this->getJson('/api/annuaire/'.$profil->slug);

        $response->assertOk();
        $response->assertJsonPath('data.bio_longue', 'Biographie très longue pour le détail.');
    }
}
