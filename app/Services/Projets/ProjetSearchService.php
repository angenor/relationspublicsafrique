<?php

declare(strict_types=1);

namespace App\Services\Projets;

use App\Models\Projet;
use App\Services\Annuaire\TextNormalizer;
use Illuminate\Database\Eloquent\Builder;

/**
 * Construit une requête de recherche/filtre/tri sur les projets publiés.
 * Calqué sur App\Services\Media\MediaSearchService.
 */
class ProjetSearchService
{
    /**
     * @param  array<string,mixed>  $criteres
     *                                         - q : terme libre (normalisé sur titre_normalise)
     *                                         - thematique : id de catégorie (type='projet')
     *                                         - zone : token « pays:{id} » ou « portee:{regional|continental} »
     *                                         - statut : actif|realise|en_developpement
     *                                         - tri : recent|alpha
     */
    public function recherche(array $criteres = []): Builder
    {
        $query = Projet::query()->published();

        $terme = isset($criteres['q']) ? trim((string) $criteres['q']) : '';
        if ($terme !== '') {
            $this->applyTermeRecherche($query, $terme);
        }

        if (! empty($criteres['thematique'])) {
            $query->byThematique((int) $criteres['thematique']);
        }

        if (! empty($criteres['zone'])) {
            $query->byZone((string) $criteres['zone']);
        }

        if (! empty($criteres['statut'])) {
            $this->applyStatutFilter($query, (string) $criteres['statut']);
        }

        $this->applyTri($query, $criteres['tri'] ?? null);

        return $query;
    }

    private function applyTermeRecherche(Builder $query, string $terme): void
    {
        $normalise = TextNormalizer::normalize($terme);
        if ($normalise === '') {
            return;
        }
        $like = '%'.str_replace(' ', '%', $normalise).'%';
        $likeBrut = '%'.str_replace(' ', '%', $terme).'%';

        $query->where(function (Builder $q) use ($like, $likeBrut): void {
            $q->where('titre_normalise', 'like', $like)
                ->orWhereHas('pays', fn (Builder $s) => $s->where('name_normalise', 'like', $like))
                ->orWhereHas('categories', fn (Builder $s) => $s->where('name', 'like', $likeBrut));
        });
    }

    private function applyStatutFilter(Builder $query, string $statut): void
    {
        if (array_key_exists($statut, Projet::$statuts)) {
            $query->byStatut($statut);
        }
    }

    private function applyTri(Builder $query, ?string $tri): void
    {
        switch ($tri) {
            case 'alpha':
                $query->orderBy('titre');
                break;
            case 'recent':
            default:
                $query->ordered();
                break;
        }
    }
}
