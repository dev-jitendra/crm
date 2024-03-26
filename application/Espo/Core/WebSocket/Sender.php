<?php


namespace Espo\Core\WebSocket;


interface Sender
{
    
    public function send(string $message): void;
}
