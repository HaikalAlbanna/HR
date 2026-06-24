<?php

namespace App\Http\Middleware;

use App\Support\JwtToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyJwtToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Bearer token wajib diisi.'], 401);
        }

        try {
            $payload = JwtToken::parse($token);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }

        $request->attributes->set('jwt_payload', $payload);

        return $next($request);
    }
}
