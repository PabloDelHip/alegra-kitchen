<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KitchenOrderService;
use App\Http\Requests\StoreKitchenOrderRequest;

class KitchenOrderController extends Controller
{
    public function __construct(
        protected KitchenOrderService $kitchenOrderService
    ) {}

    public function getOrdersWithDishes(Request $request)
    {
        return response()->json(
            $this->kitchenOrderService->getOrdersWithDishes($request->input('date'), $request->input('per_page', 20))
        );
    }

    public function getAllDishes(Request $request)
    {
        return response()->json(
            $this->kitchenOrderService->getAllDishes($request->input('per_page', 10))
        );
    }

    public function getDishesByStatus(Request $request)
    {
        return response()->json(
            $this->kitchenOrderService->getDishesByStatus(
                $request->input('status', 'ready'),
                $request->input('per_page', 10)
            )
        );
    }

    public function store(StoreKitchenOrderRequest $request)
    {
        return response()->json(
            $this->kitchenOrderService->storeOrder($request->input('quantity'))
        );
    }

    public function resendWaitingDishes()
    {
        return response()->json(
            $this->kitchenOrderService->resendWaitingDishes()
        );
    }
}
