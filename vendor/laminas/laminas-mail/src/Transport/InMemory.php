<?php

namespace Laminas\Mail\Transport;

use Laminas\Mail\Message;


class InMemory implements TransportInterface
{
    
    protected $lastMessage;

    
    public function send(Message $message)
    {
        $this->lastMessage = $message;
    }

    
    public function getLastMessage()
    {
        return $this->lastMessage;
    }
}
