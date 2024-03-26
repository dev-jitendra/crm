<?php


namespace Espo\Core\WebSocket;

use React\EventLoop\LoopInterface;


interface Subscriber
{
    
    public function subscribe(Pusher $pusher, LoopInterface $loop): void;
}
