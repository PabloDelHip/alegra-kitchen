<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WarehouseService;
use App\Http\Requests\BuyIngredientRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class WarehouseIngredientsController extends Controller
{
    public function __construct(protected WarehouseService $warehouseService) {}

    public function index(): JsonResponse
    {
        return $this->handleGet('/ingredients');
    }

    public function purchase(Request $request): JsonResponse
    {
        return $this->handleGet('/purchases', [
            'date' => $request->get('date', now()->toDateString())
        ]);
    }

    public function buy(BuyIngredientRequest $request): JsonResponse
    {
        try {
            $response = $this->warehouseService->post('/ingredients/buy', [
                'name' => $request->validated()['name'],
            ]);
            return response()->json($response['data'], $response['status']);
        } catch (ConnectionException) {
            return response()->json(['message' => 'El microservicio de warehouse no está disponible.'], 503);
        } catch (RequestException) {
            return response()->json(['message' => 'Error inesperado al procesar la compra.'], 500);
        }
    }

    private function handleGet(string $endpoint, array $query = []): JsonResponse
    {
        try {
            $response = $this->warehouseService->get($endpoint, $query);
            return response()->json($response['data'], $response['status']);
        } catch (ConnectionException) {
            return response()->json(['message' => 'El microservicio de warehouse no está disponible.'], 503);
        } catch (RequestException) {
            return response()->json(['message' => 'Error inesperado al consultar datos.'], 500);
        }
    }
}
