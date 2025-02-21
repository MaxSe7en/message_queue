<?php

// require 'vendor/autoload.php';
require __DIR__ . '/vendor/autoload.php';
use App\Services\KafkaConsumer;

$consumer = new KafkaConsumer('my_topic');
$consumer->consume();
