<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Media;
use App\Models\MediaSerie;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder idempotent de la section Média : thématiques (categories type='media'),
 * quelques séries, et au moins un contenu de chaque type avec statuts variés
 * (dont featured, pinned, scheduled, archived) pour démontrer les rubriques d'accueil.
 */
class MediaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Thématiques média (réutilisent la table categories, type='media').
        $thematiques = [
            'communication-politique' => 'Communication politique',
            'communication-corporate' => 'Communication corporate',
            'communication-digitale' => 'Communication digitale',
            'relations-presse' => 'Relations presse',
        ];
        $categorieIds = [];
        $position = 0;
        foreach ($thematiques as $slug => $name) {
            // La table legacy `categories` n'a pas d'AUTO_INCREMENT : on calcule l'id
            // manuellement à la création. Après save(), Eloquent écrase l'id en mémoire
            // avec lastInsertId()=0 → on conserve l'id connu dans une variable dédiée.
            $cat = Category::where('slug', $slug)->first();
            if ($cat === null) {
                $id = (int) (Category::max('id') ?? 0) + 1;
                $cat = new Category;
                $cat->id = $id;
                $cat->slug = $slug;
            } else {
                $id = (int) $cat->id;
            }
            $cat->name = $name;
            $cat->type = 'media';
            $cat->online = 1;
            $cat->position = $position++;
            $cat->save();

            $categorieIds[$slug] = $id;
        }

        // 2. Auteur par défaut.
        $auteur = User::query()->first() ?? User::factory()->create([
            'name' => 'Rédaction RPA',
            'email' => 'redaction@rpa.local',
        ]);

        // 3. Séries.
        $seriePodcast = MediaSerie::updateOrCreate(
            ['slug' => 'paroles-de-communicants'],
            ['titre' => 'Paroles de communicants', 'type' => 'podcast', 'online' => true, 'position' => 1],
        );

        // 4. Contenus (idempotents par slug).
        $items = [
            [
                'slug' => 'bienvenue-dans-la-newsroom-rpa',
                'titre' => 'Bienvenue dans la newsroom de RP Afrique',
                'type' => 'article',
                'status' => 'published',
                'featured' => true,
                'categorie' => 'communication-corporate',
                'content' => '<p>'.str_repeat('La communication africaine se réinvente. ', 80).'</p>',
            ],
            [
                'slug' => 'interview-strateges-2026',
                'titre' => 'Interview : les stratèges de la communication africaine en 2026',
                'type' => 'interview',
                'media_kind' => 'youtube',
                'embed_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'status' => 'published',
                'categorie' => 'communication-politique',
                'content' => '<p>'.str_repeat('Regards croisés sur la profession. ', 60).'</p>',
            ],
            [
                'slug' => 'podcast-episode-1',
                'titre' => 'Épisode 1 — Construire une marque employeur',
                'type' => 'podcast',
                'media_kind' => 'audio',
                'audio_file' => 'media/audio/demo.mp3',
                'audio_downloadable' => true,
                'duration' => 1820,
                'status' => 'published',
                'serie_id' => $seriePodcast->id,
                'saison' => 1,
                'episode' => 1,
                'categorie' => 'communication-digitale',
                'content' => '<p>Notes de l\'épisode.</p>',
            ],
            [
                'slug' => 'video-decryptage-rp',
                'titre' => 'Vidéo : décryptage des relations presse modernes',
                'type' => 'video',
                'media_kind' => 'youtube',
                'embed_url' => 'https://youtu.be/dQw4w9WgXcQ',
                'duration' => 540,
                'status' => 'published',
                'categorie' => 'relations-presse',
                'content' => '<p>Décryptage en vidéo.</p>',
            ],
            [
                'slug' => 'reportage-sommet-communication',
                'titre' => 'Reportage : au cœur du sommet de la communication',
                'type' => 'reportage',
                'status' => 'published',
                'is_pinned' => true,
                'pinned_position' => 1,
                'categorie' => 'communication-corporate',
                'content' => '<p>'.str_repeat('Sur le terrain du sommet panafricain. ', 70).'</p>',
            ],
            [
                'slug' => 'article-programme-a-venir',
                'titre' => 'À paraître : le futur de l\'influence en Afrique',
                'type' => 'article',
                'status' => 'scheduled',
                'published_at' => now()->addDays(3),
                'categorie' => 'communication-digitale',
                'content' => '<p>Article programmé.</p>',
            ],
            [
                'slug' => 'archive-retrospective-2025',
                'titre' => 'Rétrospective 2025 (archivée)',
                'type' => 'article',
                'status' => 'archived',
                'categorie' => 'communication-corporate',
                'content' => '<p>Contenu archivé.</p>',
            ],
        ];

        foreach ($items as $data) {
            $categorieSlug = $data['categorie'] ?? null;
            unset($data['categorie']);

            $data['user_id'] = $auteur->id;
            if (! array_key_exists('published_at', $data) && $data['status'] === 'published') {
                $data['published_at'] = now()->subDays(random_int(1, 20));
            }
            if (! array_key_exists('view', $data)) {
                $data['view'] = random_int(20, 800);
                $data['popularity_score'] = $data['view'];
            }

            $slug = $data['slug'];
            $media = Media::updateOrCreate(['slug' => $slug], $data);

            if ($categorieSlug && isset($categorieIds[$categorieSlug])) {
                $media->categories()->syncWithoutDetaching([$categorieIds[$categorieSlug] => ['position' => 0]]);
            }
        }
    }
}
