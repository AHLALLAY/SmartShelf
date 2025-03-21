<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\api\ProductController;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductControllerTest extends TestCase
{
    // Test pour createProduct
    public function test_create_product_validation_fails()
    {
        $controller = new ProductController();

        // Données invalides (manque le champ "name")
        $invalidData = [
            'price' => 10.99,
            'category_id' => 1,
            'quantityInitiale' => 100,
            'quantitySales' => 0,
            'quantityAvailable' => 100,
        ];

        $request = new Request($invalidData);
        $response = $controller->createProduct($request);

        $this->assertEquals(422, $response->status()); // Validation doit échouer
    }

    public function test_create_product_success()
    {
        $controller = new ProductController();

        // Données valides
        $validData = [
            'name' => 'Test Product',
            'price' => 10.99,
            'category_id' => 1,
            'quantityInitiale' => 100,
            'quantitySales' => 0,
            'quantityAvailable' => 100,
        ];

        $request = new Request($validData);
        $response = $controller->createProduct($request);

        $this->assertEquals(201, $response->status()); // Produit créé avec succès
    }

    // Test pour updateProduct
    public function test_update_product_not_found()
    {
        $controller = new ProductController();

        // ID inexistant
        $request = new Request();
        $response = $controller->updateProduct($request, 999);

        $this->assertEquals(404, $response->status()); // Produit non trouvé
    }

    public function test_update_product_validation_fails()
    {
        $controller = new ProductController();

        // Données invalides (manque le champ "name")
        $invalidData = [
            'price' => 10.99,
            'category_id' => 1,
            'quantityInitiale' => 100,
            'quantitySales' => 0,
            'quantityAvailable' => 100,
        ];

        $request = new Request($invalidData);
        $response = $controller->updateProduct($request, 1);

        $this->assertEquals(422, $response->status()); // Validation doit échouer
    }

    public function test_update_product_success()
    {
        $controller = new ProductController();

        // Données valides
        $validData = [
            'name' => 'Updated Product',
            'price' => 15.99,
            'category_id' => 1,
            'quantityInitiale' => 200,
            'quantitySales' => 50,
            'quantityAvailable' => 150,
        ];

        $request = new Request($validData);
        $response = $controller->updateProduct($request, 1);

        $this->assertEquals(200, $response->status()); // Produit mis à jour avec succès
    }

    // Test pour deleteProduct
    public function test_delete_product_not_found()
    {
        $controller = new ProductController();

        // ID inexistant
        $response = $controller->deleteProduct(new Request(), 999);

        $this->assertEquals(404, $response->status()); // Produit non trouvé
    }

    public function test_delete_product_success()
    {
        $controller = new ProductController();

        // Suppression réussie
        $response = $controller->deleteProduct(new Request(), 1);

        $this->assertEquals(200, $response->status()); // Produit supprimé avec succès
    }

    // Test pour displayProductAvailable
    public function test_display_product_available_empty()
    {
        $controller = new ProductController();

        // Aucun produit disponible
        $response = $controller->displayProductAvailable();

        $this->assertEquals(200, $response->status()); // Aucun produit disponible
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'no product is available']),
            $response->getContent()
        );
    }

    public function test_display_product_available_success()
    {
        $controller = new ProductController();

        // Produits disponibles
        $response = $controller->displayProductAvailable();

        $this->assertEquals(200, $response->status()); // Produits disponibles
        $this->assertArrayHasKey('data', $response->getData(true));
    }

    // Test pour saleProduct
    public function test_sale_product_not_found()
    {
        $controller = new ProductController();

        // ID inexistant
        $request = new Request(['quantity' => 10]);
        $response = $controller->saleProduct($request, 999);

        $this->assertEquals(404, $response->status()); // Produit non trouvé
    }

    public function test_sale_product_stock_not_enough()
    {
        $controller = new ProductController();

        // Stock insuffisant
        $request = new Request(['quantity' => 1000]);
        $response = $controller->saleProduct($request, 1);

        $this->assertEquals(400, $response->status()); // Stock insuffisant
    }

    public function test_sale_product_success()
    {
        $controller = new ProductController();

        // Vente réussie
        $request = new Request(['quantity' => 10]);
        $response = $controller->saleProduct($request, 1);

        $this->assertEquals(200, $response->status()); // Vente réussie
    }

    // Test pour displayProductPopulare
    public function test_display_product_popular_empty()
    {
        $controller = new ProductController();

        // Aucun produit populaire
        $response = $controller->displayProductPopulare();

        $this->assertEquals(404, $response->status()); // Aucun produit populaire trouvé
    }

    public function test_display_product_popular_success()
    {
        $controller = new ProductController();

        // Produits populaires
        $response = $controller->displayProductPopulare();

        $this->assertEquals(200, $response->status()); // Produits populaires trouvés
        $this->assertArrayHasKey('data', $response->getData(true));
    }

    // Test pour displayProductPromo
    public function test_display_product_promo_empty()
    {
        $controller = new ProductController();

        // Aucun produit en promotion
        $response = $controller->displayProductPromo();

        $this->assertEquals(404, $response->status()); // Aucun produit en promotion trouvé
    }

    public function test_display_product_promo_success()
    {
        $controller = new ProductController();

        // Produits en promotion
        $response = $controller->displayProductPromo();

        $this->assertEquals(200, $response->status()); // Produits en promotion trouvés
        $this->assertArrayHasKey('data', $response->getData(true));
    }

    // Test pour search
    public function test_search_no_results()
    {
        $controller = new ProductController();

        // Aucun résultat
        $request = new Request(['name' => 'Nonexistent']);
        $response = $controller->search($request);

        $this->assertEquals(404, $response->status()); // Aucun produit trouvé
    }

    public function test_search_success()
    {
        $controller = new ProductController();

        // Recherche réussie
        $request = new Request(['name' => 'Test']);
        $response = $controller->search($request);

        $this->assertEquals(200, $response->status()); // Produits trouvés
        $this->assertArrayHasKey('data', $response->getData(true));
    }

    // Test pour checkStock
    public function test_check_stock_no_critical_or_out_of_stock()
    {
        $controller = new ProductController();

        // Aucun produit critique ou en rupture de stock
        $response = $controller->checkStock();

        $this->assertEquals(200, $response->status()); // Aucun produit critique ou en rupture
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'No critical or out-of-stock products found. Email not sent.']),
            $response->getContent()
        );
    }

    public function test_check_stock_critical_stock()
    {
        $controller = new ProductController();

        // Produits critiques
        $response = $controller->checkStock();

        $this->assertEquals(200, $response->status()); // Produits critiques trouvés
        $this->assertArrayHasKey('data', $response->getData(true));
    }
}