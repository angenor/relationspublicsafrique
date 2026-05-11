<?php

namespace App\Services\Annuaire;

use App\Models\Profil;
use Illuminate\Database\Eloquent\Builder;

class ProfilSearchService
{
    /**
     * Construit une requête Eloquent paginable selon les critères fournis.
     *
     * Critères supportés :
     *   - q : terme de recherche libre (normalisé)
     *   - pays : slug ou id du pays
     *   - type : enum type_profil
     *   - domaine : string|string[] de slugs
     *   - tag : string|string[] de slugs
     *   - tri : alpha|recent|pertinence (défaut : alpha si q vide, pertinence sinon)
     *
     * @param  array<string,mixed>  $criteres
     */
    public function recherche(array $criteres = []): Builder
    {
        $query = Profil::query()->publies();

        $terme = isset($criteres['q']) ? trim((string) $criteres['q']) : '';

        if ($terme !== '') {
            $this->applyTermeRecherche($query, $terme);
        }

        if (! empty($criteres['pays'])) {
            $this->applyPaysFilter($query, $criteres['pays']);
        }

        if (! empty($criteres['type'])) {
            $this->applyTypeFilter($query, (string) $criteres['type']);
        }

        if (! empty($criteres['domaine'])) {
            $this->applyDomaineFilter($query, (array) $criteres['domaine']);
        }

        if (! empty($criteres['tag'])) {
            $this->applyTagFilter($query, (array) $criteres['tag']);
        }

        $this->applyTri($query, $criteres['tri'] ?? null, $terme);

        return $query;
    }

    private function applyTermeRecherche(Builder $query, string $terme): void
    {
        $normalise = TextNormalizer::normalize($terme);
        if ($normalise === '') {
            return;
        }
        $like = '%'.str_replace(' ', '%', $normalise).'%';

        $query->where(function (Builder $q) use ($like) {
            $q->where('nom_normalise', 'like', $like)
                ->orWhere('prenom_normalise', 'like', $like)
                ->orWhere('organisation_normalisee', 'like', $like)
                ->orWhere('ville_normalisee', 'like', $like)
                ->orWhereHas('pays', function (Builder $sub) use ($like) {
                    $sub->where('name_normalise', 'like', $like);
                })
                ->orWhereHas('domainesExpertise', function (Builder $sub) use ($like) {
                    $sub->where('slug', 'like', $like)
                        ->orWhere('libelle', 'like', $like);
                })
                ->orWhereHas('tags', function (Builder $sub) use ($like) {
                    $sub->where('slug', 'like', $like)
                        ->orWhere('libelle', 'like', $like);
                });
        });
    }

    /**
     * @param  string|int  $pays  slug ou id
     */
    private function applyPaysFilter(Builder $query, $pays): void
    {
        $query->whereHas('pays', function (Builder $sub) use ($pays) {
            if (is_numeric($pays)) {
                $sub->where('id', (int) $pays);
            } else {
                $sub->where('slug', (string) $pays);
            }
        });
    }

    private function applyTypeFilter(Builder $query, string $type): void
    {
        $autorises = ['expert', 'etudiant', 'alumni', 'partenaire', 'autre'];
        if (in_array($type, $autorises, true)) {
            $query->where('type_profil', $type);
        }
    }

    /**
     * @param  array<int,string>  $domaines  slugs
     */
    private function applyDomaineFilter(Builder $query, array $domaines): void
    {
        $domaines = array_values(array_filter(array_map('strval', $domaines)));
        if ($domaines === []) {
            return;
        }
        $query->whereHas('domainesExpertise', function (Builder $sub) use ($domaines) {
            $sub->whereIn('slug', $domaines);
        });
    }

    /**
     * @param  array<int,string>  $tags  slugs
     */
    private function applyTagFilter(Builder $query, array $tags): void
    {
        $tags = array_values(array_filter(array_map('strval', $tags)));
        if ($tags === []) {
            return;
        }
        $query->whereHas('tags', function (Builder $sub) use ($tags) {
            $sub->whereIn('slug', $tags);
        });
    }

    private function applyTri(Builder $query, ?string $tri, string $terme): void
    {
        $tri = $tri ?: ($terme !== '' ? 'pertinence' : 'alpha');

        switch ($tri) {
            case 'recent':
                $query->orderByDesc('created_at');
                break;

            case 'pertinence':
                // Heuristique simple : profils publiés récemment d'abord quand un terme matche.
                // Implémentation FULLTEXT à venir (research.md §recherche).
                $query->orderByDesc('published_at')->orderBy('nom');
                break;

            case 'alpha':
            default:
                $query->orderBy('nom')->orderBy('prenom');
                break;
        }
    }
}
