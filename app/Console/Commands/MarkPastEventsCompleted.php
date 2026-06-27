<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

/**
 * Hygiène éditoriale optionnelle (research R8) : bascule les événements PUBLIÉS
 * dont la date de fin est passée vers le statut éditorial « completed ».
 *
 * NB : l'affichage du statut temporel reste DÉRIVÉ des dates (R3) — cette commande
 * n'est qu'un confort back-office. `Event::published()` inclut `completed`, donc
 * les événements clos restent visibles publiquement (replay/photos — SC-006).
 */
class MarkPastEventsCompleted extends Command
{
    protected $signature = 'events:mark-completed';

    protected $description = 'Bascule éditoriale published→completed des événements clos (confort back-office).';

    public function handle(): int
    {
        $count = Event::query()
            ->where('status', 'published')
            ->where('end_date', '<', now())
            ->update(['status' => 'completed']);

        $this->info("{$count} événement(s) clos basculé(s) en « Terminé ».");

        return self::SUCCESS;
    }
}
