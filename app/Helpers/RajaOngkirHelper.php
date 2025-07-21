<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirHelper
{
    public static function getApiKey($type = 'default')
    {
        $mode = config('rajaongkir.mode');
        
        switch ($mode) {
            case 'sandbox':
                return config('rajaongkir.sandbox_api_key');
            case 'production':
                return config('rajaongkir.production_api_key');
            case 'custom':
                // Gunakan API key yang sesuai dengan fungsinya
                switch ($type) {
                    case 'cost':
                        return config('rajaongkir.shipping_cost_api_key');
                    default:
                        return config('rajaongkir.shipping_cost_api_key'); // default untuk province/city
                }
            default:
                return config('rajaongkir.sandbox_api_key');
        }
    }

    public static function getProvinces()
    {
        try {
            $response = Http::withHeaders([
                'key' => self::getApiKey('default')
            ])->get('https://api.rajaongkir.com/starter/province');
            
            Log::info('RajaOngkir Province Response:', $response->json());
            
            return $response['rajaongkir']['results'] ?? [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir Province Error: ' . $e->getMessage());
            return [];
        }
    }

    public static function getCities($provinceId)
    {
        try {
            $response = Http::withHeaders([
                'key' => self::getApiKey('default')
            ])->get('https://api.rajaongkir.com/starter/city', [
                'province' => $provinceId
            ]);
            
            Log::info('RajaOngkir City Response:', $response->json());
            
            return $response['rajaongkir']['results'] ?? [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir City Error: ' . $e->getMessage());
            return [];
        }
    }

    public static function getCost($origin, $destination, $weight, $courier)
    {
        try {
            $response = Http::withHeaders([
                'key' => self::getApiKey('cost')
            ])->post('https://api.rajaongkir.com/starter/cost', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
            ]);
            
            Log::info('RajaOngkir Cost Response:', $response->json());
            
            $results = $response['rajaongkir']['results'] ?? [];
            if (empty($results)) {
                return 0;
            }
            
            // Ambil service pertama yang tersedia
            $firstResult = $results[0];
            $costs = $firstResult['costs'] ?? [];
            
            if (empty($costs)) {
                return 0;
            }
            
            // Ambil cost pertama yang tersedia
            $firstCost = $costs[0];
            $costDetails = $firstCost['cost'] ?? [];
            
            if (empty($costDetails)) {
                return 0;
            }
            
            return $costDetails[0]['value'] ?? 0;
        } catch (\Exception $e) {
            Log::error('RajaOngkir Cost Error: ' . $e->getMessage());
            return 0;
        }
    }




} 