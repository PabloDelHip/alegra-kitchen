<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class MetricsRepository
{
    public function getMostUsedIngredients()
    {
        return DB::table('order_dishes')
            ->select('ingredients.name', DB::raw('SUM(recipe_ingredients.quantity) as total_used'))
            ->join('recipes', 'order_dishes.recipe_id', '=', 'recipes.id')
            ->join('recipe_ingredients', 'recipes.id', '=', 'recipe_ingredients.recipe_id')
            ->join('ingredients', 'recipe_ingredients.ingredient_id', '=', 'ingredients.id')
            ->where('order_dishes.status', 'ready')
            ->groupBy('ingredients.name')
            ->orderByDesc('total_used')
            ->get();
    }

    public function getTopRecipes()
    {
        return DB::table('order_dishes')
            ->select('recipes.name', DB::raw('COUNT(*) as total'))
            ->join('recipes', 'order_dishes.recipe_id', '=', 'recipes.id')
            ->where('order_dishes.status', 'ready')
            ->groupBy('recipes.name')
            ->orderByDesc('total')
            ->get();
    }
}
