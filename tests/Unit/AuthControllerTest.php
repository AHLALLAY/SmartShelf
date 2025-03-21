<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\api\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    // Test pour register (validation échouée)
    public function test_register_validation_fails()
    {
        $controller = new AuthController();

        // Données invalides (manque le champ "name")
        $invalidData = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'roles' => 'client',
        ];

        $request = new Request($invalidData);
        $response = $controller->register($request);

        $this->assertEquals(422, $response->status()); // Validation doit échouer
    }

    // Test pour register (utilisateur déjà existant)
    public function test_register_user_already_exists()
    {
        $controller = new AuthController();

        // Créer un utilisateur existant
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'roles' => 'client',
        ]);

        // Données valides mais email déjà existant
        $validData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'roles' => 'client',
        ];

        $request = new Request($validData);
        $response = $controller->register($request);

        $this->assertEquals(200, $response->status()); // Utilisateur déjà existant
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'john@example.com already exist !!!!']),
            $response->getContent()
        );
    }

    // Test pour register (succès)
    public function test_register_success()
    {
        $controller = new AuthController();

        // Données valides
        $validData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'roles' => 'client',
        ];

        $request = new Request($validData);
        $response = $controller->register($request);

        $this->assertEquals(201, $response->status()); // Utilisateur créé avec succès
        $this->assertArrayHasKey('token', $response->getData(true)); // Token retourné
    }

    // Test pour login (validation échouée)
    public function test_login_validation_fails()
    {
        $controller = new AuthController();

        // Données invalides (manque le champ "password")
        $invalidData = [
            'email' => 'test@example.com',
        ];

        $request = new Request($invalidData);
        $response = $controller->login($request);

        $this->assertEquals(422, $response->status()); // Validation doit échouer
    }

    // Test pour login (utilisateur non trouvé)
    public function test_login_user_not_found()
    {
        $controller = new AuthController();

        // Données valides mais utilisateur inexistant
        $validData = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ];

        $request = new Request($validData);
        $response = $controller->login($request);

        $this->assertEquals(401, $response->status()); // Utilisateur non trouvé
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Invalid credential']),
            $response->getContent()
        );
    }

    // Test pour login (mot de passe incorrect)
    public function test_login_incorrect_password()
    {
        $controller = new AuthController();

        // Créer un utilisateur existant
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'roles' => 'client',
        ]);

        // Données valides mais mot de passe incorrect
        $validData = [
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ];

        $request = new Request($validData);
        $response = $controller->login($request);

        $this->assertEquals(401, $response->status()); // Mot de passe incorrect
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Invalid credential']),
            $response->getContent()
        );
    }

    // Test pour login (succès)
    public function test_login_success()
    {
        $controller = new AuthController();

        // Créer un utilisateur existant
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'roles' => 'client',
        ]);

        // Données valides
        $validData = [
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $request = new Request($validData);
        $response = $controller->login($request);

        $this->assertEquals(200, $response->status()); // Connexion réussie
        $this->assertArrayHasKey('access_token', $response->getData(true)); // Token retourné
    }
}