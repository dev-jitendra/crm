<?php
namespace Ratchet;

interface MessageInterface {
    
    function onMessage(ConnectionInterface $from, $msg);
}
