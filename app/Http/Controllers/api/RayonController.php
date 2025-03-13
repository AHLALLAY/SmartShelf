<?php

namespace App\Http\Controllers\api;

use App\Models\Rayon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RayonController extends Controller
{
    public function makeRayon(Request $request)
    {
        $validated_data = $request->validate([
            'category' => ['required', 'string', 'max:30']
        ]);

        try {
            if (Rayon::where('category', $validated_data['category'])->exists()) {
                $rayon = Rayon::where('category', $validated_data['category'])->first();
                return response()->json([
                    "message" => "Rayon already exists since " . $rayon->created_at
                ], 200);
            } else {
                $rayon = Rayon::create(['category' => $validated_data['category']]);

                return response()->json([
                    "message" => "Rayon has been created successfully !!!"
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An error occurred while creating the Rayon.",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
