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
            'price' => ['required', 'decimal', 'min:0.5'],
            'departement_id' => ['required', 'integer', 'exists:departements,id'],
            'hasDiscount' => ['nullable', 'boolean'],
            'isAvailable' => ['required', 'boolean'],
        ]);

        $product = Product::create($validated_data);

        return response()->json([
            'message' => 'Product created successfully!',
            'product' => $product,
        ], 201);
    }
}