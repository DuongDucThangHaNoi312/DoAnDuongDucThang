<?php

namespace App\Helper;

class HLog {
    static function write($type = \Monolog\Logger::INFO, $message) {
        $connection = new \PhpAmqpLib\Connection\AMQPSocketConnection(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_LOGIN'), env('RABBITMQ_PASSWORD'), env('RABBITMQ_VHOST_LOG'));
        $channel = $connection->channel();
        $logger = new \Monolog\Logger('main', [new \Monolog\Handler\AmqpHandler($channel, 'logs')]);
        $logger->log($type, $message);
        $channel->close();
        $connection->close();
        // \Log::info(__METHOD__);
    }

    private $channel;
    private $connection;
    private $logger;

    public function __construct()
    {
        $this->connection = new \PhpAmqpLib\Connection\AMQPSocketConnection(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_LOGIN'), env('RABBITMQ_PASSWORD'), env('RABBITMQ_VHOST_LOG'));
        $this->channel = $this->connection->channel();
        $this->logger = new \Monolog\Logger('main', [new \Monolog\Handler\AmqpHandler($this->channel, 'logs')]);
        // \Log::info(__METHOD__);
    }

    public function __destruct()
    {
        // $this->logger->log(\Monolog\Logger::NOTICE, __METHOD__ . ': thay viec close() channel va connection');
        $this->channel->close();
        $this->connection->close();
        // \Log::info(__METHOD__);
    }

    public function log($type = \Monolog\Logger::INFO, $message) {
        $this->logger->log($type, $message);
        // \Log::info(__METHOD__);
    }
}

?>