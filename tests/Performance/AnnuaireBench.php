<?php

declare(strict_types=1);

namespace Tests\Performance;

use App\Services\Annuaire\ProfilSearchService;
use Database\Seeders\ProfilsStressSeeder;
use Tests\TestCase;

/**
 * Benchmark perf de l'annuaire.
 *
 * Mesure :
 *   - SC-002 : 95 % des recherches < 1 s sur 5 000 profils.
 *   - SC-008 : recherche + 3 filtres + tri < 1,5 s.
 *
 * Non inclus dans la suite par défaut (répertoire `tests/Performance` non
 * référencé dans `phpunit.xml`). Lancement explicite :
 *
 *     vendor/bin/phpunit tests/Performance/AnnuaireBench.php
 *
 * Pré-requis : MySQL ou SQLite local avec migrations à jour + ProfilsStressSeeder.
 *
 * @group performance
 */
class AnnuaireBench extends TestCase
{
    private const TERMES = [
        'stress', 'organisation', 'ville', 'communication',
        'StressNom1', 'StressPrenom42', 'Sénégal', 'Sarr',
        'nom1', 'lobbying',
    ];

    private const ITERATIONS = 100;

    private const SEUIL_SC002_MS = 1000;

    private const SEUIL_SC008_MS = 1500;

    protected function setUp(): void
    {
        parent::setUp();
        // S'assurer que le jeu de stress existe.
        (new ProfilsStressSeeder)->run(ProfilsStressSeeder::COUNT_DEFAULT);
    }

    public function test_sc002_recherche_libre_p95_sous_1s(): void
    {
        $service = app(ProfilSearchService::class);
        $durees = [];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $terme = self::TERMES[$i % count(self::TERMES)];
            $start = microtime(true);

            $service->recherche(['q' => $terme])
                ->paginate(24);

            $durees[] = (microtime(true) - $start) * 1000;
        }

        $p95 = $this->percentile($durees, 95);
        $this->displayReport('SC-002 recherche libre (P95)', $durees);

        $this->assertLessThan(self::SEUIL_SC002_MS, $p95, sprintf(
            'P95 recherche libre = %.2f ms, dépasse le seuil SC-002 (%d ms).',
            $p95,
            self::SEUIL_SC002_MS
        ));
    }

    public function test_sc008_recherche_plus_trois_filtres_plus_tri_p95_sous_1_5s(): void
    {
        $service = app(ProfilSearchService::class);
        $durees = [];

        $domaineSlug = optional(\App\Models\DomaineExpertise::query()->first())->slug ?? 'communication';
        $tagSlug = optional(\App\Models\Tag::query()->first())->slug ?? 'afrique';

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $start = microtime(true);

            $service->recherche([
                'q' => self::TERMES[$i % count(self::TERMES)],
                'type' => 'expert',
                'domaine' => [$domaineSlug],
                'tag' => [$tagSlug],
                'tri' => $i % 2 === 0 ? 'alpha' : 'recent',
            ])->paginate(24);

            $durees[] = (microtime(true) - $start) * 1000;
        }

        $p95 = $this->percentile($durees, 95);
        $this->displayReport('SC-008 recherche + 3 filtres + tri (P95)', $durees);

        $this->assertLessThan(self::SEUIL_SC008_MS, $p95, sprintf(
            'P95 combiné = %.2f ms, dépasse le seuil SC-008 (%d ms).',
            $p95,
            self::SEUIL_SC008_MS
        ));
    }

    /**
     * @param  array<int,float>  $values
     */
    private function percentile(array $values, float $p): float
    {
        if (empty($values)) {
            return 0.0;
        }
        sort($values);
        $idx = (int) ceil(($p / 100) * count($values)) - 1;

        return (float) $values[max(0, min(count($values) - 1, $idx))];
    }

    /**
     * @param  array<int,float>  $durees
     */
    private function displayReport(string $label, array $durees): void
    {
        $count = count($durees);
        $avg = $count ? array_sum($durees) / $count : 0.0;
        $p50 = $this->percentile($durees, 50);
        $p95 = $this->percentile($durees, 95);
        $max = $count ? max($durees) : 0.0;

        fwrite(STDOUT, sprintf(
            "\n[BENCH] %s : N=%d avg=%.2fms p50=%.2fms p95=%.2fms max=%.2fms\n",
            $label,
            $count,
            $avg,
            $p50,
            $p95,
            $max
        ));
    }
}
