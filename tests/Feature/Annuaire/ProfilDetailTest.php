<?php

namespace Tests\Feature\Annuaire;

use App\Models\Profil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilDetailTest extends TestCase
{
    use RefreshDatabase;

    private function urlProfil(Profil $profil): string
    {
        return '/profil/'.$profil->slug.'-'.$profil->id;
    }

    public function test_fiche_detail_publie_retourne_200(): void
    {
        $profil = Profil::factory()->publie()->create(['slug' => 'marie-diop-xyz']);

        $response = $this->get($this->urlProfil($profil));

        $response->assertOk();
        $response->assertSee($profil->nom);
    }

    public function test_fiche_detail_en_attente_retourne_404(): void
    {
        $profil = Profil::factory()->enAttente()->create(['slug' => 'en-attente-xyz']);

        $response = $this->get($this->urlProfil($profil));

        $response->assertNotFound();
    }

    public function test_fiche_detail_archive_retourne_404(): void
    {
        $profil = Profil::factory()->archive()->create(['slug' => 'archive-xyz']);

        $response = $this->get($this->urlProfil($profil));

        $response->assertNotFound();
    }

    public function test_email_masque_jamais_dans_html_public(): void
    {
        $profil = Profil::factory()->publie()->masque()->create([
            'email' => 'secret@example.com',
            'tel' => '+221770000000',
        ]);

        $response = $this->get($this->urlProfil($profil));

        $response->assertOk();
        $response->assertDontSee('secret@example.com');
        $response->assertDontSee('+221770000000');
    }

    public function test_email_visible_si_non_masque(): void
    {
        $profil = Profil::factory()->publie()->create([
            'email' => 'public@example.com',
            'tel' => '+221770000000',
            'masquer_email' => false,
            'masquer_tel' => false,
        ]);

        $response = $this->get($this->urlProfil($profil));

        $response->assertOk();
        $response->assertSee('public@example.com');
    }
}
