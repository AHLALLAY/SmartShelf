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
}