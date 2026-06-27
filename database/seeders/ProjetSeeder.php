<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Partenaire;
use App\Models\Pays;
use App\Models\Projet;
use Illuminate\Database\Seeder;

/**
 * Seeder idempotent de la section Projets : thématiques (categories type='projet'),
 * partenaires master réutilisables, et au moins un projet par statut métier
 * (actif, réalisé, en développement) plus un brouillon, avec galerie, chiffres
 * clés, partenaires et témoignages — de quoi démontrer US1/US2 sans le back-office.
 */
class ProjetSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Thématiques projet (réutilisent la table categories, type='projet').
        //    La table legacy `categories` n'a pas d'AUTO_INCREMENT : on calcule l'id
        //    manuellement (cf. MediaSeeder) et on le conserve dans une variable.
        $thematiques = [
            'communication-institutionnelle' => 'Communication institutionnelle',
            'plaidoyer' => 'Plaidoyer',
            'formation-medias' => 'Formation aux médias',
            'mobilisation-citoyenne' => 'Mobilisation citoyenne',
        ];
        $categorieIds = [];
        $position = 0;
        foreach ($thematiques as $slug => $name) {
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
            $cat->type = 'projet';
            $cat->online = 1;
            $cat->position = $position++;
            $cat->save();

            $categorieIds[$slug] = $id;
        }

        // 2. Un pays de référence (portée nationale). Réutilise l'existant ou crée
        //    une entrée avec id manuel (pays.id est `int` legacy sans AUTO_INCREMENT).
        $pays = Pays::query()->first();
        if ($pays === null) {
            $paysId = (int) (Pays::max('id') ?? 0) + 1;
            $pays = new Pays;
            $pays->id = $paysId;
            $pays->slug = 'cote-d-ivoire';
            $pays->name = 'Côte d\'Ivoire';
            $pays->online = 1;
            $pays->save();
        } else {
            $paysId = (int) $pays->id;
        }

        // 3. Partenaires master réutilisables (idempotents par slug).
        $partenaires = [
            Partenaire::updateOrCreate(['slug' => 'union-africaine'], ['nom' => 'Union africaine', 'url' => 'https://au.int']),
            Partenaire::updateOrCreate(['slug' => 'oif'], ['nom' => 'Organisation internationale de la Francophonie', 'url' => 'https://www.francophonie.org']),
        ];

        // 4. Projets (idempotents par slug) — un par statut + un brouillon.
        $projets = [
            [
                'slug' => 'campagne-communication-citoyenne-2026',
                'titre' => 'Campagne de communication citoyenne 2026',
                'resume' => 'Une campagne panafricaine pour renforcer l\'engagement citoyen autour des grands rendez-vous démocratiques.',
                'statut' => 'actif',
                'featured' => true,
                'portee' => 'pays',
                'pays_id' => $paysId,
                'contexte' => 'La défiance envers le débat public progresse. Ce projet répond au besoin de réconcilier les citoyens avec l\'information vérifiée.',
                'objectifs' => 'Toucher 1 million de citoyens, former 500 relais locaux, produire 50 contenus pédagogiques.',
                'description' => '<p>Le projet déploie une stratégie multicanale combinant radio, réseaux sociaux et ateliers de terrain.</p>',
                'activites' => '<ul><li>Ateliers de fact-checking</li><li>Production de capsules vidéo</li><li>Caravane citoyenne</li></ul>',
                'thematiques' => ['communication-institutionnelle', 'mobilisation-citoyenne'],
                'partenaires' => [0, 1],
                'resultats' => [
                    ['libelle' => 'Citoyens touchés', 'valeur' => '1 200 000', 'unite' => 'personnes'],
                    ['libelle' => 'Relais formés', 'valeur' => '540', 'unite' => 'personnes'],
                ],
                'medias' => [
                    ['type' => 'image', 'chemin' => 'projets/galerie/demo.jpg', 'legende' => 'Atelier citoyen à Abidjan'],
                    ['type' => 'video', 'url_embed' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'legende' => 'Teaser de la campagne'],
                ],
                'temoignages' => [
                    ['auteur' => 'Aïssata Diallo', 'fonction' => 'Coordinatrice terrain', 'organisation' => 'RPA', 'contenu' => 'La caravane a transformé la manière dont nos communautés s\'informent.'],
                ],
            ],
            [
                'slug' => 'tournee-presse-panafricaine',
                'titre' => 'Tournée presse panafricaine',
                'resume' => 'Un programme régional de renforcement des rédactions et de couverture coordonnée des enjeux continentaux.',
                'statut' => 'realise',
                'portee' => 'regional',
                'zone_libelle' => 'Afrique de l\'Ouest',
                'contexte' => 'Les rédactions régionales manquent de moyens pour couvrir les sujets transfrontaliers.',
                'objectifs' => 'Mettre en réseau 12 rédactions et produire une série d\'enquêtes communes.',
                'description' => '<p>Six mois de mentorat éditorial et de production conjointe.</p>',
                'activites' => '<p>Bootcamps éditoriaux, bourses d\'enquête, plateforme de mutualisation.</p>',
                'thematiques' => ['formation-medias', 'plaidoyer'],
                'partenaires' => [1],
                'resultats' => [
                    ['libelle' => 'Rédactions en réseau', 'valeur' => '12', 'unite' => 'pays'],
                    ['libelle' => 'Enquêtes publiées', 'valeur' => '28'],
                ],
                'medias' => [
                    ['type' => 'image', 'chemin' => 'projets/galerie/demo.jpg', 'legende' => 'Bootcamp éditorial'],
                ],
                'temoignages' => [
                    ['auteur' => 'Kwame Mensah', 'fonction' => 'Rédacteur en chef', 'organisation' => 'Accra Times', 'contenu' => 'Une expérience qui a durablement élevé le niveau de nos enquêtes.'],
                ],
            ],
            [
                'slug' => 'plateforme-jeunes-talents-communication',
                'titre' => 'Plateforme des jeunes talents de la communication',
                'resume' => 'Un dispositif continental en cours de conception pour révéler la prochaine génération de communicants africains.',
                'statut' => 'en_developpement',
                'portee' => 'continental',
                'zone_libelle' => 'Afrique',
                'contexte' => 'Les jeunes talents peinent à accéder à des opportunités structurantes.',
                'objectifs' => 'Construire un parcours de mentorat et un annuaire de talents.',
                'description' => '<p>Projet en phase de cadrage et de recherche de partenaires.</p>',
                'activites' => null,
                'thematiques' => ['formation-medias'],
                'partenaires' => [],
                'resultats' => [],
                'medias' => [],
                'temoignages' => [], // démontre le masquage de la section témoignages (FR-012)
            ],
            [
                'slug' => 'projet-interne-en-preparation',
                'titre' => 'Projet interne en préparation',
                'resume' => 'Brouillon non publié — ne doit jamais apparaître côté public.',
                'statut' => 'en_developpement',
                'is_published' => false,
                'portee' => 'pays',
                'pays_id' => $paysId,
                'thematiques' => ['communication-institutionnelle'],
                'partenaires' => [],
                'resultats' => [],
                'medias' => [],
                'temoignages' => [],
            ],
        ];

        foreach ($projets as $index => $data) {
            $thematiques = $data['thematiques'] ?? [];
            $partIdx = $data['partenaires'] ?? [];
            $resultats = $data['resultats'] ?? [];
            $medias = $data['medias'] ?? [];
            $temoignages = $data['temoignages'] ?? [];
            unset($data['thematiques'], $data['partenaires'], $data['resultats'], $data['medias'], $data['temoignages']);

            $isPublished = $data['is_published'] ?? true;
            $data['is_published'] = $isPublished;
            if ($isPublished && ! array_key_exists('published_at', $data)) {
                // Date déterministe (et non aléatoire) → le re-seed converge vers le
                // même état et le tri par published_at reste stable (idempotence).
                $data['published_at'] = now()->subDays($index + 1);
            }

            $slug = $data['slug'];
            $projet = Projet::updateOrCreate(['slug' => $slug], $data);

            // Thématiques (pivot).
            $syncCats = [];
            foreach ($thematiques as $i => $themeSlug) {
                if (isset($categorieIds[$themeSlug])) {
                    $syncCats[$categorieIds[$themeSlug]] = ['position' => $i];
                }
            }
            $projet->categories()->sync($syncCats);

            // Partenaires (pivot).
            $syncParts = [];
            foreach ($partIdx as $pos => $idx) {
                if (isset($partenaires[$idx])) {
                    $syncParts[$partenaires[$idx]->id] = ['position' => $pos];
                }
            }
            $projet->partenaires()->sync($syncParts);

            // Enfants hasMany : reconstruits à chaque passage pour converger (idempotence).
            $projet->resultats()->delete();
            foreach ($resultats as $pos => $r) {
                $projet->resultats()->create($r + ['position' => $pos]);
            }

            $projet->medias()->delete();
            foreach ($medias as $pos => $m) {
                $projet->medias()->create($m + ['position' => $pos]);
            }

            $projet->temoignages()->delete();
            foreach ($temoignages as $pos => $t) {
                $projet->temoignages()->create($t + ['position' => $pos]);
            }
        }
    }
}
