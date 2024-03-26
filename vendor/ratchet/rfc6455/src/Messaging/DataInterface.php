<?php
namespace Ratchet\RFC6455\Messaging;

interface DataInterface {
    
    function isCoalesced();

    
    function getPayloadLength();

    
    function getPayload();

    
    function getContents();

    
    function __toString();
}
