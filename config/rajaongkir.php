<?php

return [
    'api_key' => env('RAJAONGKIR_API_KEY', '0df6d5bf733214af6c6644eb8717c92c'),
    'account_type' => env('RAJAONGKIR_ACCOUNT_TYPE', 'starter'),
    'base_url' => env('RAJAONGKIR_BASE_URL', 'https://api.rajaongkir.com'),
    
    // Sandbox API Key (gratis untuk testing)
    'sandbox_api_key' => '0df6d5bf733214af6c6644eb8717c92c',
    
    // Production API Key (berbayar)
    'production_api_key' => env('RAJAONGKIR_PRODUCTION_API_KEY'),
    
    // API Key untuk shipping cost
    'shipping_cost_api_key' => '47bge3Cra2a7c52635f1f74bEjLsRjhF',
    

    
    // Mode: 'sandbox', 'production', atau 'custom'
    'mode' => env('RAJAONGKIR_MODE', 'sandbox'),
]; 