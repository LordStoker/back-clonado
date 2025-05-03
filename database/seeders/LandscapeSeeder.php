<?php

namespace Database\Seeders;

use App\Models\Landscape;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LandscapeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $landscapes = [ 'Montaña','Costa', 'Campo', 'Ciudad', 'Bosque', 'Desierto'];

        foreach ($landscapes as $landscape) {
            Landscape::create([
                'name' => $landscape
            ]);
        }
    }
}
