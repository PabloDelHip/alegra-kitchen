<?php

namespace App\Utils;

class RecipeTransformer
{
    public function transformCollection($recipes)
    {
        return $recipes->map(function ($recipe) {
            return [
                'id' => $recipe->id,
                'name' => $recipe->name,
                'ingredients' => $recipe->ingredients->map(function ($ingredient) {
                    return [
                        'name' => $ingredient->name,
                        'quantity' => $ingredient->pivot->quantity,
                    ];
                }),
            ];
        });
    }
}
