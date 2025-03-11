<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated_data = $request->validate([
            'name' => ['required', 'string', 'max:30'],
            'email' => ['required', 'string', 'unique:users,email', 'email', 'max:50'],
            'password' => ['required', 'string', 'min:6'],
            'roles' => ['required', 'string', 'in:client,admin'],
        ]);

        $validated_data['password'] = Hash::make($validated_data['password']);

        $user = User::create($validated_data);
        if ($user) {
            return response()->json([
                'message' => 'User created successfully!'
            ], 201);
        } else {
            return response()->json([
                'message' => 'Unexpected error occurred.'
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validated_data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:50'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::where('email', $validated_data['email'])->first();

        if ($user && Hash::check($validated_data['password'], $user->password)) {
            return response()->json([
                'message' => 'user connected !!!',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid credential',
            ], 401);
        }
    }
}
