<?php
namespace Ratchet\Session\Serialize;

interface HandlerInterface {
    
    function serialize(array $data);

    
    function unserialize($raw);
}
