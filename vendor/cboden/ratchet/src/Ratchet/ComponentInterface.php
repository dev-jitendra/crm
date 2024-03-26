<?php
namespace Ratchet;


interface ComponentInterface {
    
    function onOpen(ConnectionInterface $conn);

    
    function onClose(ConnectionInterface $conn);

    
    function onError(ConnectionInterface $conn, \Exception $e);
}
