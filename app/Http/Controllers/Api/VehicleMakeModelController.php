<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleMakeModelController extends Controller
{
    public function getMakes(Request $request)
    {
        $type = $request->get('type', 'car');
        
        $makes = DB::table('vehicle_makes')
            ->where('type', $type)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(function ($make) {
                return [
                    'id' => $make->id,
                    'name' => $make->name
                ];
            });
        
        return response()->json($makes);
    }
    
    public function getModels(Request $request)
    {
        $makeName = $request->get('make_name');
        
        if (!$makeName) {
            return response()->json([]);
        }
        
        $models = DB::table('vehicle_models')
            ->join('vehicle_makes', 'vehicle_models.make_id', '=', 'vehicle_makes.id')
            ->where('vehicle_makes.name', $makeName)
            ->where('vehicle_models.is_active', true)
            ->orderBy('vehicle_models.name')
            ->get(['vehicle_models.id', 'vehicle_models.name'])
            ->map(function ($model) {
                return [
                    'id' => $model->id,
                    'name' => $model->name
                ];
            });
        
        return response()->json($models);
    }
}