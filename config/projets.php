<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Section Projets (vitrine) — feature 003-projets-vitrine
    |--------------------------------------------------------------------------
    | Miroir de config/media.php. Pilote la pagination du listing /projets,
    | l'incrément « Charger plus » et le nombre de projets mis en avant.
    */

    // Nombre de projets par page (grille / listing).
    'per_page' => (int) env('PROJETS_PER_PAGE', 12),

    // Incrément du bouton « Charger plus ».
    'per_page_step' => (int) env('PROJETS_PER_PAGE_STEP', 12),

    // Nombre de projets « À la une » (featured) mis en avant en tête de listing.
    'a_la_une' => (int) env('PROJETS_A_LA_UNE', 3),
];
