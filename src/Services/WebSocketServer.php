<?php
namespace App\Services;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class WebSocketServer implements MessageComponentInterface
{
    protected $clients;
    
    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        echo "WebSocket server started\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
        echo "New connection! ({$conn->resourceId}) from IP: {$conn->remoteAddress}\n";
    
        // Debug connection headers
        echo "Connection headers: " . print_r($conn->httpRequest->getHeaders(), true);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // We're not expecting messages from clients in this implementation
        // But you could implement client authentication or channel subscription here
        echo "Message received from {$from->resourceId}: {$msg}\n";
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    public function broadcast($message)
    {
        // Send message to all connected clients
        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }

    public static function run($port = 8080)
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new self()
                )
            ),
            $port
        );
        
        echo "WebSocket server running on port {$port}\n";
        $server->run();
    }
}