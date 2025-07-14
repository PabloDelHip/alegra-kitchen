<?php

namespace App\Services;

use App\Repositories\IngredientRepository;
use App\Repositories\IngredientPurchaseRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class WarehouseService
{
    protected ?Command $command = null;

    public function __construct(
        protected IngredientRepository $ingredientRepo,
        protected IngredientPurchaseRepository $purchaseRepo
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
        info($message);
    }

    public function processInventoryRequest(AMQPChannel $channel, AMQPMessage $msg): void
    {
        try {
            $data = $this->parseMessage($msg);
            $missing = $this->checkIngredients($data['ingredients']);

            if (empty($missing)) {
                $this->consumeIngredients($data['ingredients']);
            }

            $this->sendInventoryResponse($channel, $data['order_id'], $data['dish_id'], $missing);

            $channel->basic_ack($msg->getDeliveryTag());

            $this->log("✅ Inventario procesado para platillo {$data['dish_id']} con estado '" . (empty($missing) ? 'ready' : 'failed') . "'");

        } catch (\Throwable $e) {
            $this->handleProcessingError($channel, $msg, $e);
        }
    }

    private function parseMessage(AMQPMessage $msg): array
    {
        $data = json_decode($msg->body, true);

        if (!$data) {
            $this->log('❌ Error al decodificar JSON');
            throw new \Exception('Error al decodificar JSON');
        }

        if (empty($data['order_id']) || empty($data['dish_id'])) {
            $this->log('❌ order_id o dish_id faltantes');
            throw new \Exception('order_id o dish_id faltantes');
        }

        return $data;
    }

    private function checkIngredients(array $ingredients): array
    {
        $missing = [];

        foreach ($ingredients as $item) {
            $ingredient = $this->ingredientRepo->findByName($item['name']);

            if (!$ingredient) {
                $missing[] = ['name' => $item['name'], 'needed' => $item['quantity']];
                continue;
            }

            if ($ingredient->quantity < $item['quantity']) {
                $added = $this->attemptPurchaseFromMarket($ingredient);

                if (($ingredient->quantity + $added) < $item['quantity']) {
                    $missing[] = [
                        'name' => $item['name'],
                        'needed' => $item['quantity'] - ($ingredient->quantity + $added)
                    ];
                }
            }
        }

        return $missing;
    }

    private function consumeIngredients(array $ingredients): void
    {
        foreach ($ingredients as $item) {
            $this->ingredientRepo->decrementQuantity($item['name'], $item['quantity']);
        }
    }

    private function sendInventoryResponse(AMQPChannel $channel, string $orderId, string $dishId, array $missing): void
    {
        $payload = json_encode([
            'order_id' => $orderId,
            'dish_id' => $dishId,
            'status' => empty($missing) ? 'ready' : 'failed',
            'missing' => $missing,
        ]);

        $msgOut = new AMQPMessage($payload, [
            'content_type' => 'application/json',
            'delivery_mode' => 2
        ]);

        $queueName = config('warehouse.response_queue', 'kitchen_inventory_responses');

        $channel->queue_declare($queueName, false, true, false, false);
        $channel->basic_publish($msgOut, '', $queueName);
    }

    private function handleProcessingError(AMQPChannel $channel, AMQPMessage $msg, \Throwable $e): void
    {
        $headers = $msg->has('application_headers') ? $msg->get('application_headers') : null;
        $xDeath = $headers?->getNativeData()['x-death'][0]['count'] ?? 0;
        $maxRetries = config('warehouse.max_retries', 3);

        if ($xDeath >= $maxRetries) {
            $channel->basic_reject($msg->getDeliveryTag(), false);
            $this->log("💀 Fallo definitivo, enviado a DLQ: {$e->getMessage()}");
        } else {
            $channel->basic_reject($msg->getDeliveryTag(), false);
            $this->log("🔁 Reintentando mensaje (intento " . ($xDeath + 1) . "): {$e->getMessage()}");
        }
    }

    private function attemptPurchaseFromMarket($ingredient): int
    {
        $url = config('warehouse.market_api', 'https://recruitment.alegra.com/api/farmers-market') . '/buy?ingredient=' . $ingredient->name;
        $response = Http::get($url);

        if (!$response->ok()) {
            $this->log('❌ Error comprando ' . $ingredient->name . ': ' . $response->body());
            return 0;
        }

        $quantitySold = $response->json('quantitySold') ?? 0;

        if ($quantitySold > 0) {
            $this->ingredientRepo->incrementQuantity($ingredient, $quantitySold);
            $this->purchaseRepo->create($ingredient, $quantitySold);
        }

        return $quantitySold;
    }
}
