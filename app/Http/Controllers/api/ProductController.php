<?php

namespace App\Http\Controllers\api;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function createProduct(Request $request)
    {
        $validated_data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0.5'],
            'category' => ['required', 'string'],
            'hasDiscount' => ['nullable', 'boolean'],
            'quantityInitiale' => ['required', 'integer', 'min:1'],
            'quantitySales' => ['required', 'integer', 'min:0'],
            'quantityAvailable' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $product = Product::create($validated_data);
            return response()->json([
                'message' => 'Product created successfully!',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create product.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function displayProductAvailable()
    {
        try {

            $productAvailable = Product::where('quantityAvailable', '>', 0)->get();

            if ($productAvailable->isEmpty()) {
                return response()->json([
                    'message' => 'no product is available',
                ], 200);
            } else {
                return response()->json([
                    'product available' => $productAvailable,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'unxpected error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function saleProduct(Request $request, $id)
    {
        try {
            // Trouver le produit par son ID
            $product = Product::find($id);

            // Vérifier si le produit existe
            if (!$product) {
                return response()->json([
                    'message' => 'Product not found'
                ], 404);
            }

            // Valider les données de la requête
            $validated_data = $request->validate([
                'quantity' => ['required', 'integer', 'min:1', 'max:1000'], // Quantité valide
            ]);

            // Calculer le stock restant après la vente
            $result = $product->quantityAvailable - $validated_data['quantity'];

            // Gérer les différents cas de stock
            if ($result < 0) {
                // Stock insuffisant
                return response()->json([
                    'message' => 'Stock available not enough',
                ], 400);
            } else {
                // Stock suffisant
                $product->quantitySales += $validated_data['quantity'];
                $product->quantityAvailable = $result; // Mettre à jour le stock disponible
                $product->save();

                return response()->json([
                    'message' => 'Sale successful!',
                ], 200);
            }
        } catch (\Exception $e) {
            // Gestion des erreurs inattendues
            return response()->json([
                'message' => 'An error occurred while processing the sale.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function displayProductPopulare(){
        try{
            $popularProduct = Product::orderBy('quantitySales', 'desc')->limit(3)->get();

            if($popularProduct->isEmpty()){
                return response()->json([
                    "message" => "no populare product foun !!!",
                ], 404);
            }else{
                return response()->json([
                    "posplare product" => $popularProduct,
                ],200);
            }
        }catch(\Exception $e){
            return  response()->json([
                "message" => "Unexpected Error",
                "Error" => $e->getMessage()
            ], 500);
        }

    }
}
