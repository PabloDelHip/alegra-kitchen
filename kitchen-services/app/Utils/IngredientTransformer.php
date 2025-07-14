<?php

namespace App\Utils;

class IngredientTransformer
{
    public function transform($ingredients)
    {
        return $ingredients->map(fn($i) => [
            'name' => $i->name,
            'quantity' => $i->pivot->quantity,
        ])->toArray();
    }
}
