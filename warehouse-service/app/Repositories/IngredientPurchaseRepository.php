<?php

namespace App\Repositories;

use App\Models\IngredientPurchase;
use App\Models\Ingredient;

class IngredientPurchaseRepository
{
    public function create(Ingredient $ingredient, int $quantity): IngredientPurchase
    {
        return IngredientPurchase::create([
            'ingredient_id' => $ingredient->id,
            'quantity' => $quantity,
            'purchased_at' => now(),
        ]);
    }
}
