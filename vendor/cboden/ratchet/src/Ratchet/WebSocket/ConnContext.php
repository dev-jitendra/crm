<?php
namespace Ratchet\WebSocket;
use Ratchet\RFC6455\Messaging\MessageBuffer;

class ConnContext {
    
    public $connection;

    
    public $buffer;

    public function __construct(WsConnection $conn, MessageBuffer $buffer) {
        $this->connection = $conn;
        $this->buffer = $buffer;
    }
}
