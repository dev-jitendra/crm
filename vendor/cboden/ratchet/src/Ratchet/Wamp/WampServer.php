<?php
namespace Ratchet\Wamp;
use Ratchet\MessageComponentInterface;
use Ratchet\WebSocket\WsServerInterface;
use Ratchet\ConnectionInterface;


class WampServer implements MessageComponentInterface, WsServerInterface {
    
    protected $wampProtocol;

    
    public function __construct(WampServerInterface $app) {
        $this->wampProtocol = new ServerProtocol(new TopicManager($app));
    }

    
    public function onOpen(ConnectionInterface $conn) {
        $this->wampProtocol->onOpen($conn);
    }

    
    public function onMessage(ConnectionInterface $conn, $msg) {
        try {
            $this->wampProtocol->onMessage($conn, $msg);
        } catch (Exception $we) {
            $conn->close(1007);
        }
    }

    
    public function onClose(ConnectionInterface $conn) {
        $this->wampProtocol->onClose($conn);
    }

    
    public function onError(ConnectionInterface $conn, \Exception $e) {
        $this->wampProtocol->onError($conn, $e);
    }

    
    public function getSubProtocols() {
        return $this->wampProtocol->getSubProtocols();
    }
}
