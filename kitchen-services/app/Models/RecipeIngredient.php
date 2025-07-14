<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeIngredient extends Model
{
    protected $fillable = ['recipe_id', 'ingredient', 'quantity'];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}

