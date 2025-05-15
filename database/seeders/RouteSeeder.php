<?php

namespace Database\Seeders;

use App\Models\Route;
use App\Models\User;
use App\Models\Country;
use App\Models\Terrain;
use App\Models\Landscape;
use App\Models\Difficulty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear rutas de muestra con coordenadas aleatorias válidas
        Route::factory(50)->create()->each(function ($route) {
            // Si la ruta tiene coordenadas válidas, intentar generar una imagen del mapa
            $this->generateMapImage($route);
        });
    }

    /**
     * Genera una imagen de mapa simple basada en las coordenadas de la ruta
     */
    private function generateMapImage(Route $route): void
    {
        try {
            $coordinates = json_decode($route->route_map);
            
            if (!$coordinates || count($coordinates) < 2) {
                return;
            }
            
            // Calculamos el centro y los límites de la ruta
            $minLat = $maxLat = $coordinates[0][0];
            $minLng = $maxLng = $coordinates[0][1];
            
            foreach ($coordinates as $coord) {
                $minLat = min($minLat, $coord[0]);
                $maxLat = max($maxLat, $coord[0]);
                $minLng = min($minLng, $coord[1]);
                $maxLng = max($maxLng, $coord[1]);
            }
            
            // Calculamos el centro y el zoom
            $centerLat = ($minLat + $maxLat) / 2;
            $centerLng = ($minLng + $maxLng) / 2;
            $zoom = $this->calculateZoom($maxLat - $minLat, $maxLng - $minLng);
            
            // Construimos la URL de la API de mapas estáticos (OpenStreetMap)
            $width = 640;
            $height = 480;
            
            // Crear los parámetros para dibujar la ruta
            $pathParam = 'color:0xff0000|weight:5';
            
            foreach ($coordinates as $coord) {
                $pathParam .= '|' . $coord[0] . ',' . $coord[1];
            }
            
            // Para este ejemplo, usaremos MapBox Static API (requiere una API key)
            // Otra opción sería usar OSRM (Open Source Routing Machine)
            
            // Esta es una implementación simplificada, en producción se necesitaría una API key válida
            // Simplemente almacenaremos un indicador para que el frontend sepa que debe generar la imagen
            
            // En lugar de una imagen real, almacenamos un marcador
            $route->image = 'auto_generated_map';
            $route->save();
            
            // Nota: En un entorno real, se debería implementar la generación de imágenes estáticas
            // usando un servicio como MapBox, Google Maps, o incluso generar las imágenes en el servidor
            // con una biblioteca como GD o Imagick
        } catch (\Exception $e) {
            \Log::error('Error generando imagen del mapa: ' . $e->getMessage());
        }
    }
    
    /**
     * Calcula un nivel de zoom apropiado basado en la extensión de la ruta
     */
    private function calculateZoom(float $latSpan, float $lngSpan): int
    {
        $span = max($latSpan, $lngSpan);
        
        if ($span <= 0.01) return 15; // Muy cercano
        if ($span <= 0.05) return 13;
        if ($span <= 0.1) return 12;
        if ($span <= 0.5) return 10;
        if ($span <= 1) return 9;
        if ($span <= 2) return 8;
        if ($span <= 4) return 7;
        if ($span <= 8) return 6;
        
        return 5; // Para rutas muy largas
    }
}
