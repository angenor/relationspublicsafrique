<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Section Événements (vitrine) — feature 004-evenements
    |--------------------------------------------------------------------------
    | Miroir de config/projets.php. Pilote la pagination du listing /evenements,
    | l'incrément « Charger plus » et le nombre d'événements mis en avant.
    */

    // Nombre d'événements par page (grille / listing).
    'per_page' => (int) env('EVENTS_PER_PAGE', 12),

    // Incrément du bouton « Charger plus ».
    'per_page_step' => (int) env('EVENTS_PER_PAGE_STEP', 12),

    // Nombre d'événements « À la une » (featured) mis en avant en tête de listing.
    'a_la_une' => (int) env('EVENTS_A_LA_UNE', 3),
];
