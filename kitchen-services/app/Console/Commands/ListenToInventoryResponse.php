<?php

namespace App\Console\Commands;

use App\Services\InventoryResponseService;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ListenToInventoryResponse extends Command
{
    protected $signature = 'app:listen-inventory-response';
    protected $description = 'Escucha las respuestas de bodega y actualiza los platos que se pueden preparar';

    public function handle()
    {
        $host = env('RABBITMQ_HOST', 'rabbitmq');
        $port = env('RABBITMQ_PORT', 5672);
        $user = env('RABBITMQ_USER', 'guest');
        $password = env('RABBITMQ_PASSWORD', 'guest');

        $queueInventory = env('RABBITMQ_QUEUE_INVENTORY_RESPONSES', 'kitchen_inventory_responses');
        $queuePrepared = env('RABBITMQ_QUEUE_PREPARED_ORDERS', 'kitchen_prepared_orders');

        $connection = new AMQPStreamConnection($host, $port, $user, $password);
        $channel = $connection->channel();

        $channel->queue_declare($queueInventory, false, true, false, false);
        $channel->queue_declare($queuePrepared, false, true, false, false);

        $this->info("👂 Escuchando {$queueInventory}...");

        $service = app(InventoryResponseService::class);
        $service->setCommand($this);

        $callback = function (AMQPMessage $msg) use ($channel, $service) {
            $service->handleMessage($channel, $msg);
        };

        $channel->basic_consume($queueInventory, '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
