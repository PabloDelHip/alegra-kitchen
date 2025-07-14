<?php

namespace App\Http\Controllers;

use App\Repositories\RecipeRepository;
use App\Utils\RecipeTransformer;

class KitchenRecipesController extends Controller
{
    public function __construct(
        protected RecipeRepository $recipeRepository,
        protected RecipeTransformer $recipeTransformer
    ) {}

    public function index()
    {
        $recipes = $this->recipeRepository->getAllWithIngredients();

        $data = $this->recipeTransformer->transformCollection($recipes);

        return response()->json($data);
    }
}
