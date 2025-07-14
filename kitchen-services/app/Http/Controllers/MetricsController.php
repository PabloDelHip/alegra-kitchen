<?php

namespace App\Http\Controllers;

use App\Repositories\MetricsRepository;

class MetricsController extends Controller
{
    public function __construct(
        protected MetricsRepository $metricsRepository
    ) {}

    public function mostUsedIngredients()
    {
        $ingredients = $this->metricsRepository->getMostUsedIngredients();

        return response()->json($ingredients);
    }

    public function topRecipes()
    {
        $recipes = $this->metricsRepository->getTopRecipes();

        return response()->json($recipes);
    }
}
