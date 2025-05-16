<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Route;
use Illuminate\Support\Facades\Log;

class ValidateRouteCoordinates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routes:validate-coordinates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validar que todas las rutas tengan coordenadas válidas en su route_map';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando validación de coordenadas de rutas...');
        
        $routes = Route::all();
        $this->info("Se encontraron {$routes->count()} rutas para validar.");
        
        $invalidRoutes = 0;
        $emptyRoutes = 0;
        $validRoutes = 0;
        
        $this->output->progressStart($routes->count());
        
        foreach ($routes as $route) {
            $this->output->progressAdvance();
            
            if (empty($route->route_map)) {
                $emptyRoutes++;
                $this->warn("La ruta #{$route->id} ({$route->name}) no tiene coordenadas.");
                continue;
            }
            
            try {
                $coordinates = json_decode($route->route_map, true);
                
                // Verificar que sea un array
                if (!is_array($coordinates)) {
                    $invalidRoutes++;
                    $this->error("La ruta #{$route->id} ({$route->name}) tiene un formato de coordenadas inválido (no es un array).");
                    continue;
                }
                
                // Verificar que tenga al menos 2 puntos
                if (count($coordinates) < 2) {
                    $invalidRoutes++;
                    $this->error("La ruta #{$route->id} ({$route->name}) tiene menos de 2 puntos en sus coordenadas.");
                    continue;
                }
                
                // Verificar que cada coordenada sea un array [lat, lng]
                $hasInvalidPoint = false;
                foreach ($coordinates as $index => $point) {
                    if (!is_array($point) || count($point) !== 2 || !is_numeric($point[0]) || !is_numeric($point[1])) {
                        $hasInvalidPoint = true;
                        $this->error("La ruta #{$route->id} ({$route->name}) tiene un punto inválido en el índice {$index}.");
                        break;
                    }
                }
                
                if ($hasInvalidPoint) {
                    $invalidRoutes++;
                    continue;
                }
                
                // La ruta tiene coordenadas válidas
                $validRoutes++;
                
            } catch (\Exception $e) {
                $invalidRoutes++;
                $this->error("Error al validar la ruta #{$route->id} ({$route->name}): " . $e->getMessage());
            }
        }
        
        $this->output->progressFinish();
        
        $this->info("\nResultados de la validación:");
        $this->info("- Rutas válidas: {$validRoutes}");
        $this->info("- Rutas sin coordenadas: {$emptyRoutes}");
        $this->info("- Rutas con coordenadas inválidas: {$invalidRoutes}");
        
        if ($invalidRoutes > 0 || $emptyRoutes > 0) {
            $this->warn("\nSe recomienda corregir las rutas con coordenadas inválidas o vacías.");
            return 1;
        }
        
        $this->info("\n¡Todas las rutas tienen coordenadas válidas!");
        return 0;
    }
}
