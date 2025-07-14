<?php

namespace App\Repositories;

use App\Models\IngredientPurchase;
use App\Models\Ingredient;

class PurchaseHistoryRepository
{
    public function getSummaryByDate(string $date)
    {
      return Ingredient::query()
        ->selectRaw('ingredients.id, ingredients.name, COALESCE(SUM(ip.quantity), 0) as total_quantity, MAX(ip.purchased_at) as last_update')
        ->leftJoin('ingredient_purchases as ip', function ($join) use ($date) {
            $join->on('ingredients.id', '=', 'ip.ingredient_id')
                 ->whereDate('ip.purchased_at', $date);
        })
        ->groupBy('ingredients.id', 'ingredients.name')
        ->orderBy('ingredients.name')
        ->get();
    }

    public function create(int $ingredientId, int $quantity): IngredientPurchase
    {
        return IngredientPurchase::create([
            'ingredient_id' => $ingredientId,
            'quantity' => $quantity,
            'purchased_at' => now()
        ]);
    }
}
