<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleApiController extends Controller
{
    /**
     * Get vehicle makes by type
     */
    public function getMakes(Request $request)
    {
        try {
            $vehicleType = $request->get('type');
            $search = $request->get('search', '');

            if (!$vehicleType) {
                return response()->json(['error' => 'Vehicle type is required'], 400);
            }

            $query = DB::table('vehicle_makes')
                ->where('type', $vehicleType)
                ->orderBy('name');

            if (!empty($search)) {
                $query->where('name', 'like', "%{$search}%");
            }

            $makes = $query->get(['id', 'name']);

            return response()->json($makes);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get vehicle models by make
     */
    public function getModels(Request $request)
    {
        try {
            $makeId = $request->get('make_id');
            $search = $request->get('search', '');

            if (!$makeId) {
                return response()->json([]);
            }

            $query = DB::table('vehicle_models')
                ->where('make_id', $makeId)
                ->orderBy('name');

            if (!empty($search)) {
                $query->where('name', 'like', "%{$search}%");
            }

            $models = $query->get(['id', 'name']);

            return response()->json($models);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get all makes and models for a specific vehicle type
     */
    public function getMakesWithModels(Request $request)
    {
        $vehicleType = $request->get('type');

        $makes = DB::table('vehicle_makes')
            ->where('type', $vehicleType)
            ->orderBy('name')
            ->get(['id', 'name']);

        $result = [];
        foreach ($makes as $make) {
            $models = DB::table('vehicle_models')
                ->where('make_id', $make->id)
                ->orderBy('name')
                ->get(['id', 'name']);

            $result[] = [
                'id' => $make->id,
                'name' => $make->name,
                'models' => $models
            ];
        }

        return response()->json($result);
    }
}