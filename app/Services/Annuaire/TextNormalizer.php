<?php

declare(strict_types=1);

namespace App\Services\Annuaire;

final class TextNormalizer
{
    /**
     * Normalise une chaîne pour la recherche : sans accents, en minuscules,
     * ponctuation remplacée par des espaces, espaces multiples compressés,
     * trim final. Retourne '' pour null/vide.
     */
    public static function normalize(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $value = mb_strtolower($value, 'UTF-8');

        // Ligatures fréquentes traitées avant transliteration.
        $value = strtr($value, [
            'œ' => 'oe', 'æ' => 'ae', 'ß' => 'ss',
        ]);

        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
            if ($converted !== false) {
                $value = $converted;
            }
        }

        // Supprime les marques restantes éventuelles produites par TRANSLIT.
        $value = preg_replace('/[\'`^"~]/u', ' ', $value) ?? $value;

        // Tout ce qui n'est pas alphanumérique devient espace.
        $value = preg_replace('/[^a-z0-9]+/i', ' ', $value) ?? $value;

        return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
    }
}
