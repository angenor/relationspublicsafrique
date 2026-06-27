<?php

declare(strict_types=1);

namespace App\Services\Events;

use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;

/**
 * Construit une requête de recherche/filtre/tri sur les événements publiés.
 * Calqué sur App\Services\Projets\ProjetSearchService.
 *
 * Visibilité : `published()` UNIQUEMENT (research R1) — jamais `online()`, qui
 * représente désormais le format (présentiel/en ligne).
 */
class EventSearchService
{
    /**
     * @param  array<string,mixed>  $criteres
     *                                         - q : terme libre (titre / description / lieu)
     *                                         - statut : statut temporel upcoming|ongoing|past (FR-015)
     *                                         - type : id de catégorie (type='event')
     *                                         - pays : pays_id
     *                                         - tri : proche (start_date asc) | recent (start_date desc) (FR-016)
     */
    public function recherche(array $criteres = []): Builder
    {
        $query = Event::query()->published();

        $terme = isset($criteres['q']) ? trim((string) $criteres['q']) : '';
        if ($terme !== '') {
            $this->applyTermeRecherche($query, $terme);
        }

        $statut = isset($criteres['statut']) ? (string) $criteres['statut'] : '';
        if ($statut !== '') {
            $this->applyStatutFilter($query, $statut);
        }

        if (! empty($criteres['type'])) {
            $query->byCategory((int) $criteres['type']);
        }

        if (! empty($criteres['pays'])) {
            $query->where('pays_id', (int) $criteres['pays']);
        }

        $this->applyTri($query, $criteres['tri'] ?? null, $statut);

        return $query;
    }

    private function applyTermeRecherche(Builder $query, string $terme): void
    {
        $like = '%'.str_replace(' ', '%', $terme).'%';

        $query->where(function (Builder $q) use ($like): void {
            $q->where('title', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhere('location', 'like', $like);
        });
    }

    private function applyStatutFilter(Builder $query, string $statut): void
    {
        match ($statut) {
            'upcoming' => $query->upcoming(),
            'ongoing' => $query->ongoing(),
            'past' => $query->past(),
            default => null,
        };
    }

    /**
     * Tri (FR-016). Par défaut (sans tri explicite) :
     *  - statut=past → du plus récent au plus ancien ;
     *  - statut=upcoming|ongoing → du plus proche au plus lointain ;
     *  - aucun filtre statut → événements actifs (en cours/à venir) d'abord, clos ensuite.
     */
    private function applyTri(Builder $query, ?string $tri, string $statut): void
    {
        if ($tri === 'proche') {
            $query->orderBy('start_date', 'asc');

            return;
        }
        if ($tri === 'recent') {
            $query->orderBy('start_date', 'desc');

            return;
        }

        if ($statut === 'past') {
            $query->orderBy('start_date', 'desc');

            return;
        }
        if ($statut === 'upcoming' || $statut === 'ongoing') {
            $query->orderBy('start_date', 'asc');

            return;
        }

        // Aucun filtre statut : actifs d'abord (end_date à venir), puis clos.
        $query->orderByRaw('CASE WHEN end_date >= ? THEN 0 ELSE 1 END', [now()->toDateTimeString()])
            ->orderBy('start_date', 'asc');
    }
}
