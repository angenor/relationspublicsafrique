<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name() ;
        return [
            'name'=> $name,
            'slug'=>  \Str::slug($name),
            'type'=> fake()->colorName(),
            'image'=> fake()->imageUrl(),
            'place'=> fake()->randomDigit(),
            'online'=> fake()->randomDigit(),
            'position'=> fake()->randomDigit(),
            'parent_id'=> fake()->randomDigit(),
        ];
    }
}
