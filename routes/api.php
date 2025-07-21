<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// RajaOngkir API Routes
Route::prefix('rajaongkir')->group(function () {
    Route::get('/provinces', [App\Http\Controllers\RajaOngkirController::class, 'getProvinces']);
    Route::get('/cities', [App\Http\Controllers\RajaOngkirController::class, 'getCities']);
    Route::post('/cost', [App\Http\Controllers\RajaOngkirController::class, 'getCost']);
});
