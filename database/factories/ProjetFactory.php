<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Projet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Projet>
 */
class ProjetFactory extends Factory
{
    protected $model = Projet::class;

    public function definition(): array
    {
        $titre = rtrim($this->faker->unique()->sentence(4), '.');

        return [
            'titre' => $titre,
            // slug + titre_normalise gérés par ProjetObserver.
            'resume' => $this->faker->sentence(14),
            'contexte' => $this->faker->paragraph(),
            'objectifs' => $this->faker->paragraph(),
            'description' => $this->faker->paragraphs(2, true),
            'activites' => $this->faker->paragraph(),
            'statut' => $this->faker->randomElement(array_keys(Projet::$statuts)),
            'is_published' => true,
            'published_at' => now()->subDays($this->faker->numberBetween(1, 30)),
            'portee' => 'pays',
            'pays_id' => null,
            'featured' => false,
            'position' => 0,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false, 'published_at' => null]);
    }

    public function published(): static
    {
        return $this->state(fn () => ['is_published' => true, 'published_at' => now()->subDay()]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => ['is_published' => true, 'published_at' => now()->addDays(7)]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['featured' => true]);
    }

    public function statut(string $statut): static
    {
        return $this->state(fn () => ['statut' => $statut]);
    }

    public function withPays(int $paysId): static
    {
        return $this->state(fn () => ['portee' => 'pays', 'pays_id' => $paysId]);
    }

    public function regional(string $libelle = 'Afrique de l\'Ouest'): static
    {
        return $this->state(fn () => ['portee' => 'regional', 'pays_id' => null, 'zone_libelle' => $libelle]);
    }

    public function continental(string $libelle = 'Afrique'): static
    {
        return $this->state(fn () => ['portee' => 'continental', 'pays_id' => null, 'zone_libelle' => $libelle]);
    }
}
