<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class WarehouseService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.warehouse.url');
    }

    public function get(string $endpoint, array $query = []): array
    {
        try {
            $response = Http::timeout(3)->get($this->baseUrl . $endpoint, $query);
            return ['data' => $response->json(), 'status' => $response->status()];
        } catch (ConnectionException|RequestException $e) {
            Log::error('❌ Error al consultar warehouse: ' . $e->getMessage());
            throw $e;
        }
    }

    public function post(string $endpoint, array $payload): array
    {
        try {
            $response = Http::timeout(3)->post($this->baseUrl . $endpoint, $payload);
            return ['data' => $response->json(), 'status' => $response->status()];
        } catch (ConnectionException|RequestException $e) {
            Log::error('❌ Error al enviar datos a warehouse: ' . $e->getMessage());
            throw $e;
        }
    }
}