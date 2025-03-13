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
                    'data' => $productAvailable,
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
                'quantity' => ['required', 'integer', 'min:1'],
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
                $product->quantityAvailable = $result;
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

    public function displayProductPopulare()
    {
        try {
            $popularProduct = Product::orderBy('quantitySales', 'desc')->limit(3)->get();

            if ($popularProduct->isEmpty()) {
                return response()->json([
                    "message" => "no populare product foun !!!",
                ], 404);
            } else {
                return response()->json([
                    "data" => $popularProduct,
                ], 200);
            }
        } catch (\Exception $e) {
            return  response()->json([
                "message" => "Unexpected Error",
                "Error" => $e->getMessage()
            ], 500);
        }
    }

    public function displayProductPromo()
    {
        try {
            // Récupérer les produits en promotion
            $products = Product::where('hasDiscount', true)->get();

            // Vérifier si des produits ont été trouvés
            if ($products->isEmpty()) {
                return response()->json([
                    "message" => "Aucun produit en promotion trouvé."
                ], 404);
            }

            // Retourner les produits en promotion
            return response()->json([
                "data" => $products
            ], 200);
        } catch (\Exception $e) {
            // Gestion des erreurs inattendues
            return response()->json([
                "message" => "Une erreur inattendue s'est produite.",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            // Valider les paramètres de la requête
            $validated_data = $request->validate([
                'name' => ['nullable', 'string', 'max:50'],
                'category' => ['nullable', 'string', 'max:50'],
            ]);

            // Commencer la requête de recherche
            $query = Product::query();

            // Filtrer par nom si le paramètre est fourni
            if ($request->has('name') && !empty($validated_data['name'])) {
                $query->where('name', 'LIKE', '%' . $validated_data['name'] . '%');
            }

            // Filtrer par catégorie si le paramètre est fourni
            if ($request->has('category') && !empty($validated_data['category'])) {
                $query->where('category', 'LIKE', '%' . $validated_data['category'] . '%');
            }

            // Exécuter la requête et récupérer les résultats
            $products = $query->get();

            // Vérifier si des produits ont été trouvés
            if ($products->isEmpty()) {
                return response()->json([
                    "message" => "no product found with this name or category"
                ], 404);
            }

            // Retourner les produits trouvés
            return response()->json([
                "data" => $products
            ], 200);
        } catch (\Exception $e) {
            // Gestion des erreurs inattendues
            return response()->json([
                "message" => "Une erreur inattendue s'est produite.",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
