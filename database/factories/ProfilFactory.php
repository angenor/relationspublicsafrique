<?php

namespace Database\Factories;

use App\Models\Pays;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profil>
 */
class ProfilFactory extends Factory
{
    public function definition(): array
    {
        $nom = $this->faker->lastName();
        $prenom = $this->faker->firstName();

        return [
            'name' => $prenom.' '.$nom,
            'slug' => Str::slug($prenom.'-'.$nom.'-'.Str::random(6)),
            'nom' => $nom,
            'prenom' => $prenom,
            'title' => $this->faker->optional()->sentence(3),
            'fonction' => $this->faker->jobTitle(),
            'domaine' => $this->faker->randomElement(['communication', 'lobbying', 'presse']),
            'bio' => $this->faker->paragraph(),
            'bio_courte' => $this->faker->sentence(15),
            'bio_longue' => $this->faker->paragraphs(3, true),
            'ville' => $this->faker->city(),
            'organisation' => $this->faker->company(),
            'nationalite' => $this->faker->country(),
            'type_profil' => $this->faker->randomElement(['expert', 'etudiant', 'alumni', 'partenaire', 'autre']),
            'etat_publication' => 'publie',
            'masquer_email' => false,
            'masquer_tel' => false,
            'legacy_sans_consentement' => false,
            'published_at' => now(),
            'online' => 1,
            'aprouve' => 1,
            'email' => $this->faker->unique()->safeEmail(),
            'tel' => $this->faker->phoneNumber(),
            'pays_id' => Pays::factory(),
            'user_id' => User::factory(),
        ];
    }

    public function publie(): static
    {
        return $this->state(fn () => [
            'etat_publication' => 'publie',
            'published_at' => now(),
        ]);
    }

    public function enAttente(): static
    {
        return $this->state(fn () => [
            'etat_publication' => 'en_attente',
            'published_at' => null,
        ]);
    }

    public function archive(): static
    {
        return $this->state(fn () => [
            'etat_publication' => 'archive',
        ]);
    }

    public function masque(): static
    {
        return $this->state(fn () => [
            'masquer_email' => true,
            'masquer_tel' => true,
        ]);
    }
}
