<?php

namespace Database\Seeders;

use App\Models\Terrain;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TerrainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $terrains = ['Asfalto', 'Gravilla', 'Tierra', 'Barro'];
        foreach ($terrains as $terrain) {
            Terrain::create([
                'name' => $terrain
            ]);
        }
    }
}
