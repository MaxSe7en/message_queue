<?php

namespace App\Services;

use RdKafka\Consumer;
use RdKafka\ConsumerTopic;
use RdKafka\Message;
use RdKafka\Conf;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class KafkaConsumer
{
    private Consumer $consumer;
    private WebSocketServer $wsServer;
    private string $topicName;
    private bool $running = true;
    private ConsumerTopic $topic;
    private Logger $logger;

    public function __construct(string $topicName, WebSocketServer $wsServer)
    {
        // Configure Kafka Consumer
        $conf = new Conf();
        $conf->set('metadata.broker.list', "172.18.0.4:9092"); // Set Kafka broker
        $conf->set('group.id', 'websocket_bridge_group'); // Set consumer group
        $conf->set('auto.offset.reset', 'latest'); // Read from the beginning if no offset exists

        // Create Consumer
        $this->consumer = new Consumer($conf);
        $this->topicName = $topicName;
        $this->wsServer = $wsServer;

        // Initialize the topic
        $this->topic = $this->consumer->newTopic($this->topicName);
        $this->topic->consumeStart(0, RD_KAFKA_OFFSET_END); // Start consuming from latest

    }


    public function processMessages(): void
    {
        $message = $this->topic->consume(0, 100); // Short timeout to avoid blocking event loop

        if ($message === null) {
            return;
        }

        if ($message->err === RD_KAFKA_RESP_ERR_NO_ERROR) {
            $data = json_decode($message->payload, true);
            // LoggerService::logInfo("Message received", [
            //     'resourceId' => $message->payload
            // ]);
            if (isset($data['message'])) {
                echo "Broadcasting message to channel:  momo_updates";
                
                // $this->logger->info("Kafka message received for channel {$data['channel']}: {$data['message']}");
                $this->wsServer->sendMessageToChannel('momo_updates', $message->payload);
            } else {
                echo "Invalid Kafka message format\n";
                // $this->logger->warning("Invalid Kafka message format: {$message->payload}");
            }
        } elseif ($message->err !== RD_KAFKA_RESP_ERR__TIMED_OUT) {
            echo "Kafka error: " . $message->errstr() . "\n";
            // $this->logger->error("Kafka error: " . $message->errstr());
        }

        // if ($message->err === RD_KAFKA_RESP_ERR_NO_ERROR) {
        //     echo "Broadcasting message: " . $message->payload . "\n";
        //     $this->wsServer->broadcast($message->payload);
        // } elseif (
        //     $message->err !== RD_KAFKA_RESP_ERR__TIMED_OUT &&
        //     $message->err !== RD_KAFKA_RESP_ERR__PARTITION_EOF
        // ) {
        //     echo "Kafka error: " . $message->errstr() . "\n";
        // }
    }

    public function start(): void
    {
        echo "KafkaWebSocketBridge started for topic: {$this->topicName}\n";

        while ($this->running) {
            $this->processMessages();
            // Small sleep to prevent CPU overuse
            usleep(10000); // 10ms
        }
    }

    public function stop(): void
    {
        $this->running = false;
    }
}
