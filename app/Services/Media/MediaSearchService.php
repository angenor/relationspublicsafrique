<?php

declare(strict_types=1);

namespace App\Services\Media;

use App\Models\Media;
use App\Services\Annuaire\TextNormalizer;
use Illuminate\Database\Eloquent\Builder;

/**
 * Construit une requête de recherche/filtre/tri sur les contenus média publiés.
 * Calqué sur App\Services\Annuaire\ProfilSearchService.
 */
class MediaSearchService
{
    /**
     * @param  array<string,mixed>  $criteres
     *                                         - q : terme libre (normalisé)
     *                                         - type : article|interview|podcast|video|reportage
     *                                         - categorie : slug de catégorie/thématique
     *                                         - pays : slug ou id du pays
     *                                         - auteur : id de l'auteur (principal ou contributeur)
     *                                         - tri : recent|populaire|recommande
     */
    public function recherche(array $criteres = []): Builder
    {
        $query = Media::query()->published();

        $terme = isset($criteres['q']) ? trim((string) $criteres['q']) : '';
        if ($terme !== '') {
            $this->applyTermeRecherche($query, $terme);
        }

        if (! empty($criteres['type'])) {
            $this->applyTypeFilter($query, (string) $criteres['type']);
        }

        if (! empty($criteres['categorie'])) {
            $query->whereHas('categories', fn (Builder $sub) => $sub->where('slug', (string) $criteres['categorie']));
        }

        if (! empty($criteres['pays'])) {
            $this->applyPaysFilter($query, $criteres['pays']);
        }

        if (! empty($criteres['auteur'])) {
            $this->applyAuteurFilter($query, $criteres['auteur']);
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
                ->orWhereHas('tags', fn (Builder $s) => $s->where('slug', 'like', $like)->orWhere('libelle', 'like', $likeBrut))
                ->orWhereHas('categories', fn (Builder $s) => $s->where('name', 'like', $likeBrut))
                ->orWhereHas('auteurs', fn (Builder $s) => $s->where('name', 'like', $likeBrut))
                ->orWhereHas('auteurPrincipal', fn (Builder $s) => $s->where('name', 'like', $likeBrut));
        });
    }

    private function applyTypeFilter(Builder $query, string $type): void
    {
        if (array_key_exists($type, Media::$types)) {
            $query->where('type', $type);
        }
    }

    /**
     * @param  string|int  $pays  slug ou id
     */
    private function applyPaysFilter(Builder $query, $pays): void
    {
        $query->whereHas('pays', function (Builder $sub) use ($pays): void {
            if (is_numeric($pays)) {
                $sub->where('id', (int) $pays);
            } else {
                $sub->where('slug', (string) $pays);
            }
        });
    }

    /**
     * @param  string|int  $auteur  id utilisateur (auteur principal ou contributeur)
     */
    private function applyAuteurFilter(Builder $query, $auteur): void
    {
        $id = (int) $auteur;
        $query->where(function (Builder $q) use ($id): void {
            $q->where('user_id', $id)
                ->orWhereHas('auteurs', fn (Builder $s) => $s->where('users.id', $id));
        });
    }

    private function applyTri(Builder $query, ?string $tri): void
    {
        switch ($tri) {
            case 'populaire':
                $query->popular();
                break;
            case 'recommande':
                $query->recommended();
                break;
            case 'recent':
            default:
                $query->recent();
                break;
        }
    }
}
