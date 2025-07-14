<?php

namespace App\Http\Controllers;

use App\Services\KitchenService;
use Illuminate\Http\JsonResponse;

class KitchenController extends Controller
{
    public function __construct(protected KitchenService $kitchenService) {}

    public function recipe(): JsonResponse
    {
        return $this->kitchenService->fetchRecipes();
    }
}
