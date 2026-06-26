<?php

declare(strict_types=1);

namespace App\Helper;

/**
 * Parse une URL YouTube/Vimeo et produit l'URL d'iframe embarquable.
 * Aucune dépendance réseau : extraction purement par expression régulière.
 */
final class VideoEmbed
{
    /**
     * Extrait l'identifiant de la vidéo + la plateforme depuis une URL publique.
     *
     * @return array{provider: string, id: string}|null
     */
    public static function parse(?string $url): ?array
    {
        if ($url === null || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        // YouTube : watch?v=ID, youtu.be/ID, /embed/ID, /shorts/ID
        if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})~i', $url, $m)) {
            return ['provider' => 'youtube', 'id' => $m[1]];
        }

        // Vimeo : vimeo.com/ID, player.vimeo.com/video/ID
        if (preg_match('~vimeo\.com/(?:video/)?(\d{6,})~i', $url, $m)) {
            return ['provider' => 'vimeo', 'id' => $m[1]];
        }

        return null;
    }

    /**
     * URL embarquable (src de l'iframe) pour l'URL fournie, ou null si non reconnue.
     */
    public static function embedUrl(?string $url): ?string
    {
        $parsed = self::parse($url);
        if ($parsed === null) {
            return null;
        }

        return match ($parsed['provider']) {
            'youtube' => 'https://www.youtube-nocookie.com/embed/'.$parsed['id'],
            'vimeo' => 'https://player.vimeo.com/video/'.$parsed['id'],
            default => null,
        };
    }

    /**
     * Iframe responsive (ratio 16:9) prête à insérer, ou chaîne vide si l'URL est invalide.
     */
    public static function iframe(?string $url, string $title = 'Vidéo'): string
    {
        $src = self::embedUrl($url);
        if ($src === null) {
            return '';
        }

        $src = e($src);
        $title = e($title);

        return <<<HTML
        <div class="media-embed ratio ratio-16x9">
            <iframe src="{$src}" title="{$title}" loading="lazy" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe>
        </div>
        HTML;
    }

    /**
     * Hôtes autorisés pour la validation de embed_url (Filament / Form Request).
     *
     * @return array<int, string>
     */
    public static function allowedHosts(): array
    {
        return ['youtube.com', 'www.youtube.com', 'youtu.be', 'vimeo.com', 'www.vimeo.com', 'player.vimeo.com'];
    }
}
