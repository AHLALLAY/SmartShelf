<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    public function test_password_hashing()
    {
        $user = new User([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->assertTrue(Hash::check('password123', $user->password)); // Vérifie le hachage du mot de passe
    }
}
