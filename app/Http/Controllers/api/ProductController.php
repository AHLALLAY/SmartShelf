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
            'category_id' => ['required', 'integer', 'min:1'],
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

    public function updateProduct(Request $request, $id)
    {
        try {
            $validated_data = $request->validate([
                'name' => ['required', 'string', 'max:50'],
                'price' => ['required', 'numeric', 'min:0.5'],
                'category_id' => ['required', 'integer', 'min:1'],
                'hasDiscount' => ['nullable', 'boolean'],
                'quantityInitiale' => ['required', 'integer', 'min:1'],
                'quantitySales' => ['required', 'integer', 'min:0'],
                'quantityAvailable' => ['required', 'integer', 'min:0'],
            ]);

            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    "message" => "Rayon not found !!"
                ], 404);
            }

            $product->name = $validated_data['name'];
            $product->price = $validated_data['price'];
            $product->category_id = $validated_data['category_id'];
            $product->hasDiscount = $validated_data['hasDiscount'] ?? false;
            $product->quantityInitiale = $validated_data['quantityInitiale'];
            $product->quantitySales = $validated_data['quantitySales'];
            $product->quantityAvailable = $validated_data['quantityAvailable'];

            if ($product->quantityAvailable !== ($product->quantityInitiale - $product->quantitySales)) {
                return response()->json([
                    "message" => "Inconsistent quantities: quantityAvailable must be equal to quantityInitiale - quantitySales"
                ], 400);
            }

            $result = $product->save();

            if ($result) {
                return response()->json([
                    "message" => "Product updated successfully"
                ], 200);
            } else {
                return response()->json([
                    "message" => "Failed to update Product"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "An unexpected error occurred",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function deleteProduct(Request $request, $id){
        try{
            $product = Product::find($id);

            if(!$product){
                return response()->json([
                    "message" => "<@*@> product not found !!"
                ], 404);
            }
    
            $result = $product->delete();
    
            if($result){
                return response()->json([
                    "message" => "<*-*> product has been deleted with successfully !!"
                ], 200);
            }else{
                return response()->json([
                    "message" => "<!-!> Failed to delete product"
                ], 500);
            }
        }catch(\Exception $e){
            return response()->json([
                "message" => "<@_@> Unexpected Error",
                "error" => $e->getMessage()
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
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found'
                ], 404);
            }

            $validated_data = $request->validate([
                'quantity' => ['required', 'integer', 'min:1'],
            ]);

            $result = $product->quantityAvailable - $validated_data['quantity'];

            if ($result < 0) {
                return response()->json([
                    'message' => 'Stock available not enough',
                ], 400);
            } else {
                $product->quantitySales += $validated_data['quantity'];
                $product->quantityAvailable = $result;
                $product->save();

                return response()->json([
                    'message' => 'Sale successful!',
                ], 200);
            }
        } catch (\Exception $e) {
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
            $products = Product::where('hasDiscount', true)->get();

            if ($products->isEmpty()) {
                return response()->json([
                    "message" => "Aucun produit en promotion trouvé."
                ], 404);
            }

            return response()->json([
                "data" => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Une erreur inattendue s'est produite.",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $validated_data = $request->validate([
                'name' => ['nullable', 'string', 'max:50'],
                'category' => ['nullable', 'string', 'max:50'],
            ]);

            $query = Product::query();

            if ($request->has('name') && !empty($validated_data['name'])) {
                $query->where('name', 'LIKE', '%' . $validated_data['name'] . '%');
            }

            if ($request->has('category') && !empty($validated_data['category'])) {
                $query->where('category', 'LIKE', '%' . $validated_data['category'] . '%');
            }

            $products = $query->get();

            if ($products->isEmpty()) {
                return response()->json([
                    "message" => "no product found with this name or category"
                ], 404);
            }

            return response()->json([
                "data" => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Une erreur inattendue s'est produite.",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
