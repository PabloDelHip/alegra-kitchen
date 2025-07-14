<?php

namespace App\Repositories;

use App\Models\Recipe;
use Illuminate\Support\Collection;

class RecipeRepository
{
  public function getRandom(int $limit): Collection
  {
      $allRecipes = Recipe::all();
  
      $selected = collect();
  
      for ($i = 0; $i < $limit; $i++) {
          $selected->push($allRecipes->random());
      }
  
      return $selected;
  }

  public function getAllWithIngredients()
  {
      return Recipe::with(['ingredients' => function ($query) {
          $query->select('ingredients.id', 'name');
      }])->get();
  }
}
