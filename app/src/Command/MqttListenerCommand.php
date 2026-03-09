<?php

namespace App\Command;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:mqtt-listener',
    description: 'Listen to MQTT messages via RabbitMQ AMQP and log them',
)]
class MqttListenerCommand extends Command
{
    public function __construct(
        private LoggerInterface $mqttLogger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info('Connecting to RabbitMQ...');

        $host = $_ENV['RABBITMQ_HOST'] ?? 'rabbitmq';
        $port = (int) ($_ENV['RABBITMQ_PORT'] ?? 5672);
        $user = $_ENV['RABBITMQ_USER'] ?? 'guest';
        $pass = $_ENV['RABBITMQ_PASS'] ?? 'guest';

        $connection = new AMQPStreamConnection($host, $port, $user, $pass);
        $channel = $connection->channel();

        // RabbitMQ MQTT plugin publishes to the 'amq.topic' exchange
        // with routing keys matching the MQTT topic (/ replaced by .)
        $exchange = 'amq.topic';
        $queueName = 'mqtt_listener';

        $channel->queue_declare($queueName, false, true, false, false);
        // Bind to all topics with wildcard '#'
        $channel->queue_bind($queueName, $exchange, '#');

        $io->success("Listening for MQTT messages on queue '$queueName' (bound to $exchange with '#')...");

        $callback = function (AMQPMessage $msg) use ($io): void {
            $routingKey = $msg->getRoutingKey();
            $body = $msg->getBody();
            $timestamp = date('Y-m-d H:i:s');

            $logEntry = sprintf('[%s] Topic: %s | Payload: %s', $timestamp, $routingKey, $body);

            $this->mqttLogger->info($logEntry);
            $io->writeln($logEntry);
        };

        $channel->basic_consume($queueName, '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();

        return Command::SUCCESS;
    }
}
