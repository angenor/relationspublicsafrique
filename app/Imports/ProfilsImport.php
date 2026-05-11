<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\DomaineExpertise;
use App\Models\LienExterne;
use App\Models\Pays;
use App\Models\Profil;
use App\Models\Tag;
use App\Services\Annuaire\RapportImport;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProfilsImport implements ToCollection, WithHeadingRow, WithValidation, WithCustomCsvSettings, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    public RapportImport $rapport;

    private const TYPES_VALIDES = ['expert', 'etudiant', 'alumni', 'partenaire', 'autre'];
    private const ETATS_VALIDES = ['en_attente', 'publie', 'archive'];

    public function __construct(
        public readonly string $strategieDoublon = 'mettre_a_jour',
    ) {
        $this->rapport = new RapportImport();
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'enclosure' => '"',
            'use_bom' => true,
            'input_encoding' => 'UTF-8',
            'output_encoding' => 'UTF-8',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:120',
            'email' => 'nullable|email|max:200',
            'type_profil' => ['nullable', Rule::in(self::TYPES_VALIDES)],
            'etat_publication' => ['nullable', Rule::in(self::ETATS_VALIDES)],
        ];
    }

    public function collection(Collection $rows): void
    {
        $this->rapport->lues = $rows->count();

        foreach ($rows as $index => $row) {
            $ligne = $index + 2; // +1 pour l'index 0-based, +1 pour la ligne d'entête.
            try {
                $this->traiterLigne($row->toArray(), $ligne);
            } catch (\Throwable $e) {
                $this->rapport->ajouterErreur($ligne, null, $e->getMessage());
            }
        }
    }

    private function traiterLigne(array $data, int $ligne): void
    {
        if (empty($data['nom'])) {
            $this->rapport->ajouterErreur($ligne, 'nom', 'Le nom est obligatoire.');
            return;
        }

        $typeProfil = $data['type_profil'] ?? null;
        if ($typeProfil && ! in_array($typeProfil, self::TYPES_VALIDES, true)) {
            $this->rapport->ajouterErreur($ligne, 'type_profil', 'Type de profil invalide : '.$typeProfil);
            return;
        }

        $email = isset($data['email']) ? trim((string) $data['email']) : null;

        // Détection des doublons par email.
        $existant = $email ? Profil::where('email', $email)->first() : null;

        if ($existant) {
            if ($this->strategieDoublon === 'ignorer') {
                $this->rapport->ignorees++;
                return;
            }
            if ($this->strategieDoublon === 'mettre_a_jour') {
                $this->appliquer($existant, $data);
                $existant->save();
                $this->attacherRelations($existant, $data);
                $this->rapport->misesAJour++;
                return;
            }
        }

        $profil = new Profil();
        $this->appliquer($profil, $data);
        $profil->slug = $profil->slug ?: Str::slug(($data['prenom'] ?? '').'-'.$data['nom'].'-'.Str::random(6));
        $profil->save();
        $this->attacherRelations($profil, $data);
        $this->rapport->creees++;
    }

    private function appliquer(Profil $profil, array $data): void
    {
        $champs = [
            'nom', 'prenom', 'fonction', 'organisation', 'nationalite', 'ville',
            'type_profil', 'etat_publication', 'bio_courte', 'bio_longue',
            'email', 'tel', 'masquer_email', 'masquer_tel',
        ];

        foreach ($champs as $champ) {
            if (array_key_exists($champ, $data) && $data[$champ] !== null && $data[$champ] !== '') {
                $profil->{$champ} = $data[$champ];
            }
        }

        if (! empty($data['pays'])) {
            $pays = Pays::firstWhere('name', $data['pays']);
            if ($pays) {
                $profil->pays_id = $pays->id;
            }
        }
    }

    private function attacherRelations(Profil $profil, array $data): void
    {
        if (! empty($data['domaines_expertise'])) {
            $ids = collect(explode('|', (string) $data['domaines_expertise']))
                ->map(fn ($n) => trim($n))
                ->filter()
                ->map(fn ($nom) => DomaineExpertise::firstOrCreate(['slug' => Str::slug($nom)], ['nom' => $nom])->id)
                ->all();
            $profil->domainesExpertise()->sync($ids);
        }

        if (! empty($data['tags'])) {
            $ids = collect(explode('|', (string) $data['tags']))
                ->map(fn ($n) => trim($n))
                ->filter()
                ->map(fn ($nom) => Tag::firstOrCreate(['slug' => Str::slug($nom)], ['nom' => $nom])->id)
                ->all();
            $profil->tags()->sync($ids);
        }

        if (! empty($data['liens_externes'])) {
            $profil->liensExternes()->delete();
            foreach (explode('|', (string) $data['liens_externes']) as $entree) {
                if (! str_contains($entree, ':')) {
                    continue;
                }
                [$type, $url] = explode(':', $entree, 2);
                LienExterne::create([
                    'profil_id' => $profil->id,
                    'type' => trim($type),
                    'url' => trim($url),
                ]);
            }
        }
    }
}
