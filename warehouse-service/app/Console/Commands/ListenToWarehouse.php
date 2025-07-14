<?php

namespace App\Console\Commands;

use App\Services\WarehouseService;
use App\Models\Ingredient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ListenToWarehouse extends Command
{
    protected $signature = 'warehouse:listen';
    protected $description = 'Escucha las solicitudes de ingredientes desde kitchen';

    public function handle(): void
    {
        $host = env('RABBITMQ_HOST', 'rabbitmq');
        $port = env('RABBITMQ_PORT', 5672);
        $user = env('RABBITMQ_USER', 'guest');
        $password = env('RABBITMQ_PASSWORD', 'guest');

        $queueMain  = env('RABBITMQ_QUEUE_WAREHOUSE', 'warehouse_check_inventories');
        $queueRetry = env('RABBITMQ_QUEUE_RETRY_WAREHOUSE', 'retry-warehouse-check-inventories');
        $queueDead  = env('RABBITMQ_QUEUE_DEAD_WAREHOUSE', 'dead-warehouse-check-inventories');

        $connection = new AMQPStreamConnection($host, $port, $user, $password);
        $channel = $connection->channel();
    
        $channel->queue_declare($queueDead, false, true, false, false);
        $channel->queue_declare($queueRetry, false, true, false, false, false, [
            'x-dead-letter-exchange'    => ['S', ''],
            'x-dead-letter-routing-key' => ['S', $queueMain],
            'x-message-ttl'             => ['I', 5000],
        ]);
        $channel->queue_declare($queueMain, false, true, false, false, false, [
            'x-dead-letter-exchange'    => ['S', ''],
            'x-dead-letter-routing-key' => ['S', $queueRetry],
        ]);
    
        $this->info('🏭 Esperando solicitudes de ingredientes...');
    
        $service = app(WarehouseService::class);
        $service->setCommand($this);

        $channel->basic_consume($queueMain, '', false, false, false, false, function (AMQPMessage $msg) use ($channel, $service) {  
            $service->processInventoryRequest($channel, $msg);
        });
    
        while ($channel->is_consuming()) {
            $channel->wait();
        }
    
        $channel->close();
        $connection->close();
    }
}
