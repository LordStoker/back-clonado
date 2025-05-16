<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Country;
use App\Models\Terrain;
use App\Models\Landscape;
use App\Models\Difficulty;
use App\Services\GeocodingService;
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
            
            'name' => fake()->sentence(3),
            'route_map' => function () {
                // Generar coordenadas aleatorias para crear un polígono cerrado
                // que simule una ruta en una región geográfica específica
                
                // Definimos algunas regiones para generar rutas
                $regions = [
                    // España (centro)
                    ['lat' => 40.416775, 'lng' => -3.703790, 'radius' => 2],
                    // Barcelona
                    ['lat' => 41.390205, 'lng' => 2.154007, 'radius' => 1],
                    // Valencia
                    ['lat' => 39.469908, 'lng' => -0.376288, 'radius' => 1],
                    // Sevilla
                    ['lat' => 37.389092, 'lng' => -5.984459, 'radius' => 1],
                    // Francia (París)
                    ['lat' => 48.856614, 'lng' => 2.352222, 'radius' => 2],
                    // Italia (Roma)
                    ['lat' => 41.902782, 'lng' => 12.496366, 'radius' => 1.5],
                ];

                // Seleccionamos una región aleatoria
                $region = $regions[array_rand($regions)];
                $centerLat = $region['lat'];
                $centerLng = $region['lng'];
                $radius = $region['radius']; // Radio en grados aproximadamente
                
                // Generamos entre 5 y 12 puntos para la ruta (asegurando suficientes puntos para visualización)
                $numPoints = rand(5, 12);
                $coordinates = [];
                
                // Generamos los puntos en un patrón que forme una ruta realista
                for ($i = 0; $i < $numPoints; $i++) {
                    // Calculamos un offset aleatorio dentro del radio
                    $latOffset = (mt_rand(-100, 100) / 100) * $radius;
                    $lngOffset = (mt_rand(-100, 100) / 100) * $radius;
                    
                    // Ajustamos la dirección para crear un patrón de ruta más interesante
                    // Usamos una función sinusoidal para crear un patrón ondulado más realista
                    $factor = $i / ($numPoints - 1); // 0 a 1
                    $dirLat = sin($factor * M_PI * 1.5) * $radius * 0.6;
                    $dirLng = cos($factor * M_PI * 1.5) * $radius * 0.6;
                    
                    $lat = $centerLat + $dirLat + ($latOffset * 0.2);
                    $lng = $centerLng + $dirLng + ($lngOffset * 0.2);
                    
                    // Añadimos el punto a las coordenadas
                    $coordinates[] = [$lat, $lng];
                }
                
                // Validamos que las coordenadas sean números válidos
                foreach ($coordinates as &$coord) {
                    $coord[0] = round($coord[0], 6); // Limitar a 6 decimales (precisión suficiente)
                    $coord[1] = round($coord[1], 6);
                }
                
                // JSON encode para almacenar en la BD
                return json_encode($coordinates);
            },
            'description' => fake()->paragraph(3),
            'distance' => function (array $attributes) {
                // Calculamos la distancia real basada en las coordenadas
                $coordinates = json_decode($attributes['route_map']);
                if (!$coordinates || count($coordinates) < 2) {
                    return fake()->numberBetween(10, 500);
                }
                
                // Función para calcular la distancia entre dos puntos en km
                $calculateDistance = function($lat1, $lon1, $lat2, $lon2) {
                    $earthRadius = 6371; // Radio de la Tierra en km
                    
                    $lat1 = deg2rad($lat1);
                    $lon1 = deg2rad($lon1);
                    $lat2 = deg2rad($lat2);
                    $lon2 = deg2rad($lon2);
                    
                    $dLat = $lat2 - $lat1;
                    $dLon = $lon2 - $lon1;
                    
                    $a = sin($dLat/2) * sin($dLat/2) + 
                         cos($lat1) * cos($lat2) * 
                         sin($dLon/2) * sin($dLon/2);
                    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                    
                    return $earthRadius * $c;
                };
                
                // Suma de todas las distancias entre puntos consecutivos
                $totalDistance = 0;
                for ($i = 0; $i < count($coordinates) - 1; $i++) {
                    $totalDistance += $calculateDistance(
                        $coordinates[$i][0], $coordinates[$i][1], 
                        $coordinates[$i+1][0], $coordinates[$i+1][1]
                    );
                }
                
                return round($totalDistance, 2);
            },
            'duration' => function (array $attributes) {
                // Estimamos la duración basada en la distancia (60 km/h de velocidad media)
                $distance = $attributes['distance'];
                $hours = $distance / 90;
                return round($hours * 60); // Convertir a minutos
            },
            'totalScore' => 0,
            'countScore' => 0,
            'country_id' => function (array $attributes) {
                // Determinamos el país basado en las coordenadas de la ruta
                $coordinates = json_decode($attributes['route_map']);
                if (!$coordinates || count($coordinates) < 2) {
                    return Country::inRandomOrder()->first()->id;
                }
                
                // Usamos el punto medio de la ruta para determinar el país
                $midIndex = floor(count($coordinates) / 2);
                $lat = $coordinates[$midIndex][0];
                $lng = $coordinates[$midIndex][1];
                
                // Usamos nuestro servicio de geocodificación
                return GeocodingService::getCountryFromCoordinates($lat, $lng) ?? 
                       Country::inRandomOrder()->first()->id;
            },
            'terrain_id' => Terrain::inRandomOrder()->first()->id,
            'difficulty_id' => Difficulty::inRandomOrder()->first()->id,
            'landscape_id' => Landscape::inRandomOrder()->first()->id,            
            'user_id' => User::inRandomOrder()->first()->id,
            
        ];
    }
}
