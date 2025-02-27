<?php
require __DIR__ . '/vendor/autoload.php';
use App\Services\KafkaProducer;

$producer = new KafkaProducer('my_topic');
$producer->sendMessage('Test message at ' . date('Y-m-d H:i:s'));
echo "Message sent!\n";