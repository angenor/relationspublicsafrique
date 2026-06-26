<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Section Média — feature 002-media-newsroom
    |--------------------------------------------------------------------------
    | Miroir de config/annuaire.php. Pilote la pagination de l'explorateur,
    | les rubriques d'accueil, le throttling public et la pondération de
    | popularité (tendances).
    */

    // Nombre d'items par page (grille / explorateur).
    'per_page' => (int) env('MEDIA_PER_PAGE', 12),

    // Incrément du bouton « Charger plus ».
    'per_page_step' => (int) env('MEDIA_PER_PAGE_STEP', 12),

    // Nombre de contenus « À la une » en tête de la page d'accueil.
    'a_la_une' => (int) env('MEDIA_A_LA_UNE', 3),

    // Limite de requêtes/min sur les formulaires publics (commentaire, newsletter).
    'rate_limit_public' => (int) env('MEDIA_RATE_LIMIT_PUBLIC', 30),

    // Demi-vie (en jours) de la décroissance de popularité (recompute-popularity).
    'popularity_half_life_days' => (int) env('MEDIA_POP_HALFLIFE', 14),
];
