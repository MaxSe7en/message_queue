<?php
require_once __DIR__ . '/vendor/autoload.php';
// require __DIR__ . '/WebSocketServer.php';

// use App\Services\WebSocketServer;
// use App\Services\KafkaConsumer;
// use React\EventLoop\Loop;
// use React\Socket\SocketServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;
header("Access-Control-Allow-Origin: *");
// Create WebSocket server
// $wsServer = new WebSocketServer();

// // Create socket server
// $socketServer = new SocketServer('0.0.0.0:8081');

// // Create HTTP server
// $httpServer = new HttpServer(new WsServer($wsServer));

// // Create IO server
// $webSocketServer = new IoServer($httpServer, $socketServer, Loop::get());

// // Create Kafka-WebSocket bridge
// $bridge = new KafkaConsumer('momo_sms_topic', $wsServer);
// // Add periodic timer to check for Kafka messages
// Loop::addPeriodicTimer(0.1, function () use ($bridge) {
//     print_r($bridge);
//     $bridge->processMessages();
// });
// // Start WebSocket server

// // use Ratchet\App;

// // $server = new App('localhost', 8080, '0.0.0.0');
// // $server->route('/ws', new WebSocketServer, ['*']);
// // echo "WebSocket server started on ws://localhost:8080/ws\n";
// // $server->run();

error_reporting(E_ALL);
ini_set('display_errors', 1);
use App\Services\WebSocketServer;
use App\Services\KafkaConsumer;
use React\EventLoop\Loop;
use React\Socket\SocketServer;
use App\Services\LoggerService;
// Create WebSocket server
$wsServer = new App\Services\WebSocketServer();

// Create socket server with the updated API
$socketServer = new SocketServer('0.0.0.0:8081');

// Create HTTP server
$httpServer = new HttpServer(
    new WsServer($wsServer)
);

// Create IO server
$webSocketServer = new IoServer(
    $httpServer,
    $socketServer,
    Loop::get()
);

// Create Kafka-WebSocket bridge
$bridge = new KafkaConsumer('momo_sms_topic', $wsServer);

// Add periodic timer to check for Kafka messages using the updated API
Loop::addPeriodicTimer(0.1, function () use ($bridge) {
    $bridge->processMessages();
});

echo "Server started. WebSocket on port 8081, listening to Kafka topic 'momo_sms_topic'\n";
LoggerService::logInfo("WebSocket started", ['message' => "Server started. WebSocket on port 8081, listening to Kafka topic 'momo_sms_topic'\n"]);
// Run the event loop (this is handled automatically now)
Loop::run();