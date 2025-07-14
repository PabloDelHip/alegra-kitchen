<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EnsureIdempotency
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('Idempotency-Key');

        if (!$key) {
            return response()->json(['error' => 'Idempotency-Key header is required'], 400);
        }

        $cacheKey = 'idempotency:' . $key;

        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        $response = $next($request);

        // Guardamos la respuesta (por ejemplo, 60 minutos)
        Cache::put($cacheKey, $response->getData(true), now()->addMinutes(60));

        return $response;
    }
}
