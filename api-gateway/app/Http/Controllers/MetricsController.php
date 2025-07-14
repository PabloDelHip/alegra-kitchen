<?php

namespace App\Http\Controllers;

use App\Services\MetricsService;
use Illuminate\Http\JsonResponse;

class MetricsController extends Controller
{
    public function __construct(protected MetricsService $metricsService) {}

    public function mostUsedIngredients(): JsonResponse
    {
        return $this->metricsService->fetchFromKitchen('/metrics/ingredients-used');
    }

    public function topRecipes(): JsonResponse
    {
        return $this->metricsService->fetchFromKitchen('/metrics/recipes');
    }
}
