<?php
namespace App\Services;

use App\Controllers\MomoMsgController;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class WebSocketServer implements MessageComponentInterface
{
    protected $clients;
    protected $channels;
    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        echo "WebSocket server started\n";
        LoggerService::logInfo("WebSocket started", ['message' => "Server started. 000000"]);

    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection
        $this->clients->attach($conn);
        LoggerService::logInfo("New WebSocket connection", [
            'resourceId' => $conn->resourceId,
            'ip' => $conn->remoteAddress,
        ]);
        echo "New connection! ({$conn->resourceId})\n";
        echo "New connection! ({$conn->resourceId}) from IP: {$conn->remoteAddress}\n";
    
        // Debug connection headers
        echo "Connection headers: " . print_r($conn->httpRequest->getHeaders(), true);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        echo "Message received from {$from->resourceId}: {$msg}\n";
        LoggerService::logInfo("onMessage received from", [
            'resourceId' => $from->resourceId,
            'message' => $msg,
        ]);
        print_r($data);
        if ($data['type'] === 'subscribe' && isset($data['channel'])) {
            $this->subscribeToChannel($from, $data['channel']);
            
            // Send acknowledgment back to the client
            $from->send(json_encode([
                "type" => "confirmation",
                "channel" => $data['channel'],
                "message" => "Subscription successful"
            ]));
        }
    
        // if (!$data || !isset($data['type'])) {
        //     return;
        // }

        // switch ($data['type']) {
        //     case 'subscribe':
        //         $this->subscribeToChannel($from, $data['channel']);
        //         break;

        //     case 'message':
        //         $this->sendMessageToChannel($data['channel'], $data['message']);
        //         break;

        //     case 'ack':
        //         (new MomoMsgController())->updateMessageStatus($data['message_id']);
        //         break;
        // }
    }

    private function subscribeToChannel(ConnectionInterface $conn, string $channel)
    {
        if (!isset($this->channels[$channel])) {
            $this->channels[$channel] = [];
        }

        $this->channels[$channel][$conn->resourceId] = $conn;
        echo "Client {$conn->resourceId} subscribed to channel: $channel\n";
        LoggerService::logInfo("subscribeToChannel", ['message' => $channel]);

    }

    public function sendMessageToChannel(string $channel, string $message)
    {
        if (!isset($this->channels[$channel])) {
            echo "No clients subscribed to channel: $channel\n";
            return;
        }

        foreach ($this->channels[$channel] as $client) {
            $client->send(json_encode([
                'type' => $channel,
                'message' => $message
            ]));
        }
    }



    private function removeClientFromChannels(ConnectionInterface $conn)
    {
        foreach ($this->channels as $channel => &$clients) {
            unset($clients[$conn->resourceId]);
            if (empty($clients)) {
                unset($this->channels[$channel]);
            }
        }
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
        LoggerService::logError("WebSocket error", ['message' => $e->getMessage()]);
        $conn->close();
    }

    public function broadcast($channel, $message)
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