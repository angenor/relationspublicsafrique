<?php

namespace Database\Factories;

use App\Models\LomeTourRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LomeTourRegistration>
 */
class LomeTourRegistrationFactory extends Factory
{
    protected $model = LomeTourRegistration::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prenom' => $this->faker->firstName(),
            'nom' => $this->faker->lastName(),
            'statut' => $this->faker->randomElement(['etudiant', 'professionnel']),
            'fonction' => $this->faker->jobTitle(),
            'telephone_whatsapp' => '+228' . $this->faker->numerify('########'),
            'email' => $this->faker->optional(0.8)->safeEmail(),
            'notes' => $this->faker->optional(0.3)->sentence(),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'rejected']),
            'registered_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the registration is pending.
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the registration is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    /**
     * Indicate that the registration is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    /**
     * Indicate that the registration is for a student.
     */
    public function student(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut' => 'etudiant',
            'fonction' => 'Étudiant',
        ]);
    }

    /**
     * Indicate that the registration is for a professional.
     */
    public function professional(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut' => 'professionnel',
            'fonction' => $this->faker->jobTitle(),
        ]);
    }
}
