<?php
namespace Ratchet\Wamp;
use Ratchet\MessageComponentInterface;
use Ratchet\WebSocket\WsServerInterface;
use Ratchet\ConnectionInterface;


class ServerProtocol implements MessageComponentInterface, WsServerInterface {
    const MSG_WELCOME     = 0;
    const MSG_PREFIX      = 1;
    const MSG_CALL        = 2;
    const MSG_CALL_RESULT = 3;
    const MSG_CALL_ERROR  = 4;
    const MSG_SUBSCRIBE   = 5;
    const MSG_UNSUBSCRIBE = 6;
    const MSG_PUBLISH     = 7;
    const MSG_EVENT       = 8;

    
    protected $_decorating;

    
    protected $connections;

    
    public function __construct(WampServerInterface $serverComponent) {
        $this->_decorating = $serverComponent;
        $this->connections = new \SplObjectStorage;
    }

    
    public function getSubProtocols() {
        if ($this->_decorating instanceof WsServerInterface) {
            $subs   = $this->_decorating->getSubProtocols();
            $subs[] = 'wamp';

            return $subs;
        }

        return ['wamp'];
    }

    
    public function onOpen(ConnectionInterface $conn) {
        $decor = new WampConnection($conn);
        $this->connections->attach($conn, $decor);

        $this->_decorating->onOpen($decor);
    }

    
    public function onMessage(ConnectionInterface $from, $msg) {
        $from = $this->connections[$from];

        if (null === ($json = @json_decode($msg, true))) {
            throw new JsonException;
        }

        if (!is_array($json) || $json !== array_values($json)) {
            throw new Exception("Invalid WAMP message format");
        }

        if (isset($json[1]) && !(is_string($json[1]) || is_numeric($json[1]))) {
            throw new Exception('Invalid Topic, must be a string');
        }

        switch ($json[0]) {
            case static::MSG_PREFIX:
                $from->WAMP->prefixes[$json[1]] = $json[2];
            break;

            case static::MSG_CALL:
                array_shift($json);
                $callID  = array_shift($json);
                $procURI = array_shift($json);

                if (count($json) == 1 && is_array($json[0])) {
                    $json = $json[0];
                }

                $this->_decorating->onCall($from, $callID, $from->getUri($procURI), $json);
            break;

            case static::MSG_SUBSCRIBE:
                $this->_decorating->onSubscribe($from, $from->getUri($json[1]));
            break;

            case static::MSG_UNSUBSCRIBE:
                $this->_decorating->onUnSubscribe($from, $from->getUri($json[1]));
            break;

            case static::MSG_PUBLISH:
                $exclude  = (array_key_exists(3, $json) ? $json[3] : null);
                if (!is_array($exclude)) {
                    if (true === (boolean)$exclude) {
                        $exclude = [$from->WAMP->sessionId];
                    } else {
                        $exclude = [];
                    }
                }

                $eligible = (array_key_exists(4, $json) ? $json[4] : []);

                $this->_decorating->onPublish($from, $from->getUri($json[1]), $json[2], $exclude, $eligible);
            break;

            default:
                throw new Exception('Invalid WAMP message type');
        }
    }

    
    public function onClose(ConnectionInterface $conn) {
        $decor = $this->connections[$conn];
        $this->connections->detach($conn);

        $this->_decorating->onClose($decor);
    }

    
    public function onError(ConnectionInterface $conn, \Exception $e) {
        return $this->_decorating->onError($this->connections[$conn], $e);
    }
}
