<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\JwtToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthTokenController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Email atau password salah.'], 401);
        }

        return response()->json([
            'token_type' => 'Bearer',
            'expires_in' => 60,
            'access_token' => JwtToken::make([
                'sub' => $user->id,
                'email' => $user->email,
            ], 60),
        ]);
    }
}
