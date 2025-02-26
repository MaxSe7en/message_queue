<?php
// simple_ws_test.php
require_once __DIR__ . '/vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\EventLoop\Loop;
use React\Socket\SocketServer;

class TestWebSocketServer implements MessageComponentInterface {
    public function onOpen(ConnectionInterface $conn) {
        echo "New connection! ({$conn->resourceId})\n";
        $conn->send('{"status":"connected"}');
    }
    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Message: $msg\n";
        $from->send('{"echo":"' . $msg . '"}');
    }
    public function onClose(ConnectionInterface $conn) {
        echo "Connection {$conn->resourceId} has disconnected\n";
    }
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = new TestWebSocketServer();
$socket = new SocketServer('0.0.0.0:8080');
$ioServer = new IoServer(
    new HttpServer(
        new WsServer($server)
    ),
    $socket,
    Loop::get()
);

echo "Simple WebSocket test server running on 0.0.0.0:8080\n";
Loop::run();