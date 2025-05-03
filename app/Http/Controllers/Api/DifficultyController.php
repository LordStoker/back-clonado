<?php

namespace App\Http\Controllers\Api;

use App\Models\Difficulty;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DifficultyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $difficulties = Difficulty::all();
        return response()->json([
            'success' => true,
            'data' => $difficulties
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
    public function show(Difficulty $difficulty)
    {
        $difficulty = Difficulty::with(['routes'])->find($difficulty->id);
        if (!$difficulty) {
            return response()->json([
                'success' => false,
                'message' => 'Difficulty not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $difficulty
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
