<?php

namespace App\Http\Controllers\Api;

use App\Models\Terrain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TerrainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $terrains = Terrain::all();
        return response()->json([
            'success' => true,
            'data' => $terrains
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Terrain $terrain)
    {
        $terrain = Terrain::with(['routes'])->find($terrain->id);
        if (!$terrain) {
            return response()->json([
                'success' => false,
                'message' => 'Terrain not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $terrain
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
