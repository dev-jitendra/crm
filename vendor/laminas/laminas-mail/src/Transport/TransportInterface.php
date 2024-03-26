<?php

namespace Laminas\Mail\Transport;

use Laminas\Mail;


interface TransportInterface
{
    
    public function send(Mail\Message $message);
}
