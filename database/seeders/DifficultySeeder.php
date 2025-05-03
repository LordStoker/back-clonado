<?php

namespace Database\Seeders;

use App\Models\Difficulty;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DifficultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $difficulties = ['Fácil', 'Moderada', 'Difícil', 'Extrema'];
        foreach ($difficulties as $difficulty) {
            Difficulty::create([
                'name' => $difficulty
            ]);
        }
    }
}
