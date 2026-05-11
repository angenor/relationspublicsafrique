<?php

declare(strict_types=1);

namespace Tests\Unit\Annuaire;

use App\Models\Pays;
use App\Models\Profil;
use App\Services\Annuaire\ProfilExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilExportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_commence_par_bom_utf8(): void
    {
        Profil::factory()->publie()->create();

        $contenu = app(ProfilExportService::class)->exportRaw([], 'csv');

        $this->assertStringStartsWith("\xEF\xBB\xBF", $contenu, 'Le CSV doit commencer par le BOM UTF-8.');
    }

    public function test_csv_utilise_le_delimiteur_point_virgule(): void
    {
        Profil::factory()->publie()->create(['nom' => 'Diop', 'prenom' => 'Marie']);

        $contenu = app(ProfilExportService::class)->exportRaw([], 'csv');

        $this->assertStringContainsString(';', $contenu);
    }

    public function test_csv_encode_correctement_les_accents(): void
    {
        $pays = Pays::factory()->create(['name' => 'Côte d\'Ivoire']);
        Profil::factory()->publie()->create(['ville' => 'Abidjan', 'pays_id' => $pays->id]);

        $contenu = app(ProfilExportService::class)->exportRaw([], 'csv');

        $this->assertStringContainsString('Côte d\'Ivoire', $contenu);
    }
}
