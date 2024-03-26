<?php
namespace Ratchet\Wamp;
use Ratchet\ComponentInterface;
use Ratchet\ConnectionInterface;


interface WampServerInterface extends ComponentInterface {
    
    function onCall(ConnectionInterface $conn, $id, $topic, array $params);

    
    function onSubscribe(ConnectionInterface $conn, $topic);

    
    function onUnSubscribe(ConnectionInterface $conn, $topic);

    
    function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible);
}
