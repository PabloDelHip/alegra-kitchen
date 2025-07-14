<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $response = $this->orderService->postOrder([
                'quantity' => $request->validated()['quantity'],
                'timestamp' => now()->toISOString()
            ]);
            return response()->json($response['data'], $response['status']);
        } catch (ConnectionException) {
            return response()->json(['message' => 'El microservicio de cocina no está disponible.'], 503);
        } catch (RequestException) {
            return response()->json(['message' => 'Error inesperado al procesar la orden.'], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        return $this->handleGet('/kitchen/orders/dishes', $request->only(['page', 'per_page']));
    }

    public function grouped(Request $request): JsonResponse
    {
        $query = $request->only(['page', 'per_page']);
        $query['date'] = $request->get('date', now()->toDateString());
        return $this->handleGet('/kitchen/orders/grouped', $query);
    }

    public function listAll(Request $request): JsonResponse
    {
        $query = $request->only(['status', 'page', 'per_page']);
        $query['status'] = $query['status'] ?? 'ready';
        return $this->handleGet('/kitchen/orders/dishes/all', $query);
    }

    private function handleGet(string $endpoint, array $query = []): JsonResponse
    {
        try {
            $response = $this->orderService->getKitchenData($endpoint, $query);
            return response()->json($response['data'], $response['status']);
        } catch (ConnectionException) {
            return response()->json(['message' => 'El microservicio de cocina no está disponible.'], 503);
        } catch (RequestException) {
            return response()->json(['message' => 'Error inesperado al consultar la orden.'], 500);
        }
    }
}
