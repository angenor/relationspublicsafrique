<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Pays;
use App\Models\Post;
use App\Models\Profil;
use App\Models\WidgetText;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $faker = \Faker\Factory::create('fr_FR');

        \App\Models\User::factory()->create([
            'name' => 'labomle',
            'email' => 'labomle@gmail.com',
            'password' => Hash::make('admin123'),
            'type' => 'admin',
            'created_at' => now(),
            'email_verified_at' => now(),
        ]);

        \App\Models\User::factory()->create([
            'name' => 'damssan',
            'email' => 'gastinoking@gmail.com',
            'password' => Hash::make('admin123'),
            'type' => 'admin',
            'created_at' => now(),
            'email_verified_at' => now(),
        ]);

        \App\Models\User::factory()->create([
            'name' => 'ithieldofontien',
            'email' => 'ithieldofontien@gmail.com',
            'password' => Hash::make('admin123'),
            'type' => 'admin',
            'created_at' => now(),
            'email_verified_at' => now(),
        ]);

        // Section Projets (vitrine) — feature 003-projets-vitrine (idempotent).
        $this->call([
            ProjetSeeder::class,
        ]);

        /**
        Artisan::call('migrate:fresh');

      foreach (WidgetText::$types as $index => $item) {
            WidgetText::create([
                'key'=>$index,
                'name'=>$item['name'],
                'slug'=>$item['slug'],
                'image'=> $item['image'],
                'resume'=> 'resume',
                'content'=> 'content',
                'position'=> 1,
                'online'=>1,

            ]) ;
        }
        $users =  \App\Models\User::factory(10)->create();
        $posts =  \App\Models\Post::factory(10, ['type' => 'blog'])->create();
        $categories =  \App\Models\Category::factory(10)->create();

        // Ajouter les seeders
        $this->call([
            EventSeeder::class,
            NousSeeder::class,
        ]);






            \App\Models\Post::factory(3)->create([
                'name' =>  Post::$types['sliders'].rand(0,4),
                'type' => 'sliders',
                'position' =>rand(0,3),
            ]);





            \App\Models\Post::factory(1)->create([
                'name' => Post::$types['bienvenue_sur_relations_publics'],
                'type' => 'bienvenue_sur_relations_publics',
                'position' =>rand(0,3),
            ]);


            \App\Models\Post::factory(10)->create([
                'name' => Post::$types['evenements'],
                'type' => 'evenements',
                'position' =>rand(0,3),
                'online' =>1,
            ]);






        foreach (range(0,10) as $index => $item) {
            $nomPays = $faker->country ;
            Pays::create([
                'slug'=> \Str::slug($nomPays),
                'name'=>$nomPays,
                'online'=>rand(0,1),
            ]);
        }

       foreach ($users as $index => $user) {
           $nom =$faker->firstName;
           $prenom =$faker->userName ;
           $slug = \Str::slug("$nom-$prenom");
           Profil::create([
               'name'=>$faker->name,
               'slug'=>$slug,
               'nom'=>$faker->name,
               'prenom'=>$faker->userName,
               'title'=>$faker->name,
               'fonction'=>$faker->name,
               'domaine'=>$faker->name,
               'facebook'=>$faker->name,
               'twitter'=>$faker->name,
               'youtube'=>$faker->name,
               'linkding'=>$faker->name,
               'site'=>$faker->name,
               'contact'=>$faker->name,
               'adresse'=>$faker->name,
               'tel'=>$faker->name,
               'email'=>$faker->name,
               'bio'=>$faker->sentence(100),
               'online'=>1,
               'aprouve'=>$faker->name,
               'image'=>'images/user2.jpg',
               'pays_id'=>rand(1,10),
               'user_id'=>$user->id,
               'cover'=>  "img/img".rand(1,11).".jpeg"

           ]);
       }

         **/
    }
}
