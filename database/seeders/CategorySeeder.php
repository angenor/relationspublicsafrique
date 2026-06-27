<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * Catégories « type d'événement » (research R4) — réutilisent la table
 * `categories` avec `type='event'` (cohérent avec 002 `type='media'` et 003
 * `type='projet'`). Idempotent par slug. La table legacy `categories` n'a pas
 * d'AUTO_INCREMENT en production : l'id est calculé manuellement (cf. ProjetSeeder).
 */
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'conference' => 'Conférence',
            'atelier' => 'Atelier',
            'webinaire' => 'Webinaire',
            'table-ronde' => 'Table ronde',
            'ceremonie' => 'Cérémonie',
        ];

        $position = 0;
        foreach ($categories as $slug => $name) {
            $cat = Category::where('slug', $slug)->where('type', 'event')->first();
            if ($cat === null) {
                $id = (int) (Category::max('id') ?? 0) + 1;
                $cat = new Category;
                $cat->id = $id;
                $cat->slug = $slug;
            }
            $cat->name = $name;
            $cat->type = 'event';
            $cat->online = 1;
            $cat->position = $position++;
            $cat->save();
        }
    }
}
