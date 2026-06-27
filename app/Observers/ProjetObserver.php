<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Projet;
use App\Services\Annuaire\TextNormalizer;
use Illuminate\Support\Str;

class ProjetObserver
{
    /** Slug auto depuis le titre si absent (création programmatique : seeders/tests). */
    public function creating(Projet $projet): void
    {
        if (empty($projet->slug)) {
            $projet->slug = Str::slug((string) $projet->titre);
        }
    }

    /**
     * Maintient titre_normalise (recherche insensible aux accents) à chaque
     * enregistrement, et garantit une date de publication lorsqu'un projet est
     * marqué publié sans date (publication immédiate ; une date future = programmé).
     */
    public function saving(Projet $projet): void
    {
        $projet->titre_normalise = TextNormalizer::normalize($projet->titre);

        if ($projet->is_published && empty($projet->published_at)) {
            $projet->published_at = now();
        }
    }
}
