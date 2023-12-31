<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (!Auth::attempt($validated)) {
            return response()->json([
                'message' => 'Login information invalid',
            ], 401);
        }

        /** @var User $user */
        $user = User::where('email', $validated['email'])->first();
        return  response()->json([
            'access_token' => $user->createToken('api_token')->plainTextToken,
            'access_type' => 'Bearer',
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|max:255|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        /** @var User $user */
        $user = User::create($validated);
        return  response()->json([
            'data' => $user,
            'access_token' => $user->createToken('api_token')->plainTextToken,
            'access_type' => 'Bearer',
        ], 201);
    }
}
