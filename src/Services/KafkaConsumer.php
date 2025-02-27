<?php

namespace App\Services;

use RdKafka\Consumer;
use RdKafka\ConsumerTopic;
use RdKafka\Message;
use RdKafka\Conf;

class KafkaConsumer
{
    private Consumer $consumer;
    private WebSocketServer $wsServer;
    private string $topicName;
    private bool $running = true;
    private ConsumerTopic $topic;

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

    // public function consume(): void
    // {
    //     $topic = $this->consumer->newTopic($this->topicName);
    //     $topic->consumeStart(0, RD_KAFKA_OFFSET_END); // Start consuming from the latest message

    //     echo "Listening for messages on topic: {$this->topicName}...\n";

    //     while ($this->running) {
    //         $message = $topic->consume(0, 1000); // Partition 0, timeout 1000ms
    //         if ($message === null) {
    //             echo "Received null message, waiting...\n";
    //             continue;
    //         }
    //         print_r("message ========> " . json_encode($message));
    //         switch ($message->err) {
    //             case RD_KAFKA_RESP_ERR_NO_ERROR:
    //                 echo "Message received: " . $message->payload . "\n";
    //                 echo "Broadcasting message: " . $message->payload . "\n";
    //                 $this->wsServer->broadcast($message->payload);
    //                 break;
    //             case RD_KAFKA_RESP_ERR__PARTITION_EOF:
    //                 echo "No more messages; waiting for new ones...\n";
    //                 break;
    //             case RD_KAFKA_RESP_ERR__TIMED_OUT:
    //                 echo "Waiting for messages...\n";
    //                 break;
    //             default:
    //                 echo "Error: " . $message->errstr() . "\n";
    //                 break;
    //         }
    //     }
    // }

    
    public function processMessages(): void
    {
        $message = $this->topic->consume(0, 100); // Short timeout to avoid blocking event loop
        
        if ($message === null) {
            return;
        }
        
        if ($message->err === RD_KAFKA_RESP_ERR_NO_ERROR) {
            echo "Broadcasting message: " . $message->payload . "\n";
            $this->wsServer->broadcast($message->payload);
        } elseif ($message->err !== RD_KAFKA_RESP_ERR__TIMED_OUT && 
                  $message->err !== RD_KAFKA_RESP_ERR__PARTITION_EOF) {
            echo "Kafka error: " . $message->errstr() . "\n";
        }
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
