<?php

namespace App\Repositories;

use App\Models\Ingredient;

class IngredientRepository
{
    public function findByName(string $name)
    {
        return Ingredient::where('name', $name)->first();
    }

    public function incrementQuantity(Ingredient $ingredient, int $amount)
    {
        $ingredient->increment('quantity', $amount);
    }

    public function decrementQuantity(string $name, int $amount)
    {
        Ingredient::where('name', $name)->decrement('quantity', $amount);
    }

    public function getAll()
    {
        return Ingredient::select('id', 'name', 'quantity')->get();
    }

    public function createOrIncrement(string $name, int $quantity)
    {
        $ingredient = $this->findByName($name);

        if ($ingredient) {
            $this->incrementQuantity($ingredient, $quantity);
            return $ingredient;
        }

        return Ingredient::create([
            'name' => $name,
            'quantity' => $quantity
        ]);
    }
}
