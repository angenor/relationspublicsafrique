<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Formation;
use App\Models\Instructors;
use App\Models\Category;
use App\Models\User;

class FormationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des formateurs si nécessaire
        $instructor = Instructors::first();
        if (!$instructor) {
            $user = User::first();
            if ($user) {
                $instructor = Instructors::create([
                    'name' => 'Formateur Principal',
                    'competance' => 'Développement Web, Laravel, Vue.js',
                    'user_id' => $user->id,
                ]);
            }
        }

        // Créer une catégorie si nécessaire
        $category = Category::first();
        if (!$category) {
            $category = Category::create([
                'name' => 'Développement Web',
                'slug' => 'developpement-web',
                'online' => 1,
            ]);
        }

        // Créer des formations
        $formations = [
            [
                'name' => 'Laravel pour les débutants',
                'slug' => 'laravel-pour-les-debutants',
                'description' => 'Apprenez les bases de Laravel, le framework PHP le plus populaire.',
                'content' => '<p>Cette formation vous permettra de maîtriser les concepts fondamentaux de Laravel :</p><ul><li>Installation et configuration</li><li>Routage et contrôleurs</li><li>Modèles et migrations</li><li>Vues et Blade</li><li>Authentification</li></ul>',
                'image' => 'formations/laravel-beginner.jpg',
                'video' => 'formations/laravel-intro.mp4',
                'duration' => 480, // 8 heures
                'price' => 50000, // 50,000 FCFA
                'level' => 1,
                'level_name' => 'débutant',
                'language' => 'fr',
                'is_published' => true,
                'is_featured' => true,
                'sort_order' => 1,
                'tags' => ['laravel', 'php', 'débutant', 'web'],
                'certificate' => 'Certificat Laravel Débutant',
                'max_students' => 30,
                'instructor_id' => $instructor->id,
                'category_id' => $category->id,
                'published_at' => now(),
            ],
            [
                'name' => 'Vue.js et Composition API',
                'slug' => 'vue-js-composition-api',
                'description' => 'Maîtrisez Vue.js 3 avec la Composition API pour créer des interfaces modernes.',
                'content' => '<p>Formation complète sur Vue.js 3 :</p><ul><li>Composition API</li><li>Reactivity</li><li>Composants</li><li>Routing</li><li>State Management</li></ul>',
                'image' => 'formations/vue-js.jpg',
                'video' => 'formations/vue-intro.mp4',
                'duration' => 600, // 10 heures
                'price' => 75000,
                'level' => 2,
                'level_name' => 'intermédiaire',
                'language' => 'fr',
                'is_published' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'tags' => ['vue', 'javascript', 'frontend', 'spa'],
                'certificate' => 'Certificat Vue.js',
                'max_students' => 25,
                'instructor_id' => $instructor->id,
                'category_id' => $category->id,
                'published_at' => now(),
            ],
            [
                'name' => 'API REST avec Laravel',
                'slug' => 'api-rest-laravel',
                'description' => 'Créez des APIs REST robustes et sécurisées avec Laravel.',
                'content' => '<p>Développement d\'APIs professionnelles :</p><ul><li>Architecture REST</li><li>Authentification JWT</li><li>Validation des données</li><li>Tests d\'API</li><li>Documentation</li></ul>',
                'image' => 'formations/api-rest.jpg',
                'video' => 'formations/api-intro.mp4',
                'duration' => 720, // 12 heures
                'price' => 100000,
                'level' => 3,
                'level_name' => 'avancé',
                'language' => 'fr',
                'is_published' => true,
                'is_featured' => false,
                'sort_order' => 3,
                'tags' => ['laravel', 'api', 'rest', 'backend'],
                'certificate' => 'Certificat API REST',
                'max_students' => 20,
                'instructor_id' => $instructor->id,
                'category_id' => $category->id,
                'published_at' => now(),
            ],
            [
                'name' => 'Docker pour les développeurs',
                'slug' => 'docker-developpeurs',
                'description' => 'Apprenez à containeriser vos applications avec Docker.',
                'content' => '<p>Containerisation et orchestration :</p><ul><li>Concepts Docker</li><li>Images et conteneurs</li><li>Docker Compose</li><li>Déploiement</li><li>Bonnes pratiques</li></ul>',
                'image' => 'formations/docker.jpg',
                'video' => 'formations/docker-intro.mp4',
                'duration' => 360, // 6 heures
                'price' => 60000,
                'level' => 2,
                'level_name' => 'intermédiaire',
                'language' => 'fr',
                'is_published' => true,
                'is_featured' => false,
                'sort_order' => 4,
                'tags' => ['docker', 'devops', 'containerisation'],
                'certificate' => 'Certificat Docker',
                'max_students' => 15,
                'instructor_id' => $instructor->id,
                'category_id' => $category->id,
                'published_at' => now(),
            ],
            [
                'name' => 'Tests automatisés avec PHPUnit',
                'slug' => 'tests-automatises-phpunit',
                'description' => 'Maîtrisez les tests unitaires et d\'intégration avec PHPUnit.',
                'content' => '<p>Stratégies de test complètes :</p><ul><li>Tests unitaires</li><li>Tests d\'intégration</li><li>Tests fonctionnels</li><li>Mocking</li><li>CI/CD</li></ul>',
                'image' => 'formations/phpunit.jpg',
                'video' => 'formations/phpunit-intro.mp4',
                'duration' => 420, // 7 heures
                'price' => 80000,
                'level' => 3,
                'level_name' => 'avancé',
                'language' => 'fr',
                'is_published' => true,
                'is_featured' => false,
                'sort_order' => 5,
                'tags' => ['phpunit', 'tests', 'qualité', 'ci-cd'],
                'certificate' => 'Certificat Tests PHPUnit',
                'max_students' => 18,
                'instructor_id' => $instructor->id,
                'category_id' => $category->id,
                'published_at' => now(),
            ],
        ];

        foreach ($formations as $formationData) {
            Formation::create($formationData);
        }

        $this->command->info('Formations créées avec succès !');
    }
}
