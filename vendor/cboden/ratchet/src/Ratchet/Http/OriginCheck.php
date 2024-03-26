<?php
namespace Ratchet\Http;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Psr\Http\Message\RequestInterface;


class OriginCheck implements HttpServerInterface {
    use CloseResponseTrait;

    
    protected $_component;

    public $allowedOrigins = [];

    
    public function __construct(MessageComponentInterface $component, array $allowed = []) {
        $this->_component = $component;
        $this->allowedOrigins += $allowed;
    }

    
    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null) {
        $header = (string)$request->getHeader('Origin')[0];
        $origin = parse_url($header, PHP_URL_HOST) ?: $header;

        if (!in_array($origin, $this->allowedOrigins)) {
            return $this->close($conn, 403);
        }

        return $this->_component->onOpen($conn, $request);
    }

    
    function onMessage(ConnectionInterface $from, $msg) {
        return $this->_component->onMessage($from, $msg);
    }

    
    function onClose(ConnectionInterface $conn) {
        return $this->_component->onClose($conn);
    }

    
    function onError(ConnectionInterface $conn, \Exception $e) {
        return $this->_component->onError($conn, $e);
    }
}