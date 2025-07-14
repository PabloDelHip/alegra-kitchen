<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\OrderDishRepository;
use Illuminate\Console\Command;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class InventoryResponseService
{
    protected ?Command $command = null;

    public function __construct(
        protected OrderRepository $orderRepo,
        protected OrderDishRepository $dishRepo
    ) {}

    public function setCommand(Command $command): void
    {
        $this->command = $command;
    }

    protected function log(string $message): void
    {
        if ($this->command) {
            $this->command->info($message);
        }

        info($message); // sigue guardando en logs de Laravel
    }

    public function handleMessage(AMQPChannel $channel, AMQPMessage $msg): void
    {
        $data = json_decode($msg->body, true);

        $orderCode = $data['order_id'] ?? null;
        $dishId = $data['dish_id'] ?? null;
        $status = $data['status'] ?? null;
        $missing = $data['missing'] ?? [];

        if (!$orderCode || !$dishId || !$status) {
            $this->log("❌ Respuesta inválida: falta order_id, dish_id o status");
            return;
        }

        $order = $this->orderRepo->findByCode($orderCode);
        if (!$order) {
            $this->log("❌ Orden $orderCode no encontrada.");
            return;
        }

        $dish = $this->dishRepo->findByIdAndOrder($dishId, $order->id);
        if (!$dish) {
            $this->log("❌ Platillo $dishId no encontrado para la orden $orderCode.");
            return;
        }

        // Aquí ajustas si el status es 'failed' y tiene ingredientes faltantes
        if ($status === 'failed' && !empty($missing)) {
            $this->dishRepo->updateStatusAndMissing($dish, $status, $missing);
        } else {
            $this->dishRepo->updateStatusAndMissing($dish, $status);
        }

        $payload = [
            'order_id' => $order->order_code,
            'dish_id' => $dish->id,
            'recipe_name' => $dish->recipe->name,
            'dish_status' => $dish->status,
        ];

        if ($status === 'failed' && !empty($missing)) {
            $payload['missing_ingredients'] = $missing;
        }

        $msgOut = new AMQPMessage(json_encode($payload), ['delivery_mode' => 2]);
        $channel->basic_publish($msgOut, '', env('RABBITMQ_QUEUE_PREPARED_ORDERS', 'kitchen_prepared_orders'));

        $this->orderRepo->updateGlobalStatus($order);

        $this->log("✅ Procesado platillo {$dish->id} con estado {$dish->status} | Orden: {$order->status}");
    }
}
