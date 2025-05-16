<?php

namespace App\Console\Commands;

use App\Models\Route;
use Illuminate\Console\Command;

class FixRouteImages extends Command
{
    /**
     * El nombre y la firma del comando.
     *
     * @var string
     */
    protected $signature = 'routes:fix-images';

    /**
     * La descripción del comando.
     *
     * @var string
     */
    protected $description = 'Actualiza todas las rutas para usar imágenes dinámicas de mapas';

    /**
     * Ejecutar el comando.
     */
    public function handle()
    {
        $this->info('Actualizando imágenes de rutas para usar generación dinámica...');
        
        // Obtenemos todas las rutas
        $routes = Route::all();
        $total = $routes->count();
        
        if ($total === 0) {
            $this->warn('No hay rutas en la base de datos.');
            return 0;
        }
        
        $this->info("Procesando {$total} rutas...");
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        $updated = 0;
        $skipped = 0;
        $failed = 0;
        
        foreach ($routes as $route) {
            try {
                // Verificar que tiene coordenadas válidas
                $coordinates = json_decode($route->route_map);
                
                if (!$coordinates || count($coordinates) < 2) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }
                
                // Actualizar la ruta para usar generación dinámica de imágenes
                $route->image = 'auto_generated_map';
                $route->save();
                
                $updated++;
            } catch (\Exception $e) {
                $failed++;
                $this->error("Error en ruta ID {$route->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        // Mostrar resumen
        $this->info("Proceso completado!");
        $this->table(
            ['Resultado', 'Cantidad'],
            [
                ['Rutas actualizadas', $updated],
                ['Rutas omitidas (sin coordenadas)', $skipped],
                ['Rutas con error', $failed],
                ['Total procesadas', $total],
            ]
        );
        
        return 0;
    }
}
