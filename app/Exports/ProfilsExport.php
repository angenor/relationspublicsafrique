<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Profil;
use App\Services\Annuaire\ProfilSearchService;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class ProfilsExport implements FromQuery, WithHeadings, WithMapping, WithCustomCsvSettings, WithStrictNullComparison
{
    /**
     * @param array<string,mixed> $filtres
     */
    public function __construct(private readonly array $filtres = [])
    {
    }

    public function query()
    {
        $query = empty($this->filtres)
            ? Profil::query()
            : app(ProfilSearchService::class)->recherche($this->filtres);

        return $query->with(['pays', 'domainesExpertise', 'tags', 'liensExternes']);
    }

    public function headings(): array
    {
        return [
            'id', 'nom', 'prenom', 'fonction', 'organisation', 'email', 'tel',
            'pays', 'nationalite', 'ville',
            'type_profil', 'etat_publication',
            'masquer_email', 'masquer_tel',
            'bio_courte', 'bio_longue',
            'domaines_expertise', 'tags',
            'liens_externes',
            'created_at', 'updated_at',
        ];
    }

    public function map($profil): array
    {
        return [
            $profil->id,
            $profil->nom,
            $profil->prenom,
            $profil->fonction,
            $profil->organisation,
            $profil->email,
            $profil->tel,
            optional($profil->pays)->name,
            $profil->nationalite,
            $profil->ville,
            $profil->type_profil,
            $profil->etat_publication,
            $profil->masquer_email ? 1 : 0,
            $profil->masquer_tel ? 1 : 0,
            $profil->bio_courte,
            strip_tags((string) $profil->bio_longue),
            $profil->domainesExpertise->pluck('nom')->join('|'),
            $profil->tags->pluck('nom')->join('|'),
            $profil->liensExternes->map(fn ($l) => $l->type.':'.$l->url)->join('|'),
            optional($profil->created_at)?->toDateTimeString(),
            optional($profil->updated_at)?->toDateTimeString(),
        ];
    }

    /**
     * Encodage UTF-8 avec BOM (compatibilité Excel Windows fr) + `;` comme délimiteur.
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'enclosure' => '"',
            'line_ending' => "\r\n",
            'use_bom' => true,
            'include_separator_line' => false,
            'output_encoding' => 'UTF-8',
        ];
    }
}
