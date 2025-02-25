<?php
require __DIR__ . '/vendor/autoload.php';
use App\Services\KafkaConsumer;

$consumer = new KafkaConsumer('momo_sms_topic');
$consumer->consume();