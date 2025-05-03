<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Route;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\TerrainSeeder;
use Database\Seeders\LandscapeSeeder;
use Database\Seeders\DifficultySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        //Seeders
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CountrySeeder::class,
            LandscapeSeeder::class,
            DifficultySeeder::class,
            TerrainSeeder::class,
        ]);

        //Factories
        User::factory(50)->create();
        Route::factory(50)->create();
        Comment::factory(200)->create();
    }
}
