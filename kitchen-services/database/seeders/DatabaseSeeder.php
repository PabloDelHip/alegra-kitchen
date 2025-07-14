<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use App\Models\Recipe;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Lista de ingredientes base
        $ingredients = [
            'tomato', 'lemon', 'potato', 'rice', 'ketchup',
            'lettuce', 'onion', 'cheese', 'meat', 'chicken'
        ];

        $ingredientMap = [];

        foreach ($ingredients as $name) {
            $ingredientMap[$name] = Ingredient::create(['name' => $name]);
        }

        // Recetas con sus ingredientes y cantidades
        $recipes = [
            'Arroz con Pollo' => ['rice' => 2, 'chicken' => 1, 'onion' => 1],
            'Ensalada Fresca' => ['lettuce' => 2, 'tomato' => 1, 'lemon' => 1],
            'Papas con Queso' => ['potato' => 2, 'cheese' => 1, 'ketchup' => 1],
            'Hamburguesa' => ['meat' => 1, 'lettuce' => 1, 'onion' => 1],
            'Pollo al Limón' => ['chicken' => 1, 'lemon' => 1, 'rice' => 1],
            'Taco de Carne' => ['meat' => 1, 'onion' => 1, 'tomato' => 1],
        ];

        foreach ($recipes as $recipeName => $ingredientsList) {
            $recipe = Recipe::create(['name' => $recipeName]);

            foreach ($ingredientsList as $ingredientName => $quantity) {
                $recipe->ingredients()->attach(
                    $ingredientMap[$ingredientName]->id,
                    ['quantity' => $quantity]
                );
            }
        }
    }
}
