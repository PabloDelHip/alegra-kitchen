<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\IngredientRepository;
use App\Services\PurchaseHistoryService;

class IngredientController extends Controller
{
    public function __construct(
        protected IngredientRepository $ingredientRepository,
        protected PurchaseHistoryService $purchaseHistoryService
    ) {}

    public function index(): JsonResponse
    {
        $ingredients = $this->ingredientRepository->getAll();

        return response()->json([
            'data' => $ingredients,
            'total' => $ingredients->count(),
        ]);
    }

    public function buy(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $result = $this->purchaseHistoryService->buyFromMarket(strtolower($request->input('name')));

        return response()->json($result);
    }
}

