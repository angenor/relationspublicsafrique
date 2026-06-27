<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\Pays;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder idempotent de la section Événements (004-evenements) : au moins un
 * événement par statut temporel (à venir / en cours / clos), avec intervenants,
 * médias post-événement, et les deux modes d'inscription (interne & externe).
 * Un événement présentiel publié démontre la correction R1 (visibilité = status).
 *
 * Pré-requis : CategorySeeder (catégories type='event'). À défaut, une catégorie
 * de secours est créée. La table legacy `categories`/`pays` n'ayant pas
 * d'AUTO_INCREMENT en production, les ids sont calculés manuellement au besoin.
 */
class EventSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        // Catégorie « type d'événement » (R4).
        $category = Category::where('type', 'event')->orderBy('position')->first();
        if ($category === null) {
            $id = (int) (Category::max('id') ?? 0) + 1;
            $category = new Category;
            $category->id = $id;
            $category->slug = 'conference';
            $category->name = 'Conférence';
            $category->type = 'event';
            $category->online = 1;
            $category->position = 0;
            $category->save();
        }

        // Pays de référence (filtre FR-015).
        $pays = Pays::query()->first();
        if ($pays === null) {
            $paysId = (int) (Pays::max('id') ?? 0) + 1;
            $pays = new Pays;
            $pays->id = $paysId;
            $pays->slug = 'togo';
            $pays->name = 'Togo';
            $pays->online = 1;
            $pays->save();
        }

        $events = [
            // 1. À VENIR — présentiel publié, inscription interne, avec intervenants.
            [
                'slug' => 'forum-rp-afrique-2026',
                'title' => 'Forum des Relations Publiques en Afrique 2026',
                'resume' => 'Le rendez-vous panafricain des professionnels de la communication et des relations publiques.',
                'description' => '<p>Deux jours de conférences, d\'ateliers et de rencontres autour des enjeux de la communication institutionnelle sur le continent.</p>',
                'objectifs' => 'Fédérer les acteurs du secteur, partager les bonnes pratiques et révéler les talents émergents.',
                'programme' => '<ul><li>Jour 1 : conférences plénières</li><li>Jour 2 : ateliers thématiques</li></ul>',
                'public_cible' => 'Communicants, dircoms, journalistes, étudiants en communication.',
                'location' => 'Lomé, Togo',
                'online' => false,
                'start_date' => now()->addDays(30)->setTime(9, 0),
                'end_date' => now()->addDays(31)->setTime(18, 0),
                'registration_deadline' => now()->addDays(25),
                'max_participants' => 300,
                'registration_mode' => 'internal',
                'is_featured' => true,
                'speakers' => [
                    ['nom' => 'Awa Traoré', 'role' => 'Directrice de la communication', 'organisation' => 'RPA'],
                    ['nom' => 'Jean-Marc Kodjo', 'role' => 'Consultant en relations publiques', 'organisation' => 'Cabinet Hermès'],
                ],
                'medias' => [],
            ],
            // 2. À VENIR — webinaire en ligne, inscription EXTERNE.
            [
                'slug' => 'webinaire-strategie-digitale',
                'title' => 'Webinaire : stratégie digitale des organisations africaines',
                'resume' => 'Une session en ligne pour maîtriser les leviers du digital au service de votre organisation.',
                'description' => '<p>Un webinaire pratique animé par des experts du secteur.</p>',
                'objectifs' => 'Donner des clés concrètes pour bâtir une présence digitale efficace.',
                'public_cible' => 'Responsables communication et marketing.',
                'location' => null,
                'online' => true,
                'start_date' => now()->addDays(14)->setTime(15, 0),
                'end_date' => now()->addDays(14)->setTime(17, 0),
                'registration_deadline' => now()->addDays(13),
                'max_participants' => null,
                'registration_mode' => 'external',
                'registration_url' => 'https://exemple.org/webinaire/inscription',
                'speakers' => [
                    ['nom' => 'Fatou Ndiaye', 'role' => 'Experte digitale'],
                ],
                'medias' => [],
            ],
            // 3. EN COURS — atelier présentiel publié.
            [
                'slug' => 'atelier-media-training',
                'title' => 'Atelier de media-training',
                'resume' => 'Un atelier intensif pour préparer les porte-paroles à la prise de parole médiatique.',
                'description' => '<p>Mises en situation, simulations d\'interviews et débriefings personnalisés.</p>',
                'objectifs' => 'Gagner en aisance face aux médias.',
                'public_cible' => 'Dirigeants et porte-paroles.',
                'location' => 'Abidjan, Côte d\'Ivoire',
                'online' => false,
                'start_date' => now()->subHours(2),
                'end_date' => now()->addHours(4),
                'registration_deadline' => now()->subDays(1),
                'max_participants' => 20,
                'registration_mode' => 'internal',
                'speakers' => [],
                'medias' => [],
            ],
            // 4. CLOS — avec médias post-événement (replay + photo + document) + compte rendu.
            [
                'slug' => 'conference-communication-publique-2025',
                'title' => 'Conférence sur la communication publique 2025',
                'resume' => 'Retour sur une édition riche en échanges autour de la communication des institutions publiques.',
                'description' => '<p>Une journée de réflexion sur le rôle de la communication publique en Afrique.</p>',
                'objectifs' => 'Dresser un état des lieux et tracer des perspectives.',
                'public_cible' => 'Agents publics et communicants institutionnels.',
                'compte_rendu' => '<p>La conférence a réuni plus de 200 participants. Les échanges ont porté sur la transparence et la confiance.</p>',
                'location' => 'Dakar, Sénégal',
                'online' => false,
                'start_date' => now()->subDays(40)->setTime(9, 0),
                'end_date' => now()->subDays(40)->setTime(17, 0),
                'registration_deadline' => now()->subDays(45),
                'registration_mode' => 'internal',
                'speakers' => [
                    ['nom' => 'Moussa Camara', 'role' => 'Directeur de cabinet', 'organisation' => 'Ministère de la Communication'],
                ],
                'medias' => [
                    ['type' => 'replay', 'url_embed' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'legende' => 'Replay de la séance plénière'],
                    ['type' => 'image', 'chemin' => 'events/medias/demo.jpg', 'legende' => 'Photo de groupe'],
                    ['type' => 'document', 'chemin' => 'events/medias/compte-rendu.pdf', 'legende' => 'Compte rendu (PDF)'],
                ],
            ],
            // 5. BROUILLON — ne doit jamais apparaître côté public.
            [
                'slug' => 'evenement-interne-en-preparation',
                'title' => 'Événement interne en préparation',
                'resume' => 'Brouillon non publié — invisible côté public.',
                'description' => '<p>Contenu en cours de rédaction.</p>',
                'location' => 'À définir',
                'online' => false,
                'start_date' => now()->addDays(60)->setTime(9, 0),
                'end_date' => now()->addDays(60)->setTime(12, 0),
                'registration_mode' => 'internal',
                'status' => 'draft',
                'speakers' => [],
                'medias' => [],
            ],
        ];

        foreach ($events as $data) {
            $speakers = $data['speakers'] ?? [];
            $medias = $data['medias'] ?? [];
            unset($data['speakers'], $data['medias']);

            $data['status'] = $data['status'] ?? 'published';
            $data['user_id'] = $user->id;
            $data['category_id'] = $category->id;
            $data['pays_id'] = $pays->id;

            $slug = $data['slug'];
            $event = Event::updateOrCreate(['slug' => $slug], $data);

            // Enfants reconstruits à chaque passage (idempotence).
            $event->speakers()->delete();
            foreach ($speakers as $pos => $speaker) {
                $event->speakers()->create($speaker + ['position' => $pos]);
            }

            $event->medias()->delete();
            foreach ($medias as $pos => $media) {
                $event->medias()->create($media + ['position' => $pos]);
            }
        }
    }
}
