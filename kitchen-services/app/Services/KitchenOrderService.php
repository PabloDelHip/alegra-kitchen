<?php

namespace App\Services;

use App\Models\OrderDish;
use App\Services\OrderService;
use App\Services\WarehousePublisher;
use App\Utils\IngredientTransformer;
use App\Repositories\OrderDishRepository;

class KitchenOrderService
{
  public function __construct(
    protected OrderService $orderService,
    protected WarehousePublisher $publisher,
    protected IngredientTransformer $ingredientTransformer,
    protected OrderDishRepository $orderDishRepository
) {}

    public function getOrdersWithDishes($date, $perPage)
    {
        $date = $date ?? now()->toDateString();
        return $this->orderService->getGroupedOrdersByDate($date, $perPage);
    }

    public function getAllDishes($perPage)
    {
        return $this->orderService->getAllOrderDishesPaginated($perPage);
    }

    public function getDishesByStatus($status, $perPage)
    {
        return $this->orderService->getOrderDishesByStatus($status, $perPage);
    }

    public function storeOrder($quantity)
    {
        $result = $this->orderService->createOrderWithRandomDishes($quantity);
        $order = $result['order'];
        $dishes = $result['dishes'];

        $response = [];

        foreach ($dishes as $dish) {
            $ingredients = $this->ingredientTransformer->transform($dish['recipe']->ingredients);

            $this->publisher->sendToWarehouse([
                'order_id' => $order->order_code,
                'dish_id' => $dish['dish']->id,
                'ingredients' => $ingredients,
            ]);

            $response[] = [
                'dish_id' => $dish['dish']->id,
                'order_id' => $order->order_code,
                'recipe_id' => $dish['recipe']->id,
                'recipe_name' => $dish['recipe']->name,
                'status' => 'waiting',
            ];
        }

        return [
            'message' => 'Orden recibida en cocina',
            'order_id' => $order->order_code,
            'dishes_count' => count($response),
            'dishes' => $response,
        ];
    }

    public function resendWaitingDishes()
    {
        $limit = config('kitchen.resend_limit', 20);
    
        $dishes = $this->orderDishRepository->getWaitingDishes($limit);
    
        if ($dishes->isEmpty()) {
            return ['message' => '✅ No hay platillos en espera.'];
        }
    
        $response = [];
    
        foreach ($dishes as $dish) {
            $ingredients = $this->ingredientTransformer->transform($dish->recipe->ingredients);
    
            $this->publisher->sendToWarehouse([
                'order_id' => $dish->order->order_code,
                'dish_id' => $dish->id,
                'ingredients' => $ingredients,
            ]);
    
            $response[] = [
                'dish_id' => $dish->id,
                'order_id' => $dish->order->order_code,
                'recipe_id' => $dish->recipe->id,
                'recipe_name' => $dish->recipe->name,
            ];
        }
    
        return [
            'message' => '🔄 Platillos reenviados a cocina.',
            'dishes_count' => count($response),
            'dishes' => $response,
        ];
    }
}
