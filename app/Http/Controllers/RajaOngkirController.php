<?php

namespace App\Http\Controllers;

use App\Helpers\RajaOngkirHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RajaOngkirController extends Controller
{
    public function getProvinces(): JsonResponse
    {
        $provinces = RajaOngkirHelper::getProvinces();
        return response()->json($provinces);
    }

    public function getCities(Request $request): JsonResponse
    {
        $provinceId = $request->input('province_id');
        $cities = RajaOngkirHelper::getCities($provinceId);
        return response()->json($cities);
    }

    public function getCost(Request $request): JsonResponse
    {
        $origin = $request->input('origin');
        $destination = $request->input('destination');
        $weight = $request->input('weight', 1000);
        $courier = $request->input('courier');

        $cost = RajaOngkirHelper::getCost($origin, $destination, $weight, $courier);
        return response()->json(['cost' => $cost]);
    }


} 