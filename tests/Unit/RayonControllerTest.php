<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\api\RayonController;
use App\Models\Rayon;
use Illuminate\Http\Request;

class RayonControllerTest extends TestCase
{
    // Test pour makeRayon (validation échouée)
    public function test_make_rayon_validation_fails()
    {
        $controller = new RayonController();

        // Données invalides (manque le champ "category")
        $invalidData = [];

        $request = new Request($invalidData);
        $response = $controller->makeRayon($request);

        $this->assertEquals(422, $response->status()); // Validation doit échouer
    }

    // Test pour makeRayon (rayon déjà existant)
    public function test_make_rayon_already_exists()
    {
        $controller = new RayonController();

        // Créer un rayon existant
        Rayon::create(['category' => 'Existing Category']);

        // Données valides mais rayon déjà existant
        $validData = [
            'category' => 'Existing Category',
        ];

        $request = new Request($validData);
        $response = $controller->makeRayon($request);

        $this->assertEquals(200, $response->status()); // Rayon déjà existant
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Rayon already exists since ' . Rayon::first()->created_at]),
            $response->getContent()
        );
    }

    // Test pour makeRayon (succès)
    public function test_make_rayon_success()
    {
        $controller = new RayonController();

        // Données valides
        $validData = [
            'category' => 'Test Category',
        ];

        $request = new Request($validData);
        $response = $controller->makeRayon($request);

        $this->assertEquals(201, $response->status()); // Rayon créé avec succès
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Rayon has been created successfully !!!']),
            $response->getContent()
        );
    }

    // Test pour updateRayon (validation échouée)
    public function test_update_rayon_validation_fails()
    {
        $controller = new RayonController();

        // Données invalides (manque le champ "category")
        $invalidData = [];

        $request = new Request($invalidData);
        $response = $controller->updateRayon($request, 1);

        $this->assertEquals(422, $response->status()); // Validation doit échouer
    }

    // Test pour updateRayon (rayon non trouvé)
    public function test_update_rayon_not_found()
    {
        $controller = new RayonController();

        // Données valides mais rayon inexistant
        $validData = [
            'category' => 'Updated Category',
        ];

        $request = new Request($validData);
        $response = $controller->updateRayon($request, 999);

        $this->assertEquals(404, $response->status()); // Rayon non trouvé
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Rayon not found !!']),
            $response->getContent()
        );
    }

    // Test pour updateRayon (succès)
    public function test_update_rayon_success()
    {
        $controller = new RayonController();

        // Créer un rayon existant
        $rayon = Rayon::create(['category' => 'Test Category']);

        // Données valides
        $validData = [
            'category' => 'Updated Category',
        ];

        $request = new Request($validData);
        $response = $controller->updateRayon($request, $rayon->id);

        $this->assertEquals(200, $response->status()); // Rayon mis à jour avec succès
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Rayon updated successfully']),
            $response->getContent()
        );
    }

    // Test pour deleteRayon (rayon non trouvé)
    public function test_delete_rayon_not_found()
    {
        $controller = new RayonController();

        // Rayon inexistant
        $response = $controller->deleteRayon(new Request(), 999);

        $this->assertEquals(404, $response->status()); // Rayon non trouvé
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => '<@*@> Rayon not found !!']),
            $response->getContent()
        );
    }

    // Test pour deleteRayon (succès)
    public function test_delete_rayon_success()
    {
        $controller = new RayonController();

        // Créer un rayon existant
        $rayon = Rayon::create(['category' => 'Test Category']);

        // Supprimer le rayon
        $response = $controller->deleteRayon(new Request(), $rayon->id);

        $this->assertEquals(200, $response->status()); // Rayon supprimé avec succès
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => '<*-*> Rayon has been deleted with successfully !!']),
            $response->getContent()
        );
    }
}