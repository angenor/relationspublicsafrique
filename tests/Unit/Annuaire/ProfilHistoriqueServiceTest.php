<?php

declare(strict_types=1);

namespace Tests\Unit\Annuaire;

use App\Services\Annuaire\ProfilHistoriqueService;
use Tests\TestCase;

class ProfilHistoriqueServiceTest extends TestCase
{
    public function test_sanitize_omet_les_champs_normalises_et_techniques(): void
    {
        $service = new ProfilHistoriqueService;

        $diff = [
            'nom' => ['avant' => 'Diop', 'apres' => 'Sarr'],
            'nom_normalise' => ['avant' => 'diop', 'apres' => 'sarr'],
            'prenom_normalise' => ['avant' => 'aminata', 'apres' => 'aminata'],
            'organisation_normalisee' => ['avant' => 'rp afrique', 'apres' => 'rp afrique'],
            'ville_normalisee' => ['avant' => 'dakar', 'apres' => 'abidjan'],
            'updated_at' => ['avant' => '2026-05-01', 'apres' => '2026-05-11'],
            'created_at' => ['avant' => '2026-01-01', 'apres' => '2026-01-01'],
            'fonction' => ['avant' => 'X', 'apres' => 'Y'],
        ];

        $clean = $service->sanitizeDiff($diff);

        $this->assertArrayHasKey('nom', $clean);
        $this->assertArrayHasKey('fonction', $clean);
        $this->assertArrayNotHasKey('nom_normalise', $clean);
        $this->assertArrayNotHasKey('prenom_normalise', $clean);
        $this->assertArrayNotHasKey('organisation_normalisee', $clean);
        $this->assertArrayNotHasKey('ville_normalisee', $clean);
        $this->assertArrayNotHasKey('updated_at', $clean);
        $this->assertArrayNotHasKey('created_at', $clean);
    }

    public function test_sanitize_preserve_la_structure_avant_apres(): void
    {
        $service = new ProfilHistoriqueService;

        $diff = ['ville' => ['avant' => 'Dakar', 'apres' => 'Lomé']];

        $clean = $service->sanitizeDiff($diff);

        $this->assertSame(['ville' => ['avant' => 'Dakar', 'apres' => 'Lomé']], $clean);
    }

    public function test_sanitize_retourne_tableau_vide_si_uniquement_champs_ignores(): void
    {
        $service = new ProfilHistoriqueService;

        $diff = [
            'nom_normalise' => ['avant' => 'a', 'apres' => 'b'],
            'updated_at' => ['avant' => 't1', 'apres' => 't2'],
        ];

        $this->assertSame([], $service->sanitizeDiff($diff));
    }
}
