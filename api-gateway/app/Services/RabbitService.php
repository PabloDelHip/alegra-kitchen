<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitService
{
    public function publish(string $queue, array $data): void
    {
      $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
      $channel = $connection->channel();

      $channel->queue_declare($queue, false, true, false, false);

      $msg = new AMQPMessage(json_encode($data), [
          'content_type' => 'application/json',
          'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
      ]);

      $channel->basic_publish($msg, '', $queue);

      $channel->close();
      $connection->close();
    }
}
