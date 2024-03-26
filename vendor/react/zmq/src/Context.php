<?php

namespace React\ZMQ;

use React\EventLoop\LoopInterface;
use ZMQ;
use ZMQContext;
use ZMQSocket;


class Context
{
    
    protected $loop;

    
    protected $context;

    
    public function __construct(LoopInterface $loop, ZMQContext $context = null)
    {
        $this->loop = $loop;

        if (!$context) {
            $context = new ZMQContext();
        }

        $this->context = $context;
    }

    
    public function __call($method, array $parameters)
    {
        $result = call_user_func_array(array($this->context, $method), $parameters);

        if ($result instanceof ZMQSocket) {
            $result = $this->wrapSocket($result);
        }

        return $result;
    }

    
    protected function wrapSocket(ZMQSocket $socket)
    {
        $wrapped = new SocketWrapper($socket, $this->loop);

        if ($this->isReadableSocketType($socket->getSocketType())) {
            $wrapped->attachReadListener();
        }

        return $wrapped;
    }

    
    protected function isReadableSocketType($type)
    {
        $readableTypes = array(
            ZMQ::SOCKET_PULL,
            ZMQ::SOCKET_SUB,
            ZMQ::SOCKET_REQ,
            ZMQ::SOCKET_REP,
            ZMQ::SOCKET_ROUTER,
            ZMQ::SOCKET_DEALER,
        );

        return in_array($type, $readableTypes);
    }
}
