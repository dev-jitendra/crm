<?php
namespace Ratchet\Http;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Psr\Http\Message\RequestInterface;

interface HttpServerInterface extends MessageComponentInterface {
    
    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null);
}
