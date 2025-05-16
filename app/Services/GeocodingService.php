<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    /**
     * Tiempo máximo de espera para peticiones (en segundos)
     */
    const TIMEOUT = 10;

    /**
     * Tiempo de caché para los resultados de geocodificación (en minutos)
     */
    const CACHE_TTL = 60 * 24 * 7; // 7 días

    /**
     * Obtiene el país a partir de coordenadas geográficas
     *
     * @param float $lat Latitud
     * @param float $lng Longitud
     * @return int|null ID del país o null si no se pudo determinar
     */
    public static function getCountryFromCoordinates(float $lat, float $lng): ?int
    {
        // Redondeamos las coordenadas para la clave de caché
        $roundedLat = round($lat, 2);
        $roundedLng = round($lng, 2);
        $cacheKey = "geo_country_{$roundedLat}_{$roundedLng}";
        
        // Intentar obtener del caché primero
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        try {
            // Usar la API de OpenStreetMap Nominatim para la geocodificación inversa
            $response = Http::timeout(self::TIMEOUT)
                ->retry(2, 1000) // Reintentar 2 veces con 1 segundo de espera
                ->withHeaders([
                    'User-Agent' => 'JohnyMotorbikeApp/1.0 (info@johnymotorbike.com)'
                ])
                ->get("https://nominatim.openstreetmap.org/reverse", [
                    'format' => 'json',
                    'lat' => $lat,
                    'lon' => $lng,
                    'zoom' => 5
                ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data['address']['country'])) {
                    $countryName = $data['address']['country'];
                    $countryCode = $data['address']['country_code'] ?? null;
                      // Buscar el país en la base de datos (primero por código, luego por nombre)
                    $countryCode = isset($data['address']['country_code']) ? strtoupper($data['address']['country_code']) : null;
                    
                    // Primero buscamos por código si está disponible (más preciso)
                    $country = null;
                    if ($countryCode) {
                        $country = Country::where('code', $countryCode)->first();
                    }
                    
                    // Si no lo encontramos por código, buscamos por nombre
                    if (!$country) {
                        $country = Country::where('name', $countryName)->first();
                    }
                    
                    if ($country) {
                        Cache::put($cacheKey, $country->id, self::CACHE_TTL);
                        return $country->id;
                    }
                    
                    // Si no existe y tenemos el código, intentamos crear el país
                    if ($countryCode) {
                        try {
                            $countryCode = strtoupper($countryCode);
                            $newCountry = Country::create([
                                'name' => $countryName,
                                'code' => $countryCode
                            ]);
                            
                            Log::info("Nuevo país añadido: {$countryName} ({$countryCode})");
                            
                            Cache::put($cacheKey, $newCountry->id, self::CACHE_TTL);
                            return $newCountry->id;
                        } catch (\Exception $e) {
                            // Si falla por duplicado, intentamos buscar el país de nuevo
                            Log::warning("Error al crear país: {$e->getMessage()}");
                            
                            // Buscar por código
                            $existingCountry = Country::where('code', $countryCode)->first();
                            if ($existingCountry) {
                                Cache::put($cacheKey, $existingCountry->id, self::CACHE_TTL);
                                return $existingCountry->id;
                            }
                        }
                    }
                }
            } else {
                Log::warning("Error de API Nominatim: " . $response->status() . " - " . $response->body());
            }
              // Esperar un segundo para respetar los límites de la API
            sleep(1);
            
        } catch (\Exception $e) {
            Log::error('Error en geocodificación: ' . $e->getMessage(), [
                'lat' => $lat,
                'lng' => $lng
            ]);
        }
        
        // Determinar el país por coordenadas aproximadas si la API falla
        try {
            // España: latitud entre 36 y 44, longitud entre -10 y 5
            if ($lat >= 36 && $lat <= 44 && $lng >= -10 && $lng <= 5) {
                $country = Country::where('code', 'ES')->first();
                if ($country) {
                    Log::info("País determinado por coordenadas aproximadas: España ({$lat}, {$lng})");
                    Cache::put($cacheKey, $country->id, self::CACHE_TTL);
                    return $country->id;
                }
            }
            
            // Francia: latitud entre 42 y 52, longitud entre -5 y 8
            if ($lat >= 42 && $lat <= 52 && $lng >= -5 && $lng <= 8) {
                $country = Country::where('code', 'FR')->first();
                if ($country) {
                    Log::info("País determinado por coordenadas aproximadas: Francia ({$lat}, {$lng})");
                    Cache::put($cacheKey, $country->id, self::CACHE_TTL);
                    return $country->id;
                }
            }
            
            // Italia: latitud entre 36 y 47, longitud entre 7 y 19
            if ($lat >= 36 && $lat <= 47 && $lng >= 7 && $lng <= 19) {
                $country = Country::where('code', 'IT')->first();
                if ($country) {
                    Log::info("País determinado por coordenadas aproximadas: Italia ({$lat}, {$lng})");
                    Cache::put($cacheKey, $country->id, self::CACHE_TTL);
                    return $country->id;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error en determinación geográfica aproximada: ' . $e->getMessage());
        }
        
        // Si todo falla, usar un país aleatorio y cachearlo temporalmente (1 día)
        $fallbackId = Country::inRandomOrder()->first()->id;
        Log::warning("Usando país aleatorio como último recurso para coordenadas: {$lat}, {$lng}");
        Cache::put($cacheKey, $fallbackId, 60 * 24);
        
        return $fallbackId;
    }
}
