<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;

class OrderService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.kitchen.url');
    }

    public function postOrder(array $payload): array
    {
        try {
            $response = Http::timeout(3)->post($this->baseUrl . '/kitchen/orders', $payload)->throw();
            return ['data' => $response->json(), 'status' => $response->status()];
        } catch (ConnectionException|RequestException $e) {
            Log::error('❌ Error al enviar orden a cocina: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function getKitchenData(string $endpoint, array $query = []): array
    {
        try {
            $response = Http::timeout(3)->get($this->baseUrl . $endpoint, $query)->throw();
            return ['data' => $response->json(), 'status' => $response->status()];
        } catch (ConnectionException|RequestException $e) {
            Log::error('❌ Error al consultar cocina: ' . $e->getMessage());
            throw $e;
        }
    }
}
