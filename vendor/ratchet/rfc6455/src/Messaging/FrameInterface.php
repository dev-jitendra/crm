<?php
namespace Ratchet\RFC6455\Messaging;

interface FrameInterface extends DataInterface {
    
    function addBuffer($buf);

    
    function isFinal();

    
    function isMasked();

    
    function getOpcode();

    
    

    
    function getMaskingKey();
}
