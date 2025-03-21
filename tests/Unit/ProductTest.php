<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;

class ProductTest extends TestCase
{
    public function test_stock_availability()
    {
        $product = new Product([
            'quantityInitiale' => 100,
            'quantitySales' => 50,
            'quantityAvailable' => 50,
        ]);

        $this->assertEquals(50, $product->quantityAvailable); // Vérifie la disponibilité du stock
    }
}