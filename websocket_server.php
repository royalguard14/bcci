<?php
require dirname(__DIR__) . '/BCCI/vendor/autoload.php'; // Load Composer dependencies
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class ChatServer implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // New connection, attach it to the clients list
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onClose(ConnectionInterface $conn) {
        // Connection closed, detach it from the clients list
        $this->clients->detach($conn);
        echo "Connection closed! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Broadcast message to all clients except the sender
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg); // Send message to other clients
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        // Handle any errors
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}

// Set up the WebSocket server on port 8080
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer()
        )
    ),
    8080 // WebSocket server port
);

echo "WebSocket server started...\n";
$server->run();
