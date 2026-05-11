<?php

declare(strict_types=1);

namespace Tests\Performance;

use App\Models\Pays;
use App\Services\Annuaire\ProfilImportService;
use Tests\TestCase;

/**
 * Benchmark de l'import CSV de profils (SC-004).
 *
 * Cible : import d'un fichier CSV de 500 profils factices,
 * assertion sur la durée P95 < 120 secondes.
 *
 * Non inclus dans la suite par défaut (répertoire `tests/Performance` non
 * référencé dans `phpunit.xml`). Lancement explicite :
 *
 *     vendor/bin/phpunit tests/Performance/AnnuaireImportBench.php
 *
 * @group performance
 */
class AnnuaireImportBench extends TestCase
{
    private const NB_PROFILS = 500;

    private const SEUIL_SC004_MS = 120_000; // 120 secondes

    private string $csvPath;

    protected function setUp(): void
    {
        parent::setUp();

        if (Pays::count() === 0) {
            Pays::factory()->count(5)->create();
        }

        $this->csvPath = $this->genererCsvStress(self::NB_PROFILS);
    }

    protected function tearDown(): void
    {
        if (isset($this->csvPath) && file_exists($this->csvPath)) {
            @unlink($this->csvPath);
        }
        parent::tearDown();
    }

    public function test_sc004_import_500_profils_sous_120s(): void
    {
        /** @var ProfilImportService $service */
        $service = app(ProfilImportService::class);

        $start = microtime(true);
        $rapport = $service->import($this->csvPath, 'mettre_a_jour');
        $duree = (microtime(true) - $start) * 1000;

        fwrite(STDOUT, sprintf(
            "\n[BENCH] SC-004 import %d profils : %.2f ms (lues=%d créées=%d maj=%d ignorées=%d erreurs=%d)\n",
            self::NB_PROFILS,
            $duree,
            $rapport->lues,
            $rapport->creees,
            $rapport->mises_a_jour,
            $rapport->ignorees,
            count($rapport->erreurs)
        ));

        $this->assertLessThan(self::SEUIL_SC004_MS, $duree, sprintf(
            'Import de %d profils en %.2f ms — dépasse le seuil SC-004 (%d ms).',
            self::NB_PROFILS,
            $duree,
            self::SEUIL_SC004_MS
        ));
    }

    private function genererCsvStress(int $count): string
    {
        $paysName = Pays::query()->value('name') ?? "Côte d'Ivoire";
        $path = storage_path('app/annuaire-bench-import-'.uniqid().'.csv');

        $fh = fopen($path, 'wb');
        fwrite($fh, "\xEF\xBB\xBF"); // BOM UTF-8

        // En-têtes attendus par ProfilsImport (cf. contracts/csv-schema.md).
        $headers = [
            'nom', 'prenom', 'email', 'tel', 'fonction', 'organisation',
            'ville', 'pays', 'nationalite', 'type_profil', 'bio_courte',
            'masquer_email', 'masquer_tel',
        ];
        fputcsv($fh, $headers, ';');

        for ($i = 1; $i <= $count; $i++) {
            fputcsv($fh, [
                'BenchNom'.$i,
                'BenchPrenom'.$i,
                'bench'.$i.'@example.test',
                '+221700'.str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'Consultant'.($i % 20),
                'Organisation'.($i % 50),
                'Ville'.($i % 30),
                $paysName,
                'Sénégalaise',
                'expert',
                'Bio courte du profil bench '.$i,
                ($i % 7 === 0) ? '1' : '0',
                ($i % 5 === 0) ? '1' : '0',
            ], ';');
        }

        fclose($fh);

        return $path;
    }
}
