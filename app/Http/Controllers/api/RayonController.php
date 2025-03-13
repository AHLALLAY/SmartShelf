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

    public function updateRayon(Request $request, $id)
    {
        try {
            $validated_data = $request->validate([
                'category' => ['required', 'string', 'max:30']
            ]);

            $rayon = Rayon::find($id);

            if (!$rayon) {
                return response()->json([
                    "message" => "Rayon not found !!"
                ], 404);
            }

            $rayon->category = $validated_data['category'];
            $result = $rayon->save();

            if ($result) {
                return response()->json([
                    "message" => "Rayon updated successfully"
                ], 200);
            } else {
                return response()->json([
                    "message" => "Failed to update Rayon"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function deleteRayon(Request $request, $id){
        try{
            $rayon = Rayon::find($id);

            if(!$rayon){
                return response()->json([
                    "message" => "<@*@> Rayon not found !!"
                ], 404);
            }
    
            $result = $rayon->delete();
    
            if($result){
                return response()->json([
                    "message" => "<*-*> Rayon has been deleted with successfully !!"
                ], 200);
            }else{
                return response()->json([
                    "message" => "<!-!> Failed to delete Rayon"
                ], 500);
            }
        }catch(\Exception $e){
            return response()->json([
                "message" => "<@_@> Unexpected Error",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
