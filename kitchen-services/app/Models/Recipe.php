<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = ['name'];

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function orderDishes()
    {
        return $this->hasMany(OrderDish::class);
    }

}
