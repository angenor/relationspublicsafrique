<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class NousSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mission
        $missionPost = Post::where('type', 'mission')->first();
        if (!$missionPost) {
            Post::create([
                'name' => 'Notre Mission',
                'slug' => 'notre-mission',
                'content' => '<h2>Notre Mission</h2>
                <p>Relations Publiques Afrique s\'engage à promouvoir l\'excellence dans le domaine des relations publiques sur le continent africain. Notre mission est de :</p>

                <h3>🎯 Objectifs principaux</h3>
                <ul>
                    <li><strong>Développer les compétences</strong> en relations publiques à travers l\'Afrique</li>
                    <li><strong>Créer un réseau</strong> de professionnels qualifiés et expérimentés</li>
                    <li><strong>Promouvoir les meilleures pratiques</strong> en communication stratégique</li>
                    <li><strong>Renforcer la visibilité</strong> des organisations africaines sur la scène internationale</li>
                </ul>

                <h3>🌟 Nos engagements</h3>
                <p>Nous nous engageons à fournir des services de qualité supérieure, à respecter les valeurs éthiques et à contribuer au développement socio-économique de l\'Afrique à travers une communication efficace et responsable.</p>

                <h3>🤝 Notre approche</h3>
                <p>Nous croyons en une approche collaborative et inclusive qui valorise la diversité culturelle africaine tout en adoptant les standards internationaux de l\'industrie des relations publiques.</p>',
                'resume' => 'Promouvoir l\'excellence en relations publiques sur le continent africain à travers la formation, le réseautage et les meilleures pratiques.',
                'type' => 'mission',
                'online' => true,
                'user_id' => User::first()->id,
                'note' => 0,
                'position' => 1,
                'view' => 0,
                'parent_id' => 0,
                'category_id' => 0,
                'image' => 'nous/mission-featured.jpg',
                'externe_link' => '',
                'dateevente' => now(),
                'video' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Mettre à jour l'image si le post existe déjà
            $missionPost->update(['image' => 'nous/mission-featured.jpg']);
        }

        // Vision
        $visionPost = Post::where('type', 'vision')->first();
        if (!$visionPost) {
            Post::create([
                'name' => 'Notre Vision',
                'slug' => 'notre-vision',
                'content' => '<h2>Notre Vision</h2>
                <p>Relations Publiques Afrique aspire à devenir la référence incontournable en matière de relations publiques sur le continent africain d\'ici 2030.</p>

                <h3>🚀 Vision 2030</h3>
                <p>Nous envisageons un avenir où :</p>
                <ul>
                    <li><strong>L\'Afrique</strong> est reconnue comme un leader mondial en communication stratégique</li>
                    <li><strong>Les professionnels africains</strong> sont les plus recherchés sur le marché international</li>
                    <li><strong>Les organisations africaines</strong> maîtrisent parfaitement leur image et leur communication</li>
                    <li><strong>Le continent</strong> influence positivement la perception mondiale de l\'Afrique</li>
                </ul>

                <h3>🌍 Impact souhaité</h3>
                <p>Notre vision s\'articule autour de trois piliers fondamentaux :</p>

                <h4>1. Excellence Professionnelle</h4>
                <p>Développer une nouvelle génération de professionnels des relations publiques formés aux standards internationaux.</p>

                <h4>2. Innovation Technologique</h4>
                <p>Intégrer les dernières technologies de communication pour positionner l\'Afrique à l\'avant-garde de l\'innovation.</p>

                <h4>3. Leadership Mondial</h4>
                <p>Faire de l\'Afrique un acteur majeur dans la définition des tendances et des standards des relations publiques mondiales.</p>

                <h3>💡 Notre ambition</h3>
                <p>Transformer la perception de l\'Afrique à travers une communication stratégique, authentique et impactante qui reflète la richesse et la diversité de notre continent.</p>',
                'resume' => 'Devenir la référence incontournable en relations publiques sur le continent africain d\'ici 2030.',
                'type' => 'vision',
                'online' => true,
                'user_id' => User::first()->id,
                'note' => 0,
                'position' => 1,
                'view' => 0,
                'parent_id' => 0,
                'category_id' => 0,
                'image' => 'nous/vision-featured.jpg',
                'externe_link' => '',
                'dateevente' => now(),
                'video' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Mettre à jour l'image si le post existe déjà
            $visionPost->update(['image' => 'nous/vision-featured.jpg']);
        }

        // Historique
        $historiquePost = Post::where('type', 'historique')->first();
        if (!$historiquePost) {
            Post::create([
                'name' => 'Notre Historique',
                'slug' => 'notre-historique',
                'content' => '<h2>Notre Historique</h2>
                <p>Relations Publiques Afrique a été fondée avec la conviction que l\'Afrique mérite une voix forte et professionnelle sur la scène internationale.</p>

                <h3>📅 Chronologie</h3>

                <h4>2020 - Les débuts</h4>
                <p>Fondation de Relations Publiques Afrique par un groupe de professionnels passionnés, convaincus du potentiel immense du continent en matière de communication stratégique.</p>

                <h4>2021 - Premiers pas</h4>
                <p>Lancement de nos premiers programmes de formation et organisation de notre premier événement majeur qui a rassemblé plus de 200 professionnels de 15 pays africains.</p>

                <h4>2022 - Expansion</h4>
                <p>Ouverture de nos bureaux régionaux et signature de partenariats stratégiques avec des institutions internationales de renom.</p>

                <h4>2023 - Reconnaissance</h4>
                <p>Reconnaissance officielle par l\'Association Internationale des Relations Publiques (IPRA) et obtention de plusieurs certifications de qualité.</p>

                <h4>2024 - Innovation</h4>
                <p>Lancement de notre plateforme digitale et introduction de programmes de formation en ligne accessibles à tous les professionnels africains.</p>

                <h3>🏆 Moments marquants</h3>
                <ul>
                    <li><strong>2021 :</strong> Premier sommet africain des relations publiques</li>
                    <li><strong>2022 :</strong> Création du réseau des professionnels africains</li>
                    <li><strong>2023 :</strong> Lancement du programme de certification</li>
                    <li><strong>2024 :</strong> Mise en place de la plateforme digitale</li>
                </ul>

                <h3>👥 Fondateurs</h3>
                <p>Notre organisation a été créée par une équipe de visionnaires qui partageaient une passion commune : voir l\'Afrique exceller dans le domaine des relations publiques et de la communication stratégique.</p>

                <h3>🌱 Évolution continue</h3>
                <p>Depuis notre création, nous n\'avons cessé d\'évoluer et de nous adapter aux besoins changeants du marché africain, tout en maintenant notre engagement envers l\'excellence et l\'innovation.</p>',
                'resume' => 'Fondée en 2020, Relations Publiques Afrique a évolué pour devenir un acteur majeur de la communication stratégique sur le continent.',
                'type' => 'historique',
                'online' => true,
                'user_id' => User::first()->id,
                'note' => 0,
                'position' => 1,
                'view' => 0,
                'parent_id' => 0,
                'category_id' => 0,
                'image' => 'nous/historique-featured.jpg',
                'externe_link' => '',
                'dateevente' => now(),
                'video' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Mettre à jour l'image si le post existe déjà
            $historiquePost->update(['image' => 'nous/historique-featured.jpg']);
        }

        $this->command->info('✅ Données "Nous" créées avec succès !');
        $this->command->info('📄 Mission, Vision et Historique ont été ajoutés à la base de données.');
    }
}
