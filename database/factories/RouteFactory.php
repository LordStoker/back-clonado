<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Country;
use App\Models\Terrain;
use App\Models\Landscape;
use App\Models\Difficulty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Route>
 */
class RouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            
            'name' => fake()->word(),
            'route_map' => fake()->text(200),
            'description' => fake()->text(200),
            'distance' => fake()->numberBetween(1, 100),
            'duration' => fake()->numberBetween(1, 24),
            'totalScore' => fake()->numberBetween(1, 5),
            'countScore' => fake()->numberBetween(1, 100),
            'country_id' => Country::inRandomOrder()->first()->id,
            'terrain_id' => Terrain::inRandomOrder()->first()->id,
            'difficulty_id' => Difficulty::inRandomOrder()->first()->id,
            'landscape_id' => Landscape::inRandomOrder()->first()->id,            
            'user_id' => User::inRandomOrder()->first()->id,
            
        ];
    }
}
