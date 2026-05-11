<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Annuaire — feature 001-annuaire-evolution
    |--------------------------------------------------------------------------
    */

    'per_page' => (int) env('ANNUAIRE_PER_PAGE', 24),

    'consentement_expiry_days' => (int) env('ANNUAIRE_CONSENTEMENT_EXPIRY_DAYS', 90),

    'rate_limit_public' => (int) env('ANNUAIRE_RATE_LIMIT_PUBLIC', 60),
];
