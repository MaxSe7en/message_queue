<?php

namespace App\Services;

use RdKafka\Consumer;
use RdKafka\ConsumerTopic;
use RdKafka\Message;
use RdKafka\Conf;

class KafkaConsumer
{
    private Consumer $consumer;
    private string $topicName;

    public function __construct(string $topicName)
    {
        // Configure Kafka Consumer
        $conf = new Conf();
        $conf->set('metadata.broker.list', "172.18.0.4:9092"); // Set Kafka broker
        $conf->set('group.id', 'my_consumer_group'); // Set consumer group
        $conf->set('auto.offset.reset', 'earliest'); // Read from the beginning if no offset exists

        // Create Consumer
        $this->consumer = new Consumer($conf);
        $this->topicName = $topicName;
    }

    public function consume(): void
    {
        $topic = $this->consumer->newTopic($this->topicName);
        $topic->consumeStart(0, RD_KAFKA_OFFSET_END); // Start consuming from the latest message

        echo "Listening for messages on topic: {$this->topicName}...\n";

        while (true) {
            $message = $topic->consume(0, 1000); // Partition 0, timeout 1000ms
            if ($message === null) {
                echo "Received null message, waiting...\n";
                continue;
            }
            print_r("message ========> ".json_encode($message));
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    echo "Message received: " . $message->payload . "\n";
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; waiting for new ones...\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Waiting for messages...\n";
                    break;
                default:
                    echo "Error: " . $message->errstr() . "\n";
                    break;
            }
        }
    }
}
