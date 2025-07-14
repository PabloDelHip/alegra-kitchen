<?php

namespace App\Repositories;

use App\Models\OrderDish;
use Illuminate\Database\Eloquent\Collection;

class OrderDishRepository
{
    public function create(int $orderId, int $recipeId): OrderDish
    {
        return OrderDish::create([
            'order_id' => $orderId,
            'recipe_id' => $recipeId,
            'status' => 'waiting',
        ]);
    }

    public function updateStatusAndMissing($dish, string $status, array $missing = []): void
    {
        $dish->status = $status;
        $dish->missing_ingredients = !empty($missing) ? json_encode($missing) : null;
        $dish->save();
    }

    public function getWaitingDishes($limit = 20)
    {
        return OrderDish::where('status', 'waiting')
            ->orderBy('updated_at', 'asc')
            ->with('order', 'recipe.ingredients')
            ->limit($limit)
            ->get();
    }

    public function findByIdAndOrder(int $dishId, int $orderId): ?OrderDish
    {
        return OrderDish::where('id', $dishId)
            ->where('order_id', $orderId)
            ->with('recipe')
            ->first();
    }

    public function getAllPaginated(int $perPage = 10)
    {
        return OrderDish::with('recipe', 'order')
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }

    public function getByStatusPaginated(string $status = 'ready', int $perPage = 10)
    {
        return OrderDish::with('recipe', 'order')
            ->where('status', $status)
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }

    public function updateStatus(OrderDish $dish, string $status): void
    {
        $dish->status = $status;
        $dish->save();
    }
}
