<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DishPrepared implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $orderId;
    public int $dishId;
    public string $recipeName;
    public string $dishStatus;
    public array $missingIngredients; // <- aquí el cambio

    public function __construct(
        string $orderId,
        int $dishId,
        string $recipeName,
        string $dishStatus,
        array $missingIngredients = []
    ) {
        $this->orderId = $orderId;
        $this->dishId = $dishId;
        $this->recipeName = $recipeName;
        $this->dishStatus = $dishStatus;
        $this->missingIngredients = $missingIngredients;
    }

    public function broadcastOn(): array
    {
        \Log::info("📣 Evento emitido: Orden {$this->orderId}, Platillo {$this->dishId}, Status {$this->dishStatus}");
        return [
            new Channel(env('BROADCAST_CHANNEL_ORDERS', 'orders')),
        ];
    }

    public function broadcastAs(): string
    {
        return 'dish.prepared';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->orderId,
            'dish_id' => $this->dishId,
            'recipe_name' => $this->recipeName,
            'dish_status' => $this->dishStatus,
            'missing_ingredients' => $this->missingIngredients,
        ];
    }
}

