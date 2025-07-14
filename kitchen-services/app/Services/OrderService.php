<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\RecipeRepository;
use App\Repositories\OrderDishRepository;

class OrderService
{
    public function __construct(
        protected OrderRepository $orderRepo,
        protected RecipeRepository $recipeRepo,
        protected OrderDishRepository $orderDishRepo,
    ) {}

    public function getGroupedOrdersByDate(string $date, int $perPage = 20)
    {
        return $this->orderRepo->getOrdersWithDishesByDate($date, $perPage);
    }

    public function getOrderDishesByStatus(string $status = 'ready', int $perPage = 10)
    {
      return $this->orderDishRepo->getByStatusPaginated($status, $perPage);
    }

    public function getAllOrderDishesPaginated(int $perPage = 10)
    {
        return $this->orderDishRepo->getAllPaginated($perPage);
    }


    public function createOrderWithRandomDishes(int $quantity): array
    {
        $order = $this->orderRepo->create();

        $recipes = $this->recipeRepo->getRandom($quantity);
        $dishes = [];

        foreach ($recipes as $recipe) {
            $orderDish = $this->orderDishRepo->create($order->id, $recipe->id);

            $dishes[] = [
                'dish' => $orderDish,
                'recipe' => $recipe,
            ];
        }

        return [
            'order' => $order,
            'dishes' => $dishes,
        ];
    }
}
