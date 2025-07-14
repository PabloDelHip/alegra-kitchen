<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ingredient;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = [
            'tomato', 'lemon', 'potato', 'rice', 'ketchup',
            'lettuce', 'onion', 'cheese', 'meat', 'chicken'
        ];

        foreach ($ingredients as $name) {
            Ingredient::create([
                'name' => $name,
                'quantity' => 10
            ]);
        }
    }
}
