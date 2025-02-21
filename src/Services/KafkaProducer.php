<?php

namespace App\Services;

use RdKafka\Producer;
use RdKafka\Conf;
class KafkaProducer
{
    private Producer $producer;
    private string $topicName;

    public function __construct(string $topicName)
    {
        $conf = new Conf();
        $conf->set('metadata.broker.list', $_ENV['KAFKA_BROKER']); // Set broker
        $this->producer = new Producer($conf);
        $this->producer->addBrokers($_ENV['KAFKA_BROKER']);
        $this->topicName = $topicName;
    }

    public function sendMessage(string $message): void
    {
        $topic = $this->producer->newTopic($this->topicName);
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);

        // Flush to make sure the message is sent
        $result = $this->producer->flush(10000); // 10 seconds timeout

        if ($result !== RD_KAFKA_RESP_ERR_NO_ERROR) {
            throw new \RuntimeException('Failed to send Kafka message.');
        }
    }
}
