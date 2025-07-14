<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Str;

class OrderRepository
{
    public function create(): Order
    {
      $code = 'ORD-' . now()->format('ymd') . '-' . random_int(100, 999);  // Ej: ORD-250714-837

      return Order::create([
          'order_code' => $code,
      ]);
    }

    public function getAllPaginated(int $perPage = 10)
    {
        return OrderDish::with('recipe', 'order')
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }

    public function getOrdersWithDishesByDate(string $date, int $perPage = 20)
    {
      return Order::with('dishes.recipe')
        ->whereDate('created_at', $date)
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
      }

    public function findByCode(string $code): ?Order
    {
        return Order::where('order_code', $code)->first();
    }

    public function updateGlobalStatus(Order $order): void
    {
        $dishes = $order->dishes;
        $total = $dishes->count();
        $ready = $dishes->where('status', 'ready')->count();
        $failed = $dishes->where('status', 'failed')->count();

        $order->status = match (true) {
            $ready === $total => 'ready',
            $failed === $total => 'failed',
            $ready > 0 && $failed > 0 => 'partial',
            default => 'waiting',
        };

        $order->save();
    }
}
