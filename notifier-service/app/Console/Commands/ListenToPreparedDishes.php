<?php

namespace App\Console\Commands;

use App\Events\DishPrepared;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ListenToPreparedDishes extends Command
{
    protected $signature = 'notifier:listen-prepared';
    protected $description = 'Escucha kitchen_prepared_orders y notifica';

    public function handle()
    {
        $host = env('RABBITMQ_HOST', 'rabbitmq');
        $port = env('RABBITMQ_PORT', 5672);
        $user = env('RABBITMQ_USER', 'guest');
        $password = env('RABBITMQ_PASSWORD', 'guest');
        $queue = env('QUEUE_KITCHEN_PREPARED', 'kitchen_prepared_orders');

        $connection = new AMQPStreamConnection($host, $port, $user, $password);
        $channel = $connection->channel();

        $channel->queue_declare($queue, false, true, false, false);
        $this->info("👂 Escuchando $queue...");

        $callback = function ($msg) use ($queue) {
            $data = json_decode($msg->body, true);

            if (!$data || !isset($data['order_id'], $data['dish_id'])) {
                $this->error("❌ Mensaje inválido");
                return;
            }

            $orderId = $data['order_id'];
            $dishId = $data['dish_id'];
            $recipeName = $data['recipe_name'] ?? '(desconocido)';
            $dishStatus = $data['dish_status'] ?? 'ready';

            $this->info("✅ Platillo listo:");
            $this->line("   Orden: $orderId");
            $this->line("   Platillo ID: $dishId");
            $this->line("   Receta: $recipeName");
            $this->line("   Estado: $dishStatus");

            event(new DishPrepared(
                $orderId,
                $dishId,
                $recipeName,
                $dishStatus,
                $data['missing_ingredients'] ?? []
            ));
        };

        $channel->basic_consume($queue, '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

}
