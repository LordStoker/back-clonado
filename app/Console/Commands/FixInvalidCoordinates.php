<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Route;
use Illuminate\Support\Facades\Log;

class FixInvalidCoordinates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routes:fix-coordinates {route_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corregir coordenadas inválidas en las rutas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routeId = $this->argument('route_id');
        
        if ($routeId) {
            $routes = Route::where('id', $routeId)->get();
            if ($routes->isEmpty()) {
                $this->error("No se encontró ninguna ruta con ID {$routeId}");
                return 1;
            }
        } else {
            $routes = Route::all();
        }
        
        $this->info("Se procesarán {$routes->count()} rutas.");
        
        $fixedRoutes = 0;
        $skippedRoutes = 0;
        $errorRoutes = 0;
        
        $this->output->progressStart($routes->count());
        
        foreach ($routes as $route) {
            $this->output->progressAdvance();
            
            if (empty($route->route_map)) {
                $this->warn("La ruta #{$route->id} ({$route->name}) no tiene coordenadas. Se omite.");
                $skippedRoutes++;
                continue;
            }
            
            try {
                $originalCoordinates = $route->route_map;
                $needsFixing = false;
                $coordinates = null;
                
                // Si ya es una cadena JSON válida, la decodificamos
                if (is_string($originalCoordinates)) {
                    try {
                        $coordinates = json_decode($originalCoordinates, true);
                        
                        // Verificar si el formato es válido
                        if (!is_array($coordinates)) {
                            $needsFixing = true;
                            $this->line("La ruta #{$route->id} no es un array JSON válido.");
                        } elseif (count($coordinates) < 2) {
                            $needsFixing = true;
                            $this->line("La ruta #{$route->id} tiene menos de 2 puntos.");
                        } else {
                            // Verificar cada punto
                            foreach ($coordinates as $point) {
                                if (!is_array($point) || count($point) !== 2 || !is_numeric($point[0]) || !is_numeric($point[1])) {
                                    $needsFixing = true;
                                    $this->line("La ruta #{$route->id} tiene puntos en formato incorrecto.");
                                    break;
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        $needsFixing = true;
                        $this->line("Error al decodificar JSON para la ruta #{$route->id}: " . $e->getMessage());
                    }
                } else {
                    $needsFixing = true;
                    $this->line("La ruta #{$route->id} no tiene coordenadas en formato string.");
                }
                
                // Si necesita corrección y es una ruta con coordenadas que podemos intentar arreglar
                if ($needsFixing && $coordinates) {
                    // Construir un array de coordenadas válido
                    $fixedCoordinates = [];
                    
                    foreach ($coordinates as $point) {
                        if (is_array($point) && count($point) >= 2) {
                            // Extraer y convertir a flotante lat y lng
                            $lat = is_numeric($point[0]) ? (float)$point[0] : null;
                            $lng = is_numeric($point[1]) ? (float)$point[1] : null;
                            
                            if ($lat !== null && $lng !== null) {
                                $fixedCoordinates[] = [$lat, $lng];
                            }
                        }
                    }
                    
                    // Si tenemos al menos 2 puntos válidos, actualizamos la ruta
                    if (count($fixedCoordinates) >= 2) {
                        $route->route_map = json_encode($fixedCoordinates);
                        $route->save();
                        
                        $this->info("✓ Ruta #{$route->id} ({$route->name}) corregida con éxito.");
                        $fixedRoutes++;
                    } else {
                        $this->error("× No se pudieron recuperar suficientes puntos válidos para la ruta #{$route->id}");
                        $errorRoutes++;
                    }
                } elseif (!$needsFixing) {
                    $this->line("✓ La ruta #{$route->id} ({$route->name}) ya tiene coordenadas válidas.");
                    $skippedRoutes++;
                } else {
                    $this->error("× No se pudo corregir la ruta #{$route->id} ({$route->name})");
                    $errorRoutes++;
                }
                
            } catch (\Exception $e) {
                $this->error("Error al procesar la ruta #{$route->id}: " . $e->getMessage());
                $errorRoutes++;
            }
        }
        
        $this->output->progressFinish();
        
        $this->info("\nResultados:");
        $this->info("- Rutas corregidas: {$fixedRoutes}");
        $this->info("- Rutas omitidas (ya válidas): {$skippedRoutes}");
        $this->info("- Rutas con error: {$errorRoutes}");
        
        if ($errorRoutes > 0) {
            $this->warn("\nAlgunas rutas no pudieron ser corregidas. Revise los errores anteriores.");
            return 1;
        }
        
        $this->info("\nProceso completado correctamente.");
        return 0;
    }
}
