<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
        $isExistUser = User::where('email', $validated_data['email'])->first();
        if ($isExistUser) {
            return Response()->json([
                "message" => $validated_data['email'] . " already exist !!!!"
            ]);
        } else {
            $user = User::create($validated_data);
            if ($user) {
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'message' => 'the account for ' . explode('@', $validated_data['email'])[0] . ' created successfully !!!',
                    'token' => $token
                ], 201);
            } else {
                return response()->json([
                    'message' => 'Unexpected error occurred.'
                ], 500);
            }
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
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'message' => explode('@', $user->email)[0] . ' connected !!!',
                'access_token' => $token
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid credential',
            ], 401);
        }
    }
}
