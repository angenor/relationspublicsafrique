<?php

declare(strict_types=1);

namespace Tests\Feature\Annuaire;

use App\Models\Pays;
use App\Models\Profil;
use App\Services\Annuaire\ProfilImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportExportTest extends TestCase
{
    use RefreshDatabase;

    private function csvFile(string $contenu): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'imp_').'.csv';
        file_put_contents($path, "\xEF\xBB\xBF".$contenu);

        return new UploadedFile($path, 'profils.csv', 'text/csv', null, true);
    }

    public function test_import_csv_cree_de_nouveaux_profils_quand_email_inconnu(): void
    {
        $csv = "nom;prenom;email;type_profil\nDiop;Marie;marie@example.com;expert\n";

        $rapport = app(ProfilImportService::class)->import($this->csvFile($csv), 'mettre_a_jour');

        $this->assertEquals(1, $rapport->lues);
        $this->assertEquals(1, $rapport->creees);
        $this->assertDatabaseHas('profils', ['email' => 'marie@example.com', 'nom' => 'Diop']);
    }

    public function test_import_csv_met_a_jour_si_email_connu(): void
    {
        Profil::factory()->create(['email' => 'doublon@example.com', 'nom' => 'Ancien']);

        $csv = "nom;prenom;email;type_profil\nNouveau;Eric;doublon@example.com;expert\n";

        $rapport = app(ProfilImportService::class)->import($this->csvFile($csv), 'mettre_a_jour');

        $this->assertEquals(1, $rapport->misesAJour);
        $this->assertDatabaseHas('profils', ['email' => 'doublon@example.com', 'nom' => 'Nouveau']);
    }

    public function test_import_csv_ignore_si_strategie_ignorer(): void
    {
        Profil::factory()->create(['email' => 'ignored@example.com', 'nom' => 'Ancien']);

        $csv = "nom;prenom;email;type_profil\nNouveau;Eric;ignored@example.com;expert\n";

        $rapport = app(ProfilImportService::class)->import($this->csvFile($csv), 'ignorer');

        $this->assertEquals(1, $rapport->ignorees);
        $this->assertDatabaseHas('profils', ['email' => 'ignored@example.com', 'nom' => 'Ancien']);
    }

    public function test_ligne_invalide_est_listee_dans_les_erreurs(): void
    {
        $csv = "nom;prenom;email;type_profil\n;Anonyme;sansnom@example.com;hors_enum\n";

        $rapport = app(ProfilImportService::class)->import($this->csvFile($csv), 'mettre_a_jour');

        $this->assertGreaterThanOrEqual(1, count($rapport->erreurs));
        $this->assertEquals(0, $rapport->creees);
    }
}
