<?php
namespace Ratchet\Server;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class IpBlackList implements MessageComponentInterface {
    
    protected $_blacklist = array();

    
    protected $_decorating;

    
    public function __construct(MessageComponentInterface $component) {
        $this->_decorating = $component;
    }

    
    public function blockAddress($ip) {
        $this->_blacklist[$ip] = true;

        return $this;
    }

    
    public function unblockAddress($ip) {
        if (isset($this->_blacklist[$this->filterAddress($ip)])) {
            unset($this->_blacklist[$this->filterAddress($ip)]);
        }

        return $this;
    }

    
    public function isBlocked($address) {
        return (isset($this->_blacklist[$this->filterAddress($address)]));
    }

    
    public function getBlockedAddresses() {
        return array_keys($this->_blacklist);
    }

    
    public function filterAddress($address) {
        if (strstr($address, ':') && substr_count($address, '.') == 3) {
            list($address, $port) = explode(':', $address);
        }

        return $address;
    }

    
    function onOpen(ConnectionInterface $conn) {
        if ($this->isBlocked($conn->remoteAddress)) {
            return $conn->close();
        }

        return $this->_decorating->onOpen($conn);
    }

    
    function onMessage(ConnectionInterface $from, $msg) {
        return $this->_decorating->onMessage($from, $msg);
    }

    
    function onClose(ConnectionInterface $conn) {
        if (!$this->isBlocked($conn->remoteAddress)) {
            $this->_decorating->onClose($conn);
        }
    }

    
    function onError(ConnectionInterface $conn, \Exception $e) {
        if (!$this->isBlocked($conn->remoteAddress)) {
            $this->_decorating->onError($conn, $e);
        }
    }
}
