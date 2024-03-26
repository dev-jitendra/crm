<?php
namespace Ratchet\RFC6455\Messaging;

interface MessageInterface extends DataInterface, \Traversable, \Countable {
    
    function addFrame(FrameInterface $fragment);

    
    function getOpcode();

    
    function isBinary();
}
