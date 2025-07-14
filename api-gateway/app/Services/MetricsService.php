<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;

class MetricsService
{
    protected string $kitchenUrl;

    public function __construct()
    {
        $this->kitchenUrl = config('services.kitchen.url');
    }

    public function fetchFromKitchen(string $endpoint): JsonResponse
    {
        try {
            $response = Http::timeout(3)->get($this->kitchenUrl . $endpoint);
            return response()->json($response->json(), $response->status());

        } catch (ConnectionException $e) {
            Log::error("❌ Error de conexión con cocina ({$endpoint}): {$e->getMessage()}");

            return response()->json([
                'message' => 'El microservicio de cocina no está disponible actualmente.',
            ], 503);

        } catch (RequestException $e) {
            Log::error("❌ Error HTTP al consultar ({$endpoint}): {$e->getMessage()}");

            return response()->json([
                'message' => 'Error inesperado al consultar métricas.',
            ], 500);
        }
    }
}
