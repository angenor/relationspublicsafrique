<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer un utilisateur et une catégorie existants
        $user = User::first();
        $category = Category::first();

        if (!$user) {
            $user = User::factory()->create();
        }

        if (!$category) {
            $category = Category::factory()->create();
        }

        // Créer des événements de test
        Event::factory()->count(20)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        // Créer quelques événements mis en avant
        Event::factory()->featured()->count(3)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        // Créer des événements à venir
        Event::factory()->upcoming()->count(5)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
    }
}
