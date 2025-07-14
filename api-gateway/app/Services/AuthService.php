<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AuthService
{
    public function login(array $credentials)
    {
        $response = Http::post(config('services.users.url') . '/login', $credentials);
        return response()->json($response->json(), $response->status());
    }
}
