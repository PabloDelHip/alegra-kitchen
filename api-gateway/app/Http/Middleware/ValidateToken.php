<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ValidateToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token no proporcionado'], 401);
        }

        $response = Http::withToken($token)->get(config('services.users.url') . '/validate-token');

        if ($response->successful()) {
            $request->merge(['auth_user' => $response->json()]);
            return $next($request);
        }

        return response()->json(['error' => 'Token inválido'], 401);
    }
}
