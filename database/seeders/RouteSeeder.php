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
use Illuminate\Support\Facades\Log;

class RouteSeeder extends Seeder
{    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando rutas de muestra...');
        
        // Crear rutas de muestra con coordenadas aleatorias válidas
        Route::factory(200)->create()->each(function ($route) {
            // Si la ruta tiene coordenadas válidas, intentar generar una imagen del mapa
            $this->generateMapImage($route);
            
            // Mostrar información de la ruta creada
            $this->updateRouteMetadata($route);
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
            }            // En lugar de generar una imagen en el servidor,
            // almacenamos un marcador especial para que el frontend
            // sepa que debe generar la imagen dinámicamente
            
            // Verificamos que las coordenadas estén correctas
            if (is_array($coordinates) && count($coordinates) >= 2) {
                // Comprobamos que route_map sea un JSON válido para asegurar que el frontend pueda usarlo
                if (!is_string($route->route_map) || empty($route->route_map)) {
                    // Si por alguna razón las coordenadas no se guardaron como JSON, las guardamos nuevamente
                    $route->route_map = json_encode($coordinates);
                }
                
                // IMPORTANTE: Marcamos la imagen para generación dinámica en el frontend
                $route->image = 'auto_generated_map';
                $route->save();
                
                $this->command->info("   → Imagen de mapa configurada para generación dinámica");
            } else {
                $this->command->warn("   → No se pudo configurar la imagen del mapa: coordenadas insuficientes");
            }
            
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
    
    /**
     * Actualiza los metadatos de la ruta (país, distancia, duración)
     */
    private function updateRouteMetadata(Route $route): void
    {
        try {
            $coordinates = json_decode($route->route_map);
            if (!$coordinates || count($coordinates) < 2) {
                $this->command->error("Ruta {$route->id}: {$route->name} - Coordenadas inválidas");
                return;
            }
            
            // Verificar que los datos de la ruta son coherentes
            if ($route->country) {
                // Obtener la coordenada central para mostrarla
                $midIndex = floor(count($coordinates) / 2);
                $lat = $coordinates[$midIndex][0];
                $lng = $coordinates[$midIndex][1];
                
                $this->command->info("✓ Ruta {$route->id}: {$route->name} - País: {$route->country->name} ({$route->country->code}), Coordenadas: {$lat},{$lng}, Distancia: {$route->distance} km, Duración: {$route->duration} min");
            } else {
                // Si no hay país asignado, intentamos determinar por qué
                $midIndex = floor(count($coordinates) / 2);
                $lat = $coordinates[$midIndex][0];
                $lng = $coordinates[$midIndex][1];
                
                $this->command->warn("⚠ Ruta {$route->id}: {$route->name} - Sin país asignado. Coordenadas: {$lat},{$lng}");
                
                // Intentamos determinar visualmente si estas coordenadas corresponden a un país conocido
                $country = null;
                if ($lat >= 36 && $lat <= 44 && $lng >= -10 && $lng <= 5) {
                    $country = "España (probablemente)";
                } elseif ($lat >= 42 && $lat <= 52 && $lng >= -5 && $lng <= 8) {
                    $country = "Francia (probablemente)";
                } elseif ($lat >= 36 && $lat <= 47 && $lng >= 7 && $lng <= 19) {
                    $country = "Italia (probablemente)";
                }
                
                if ($country) {
                    $this->command->warn("   → Coordenadas corresponden a {$country}. Verificar tabla de países.");
                }
            }
        } catch (\Exception $e) {
            Log::error('Error actualizando metadatos de ruta: ' . $e->getMessage());
            $this->command->error("Error en ruta {$route->id}: " . $e->getMessage());
        }
    }
}
