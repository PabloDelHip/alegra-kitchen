<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientPurchase extends Model
{
    protected $fillable = ['ingredient_id', 'quantity', 'purchased_at'];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
