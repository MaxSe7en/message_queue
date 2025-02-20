<?php

namespace App\Services;

use RdKafka\Producer;

class KafkaProducer
{
    private Producer $producer;
    private string $topicName;

    public function __construct(string $topicName)
    {
        $this->producer = new Producer();
        $this->producer->addBrokers($_ENV['KAFKA_BROKER']);
        $this->topicName = $topicName;
    }

    public function sendMessage(string $message): void
    {
        $topic = $this->producer->newTopic($this->topicName);
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
        $this->producer->flush(1000);
    }
}
