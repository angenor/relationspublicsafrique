<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
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
            'name' =>$name,
             'slug' => fake()->sentence,
             'resume' => fake()->sentence(200),
             'content' => fake()->sentence(200),
             'position' => 1,
             'online' => 1,
             'type' =>'Post',
             'note' =>25,
             'user_id' =>1,
             'category_id' =>0,
             'parent_id' =>0,
             'image' =>  ("img/img".rand(1,11).".jpeg")


        ];
    }
}
