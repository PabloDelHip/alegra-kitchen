<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class WarehousePublisher
{
    protected $channel;
    protected $connection;
    protected bool $isConnected = false;

    public function __construct()
    {
        $host = env('RABBITMQ_HOST', 'rabbitmq');
        $port = env('RABBITMQ_PORT', 5672);
        $user = env('RABBITMQ_USER', 'guest');
        $password = env('RABBITMQ_PASSWORD', 'guest');

        try {
            $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
            $this->channel = $this->connection->channel();

            $this->isConnected = true;
        } catch (Exception $e) {
            Log::error('❌ Error al conectar con RabbitMQ: ' . $e->getMessage());
            $this->isConnected = false;
        }
    }

    public function sendToWarehouse(array $payload): void
    {
        if (! $this->isConnected) {
            Log::warning('📭 No se pudo enviar mensaje: conexión con RabbitMQ no disponible.');
            return;
        }

        try {
            $queue = env('RABBITMQ_QUEUE_WAREHOUSE', 'warehouse_check_inventories');

            $msg = new AMQPMessage(json_encode($payload), [
                'content_type' => 'application/json',
                'delivery_mode' => 2,
            ]);

            $this->channel->basic_publish($msg, '', $queue);

            Log::info("📤 Mensaje enviado a {$queue}");
        } catch (Exception $e) {
            Log::error('❌ Error al enviar mensaje a RabbitMQ: ' . $e->getMessage());
        }
    }

    public function __destruct()
    {
        if ($this->isConnected) {
            $this->channel?->close();
            $this->connection?->close();
        }
    }
}
