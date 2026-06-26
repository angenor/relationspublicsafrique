<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Media;
use App\Models\NewsletterSubscriber;
use App\Notifications\NewMediaPublishedNotification;
use App\Services\Annuaire\TextNormalizer;
use Illuminate\Support\Str;

class MediaObserver
{
    /** Slug auto depuis le titre si absent (création programmatique : seeders/tests). */
    public function creating(Media $media): void
    {
        if (empty($media->slug)) {
            $media->slug = Str::slug((string) $media->titre);
        }
    }

    /**
     * Maintient titre_normalise (recherche insensible aux accents) et
     * reading_time (mots / 200) à chaque enregistrement.
     */
    public function saving(Media $media): void
    {
        $media->titre_normalise = TextNormalizer::normalize($media->titre);

        if (! empty($media->content)) {
            $mots = str_word_count(strip_tags((string) $media->content));
            $media->reading_time = max(1, (int) round($mots / 200));
        }
    }

    /**
     * À la transition d'un contenu vers `published` (UPDATE uniquement, donc pas
     * sur une création directe ni sur un simple increment de vues), alerte les
     * abonnés newsletter confirmés. wasChanged('status') garantit l'idempotence.
     */
    public function updated(Media $media): void
    {
        if (! $media->wasChanged('status') || $media->status !== 'published') {
            return;
        }

        NewsletterSubscriber::query()
            ->confirmed()
            ->cursor()
            ->each(fn (NewsletterSubscriber $subscriber) => $subscriber->notify(
                new NewMediaPublishedNotification($media)
            ));
    }
}
