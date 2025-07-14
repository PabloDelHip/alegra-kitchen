<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_code', 'status'];
    public function dishes()
    {
        return $this->hasMany(OrderDish::class);
    }
}
