<?php

namespace Laminas\Mail\Storage\Message;

interface MessageInterface
{
    
    public function getTopLines();

    
    public function hasFlag($flag);

    
    public function getFlags();
}
