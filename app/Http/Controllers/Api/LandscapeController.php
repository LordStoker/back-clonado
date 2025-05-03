<?php

namespace App\Http\Controllers\Api;

use App\Models\Landscape;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LandscapeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $landscapes = Landscape::all();
        return response()->json([
            'succes' => true ,
            'data' => $landscapes], 200);
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
    public function show(string $id)
    {
        $landscape = Landscape::find($id);
        if (!$landscape) {
            return response()->json([
                'success' => false,
                'message' => 'Landscape not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $landscape
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
