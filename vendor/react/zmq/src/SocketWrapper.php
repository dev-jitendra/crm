<?php

namespace React\ZMQ;

use Evenement\EventEmitter;
use React\EventLoop\LoopInterface;
use ZMQ;
use ZMQSocket;


class SocketWrapper extends EventEmitter
{
    
    public $fileDescriptor;

    
    public $closed = false;

    
    protected $socket = null;

    
    protected $loop;

    
    protected $buffer = null;

    
    public function __construct(ZMQSocket $socket, LoopInterface $loop)
    {
        $this->socket = $socket;
        $this->loop = $loop;

        $this->fileDescriptor = $this->socket->getSockOpt(ZMQ::SOCKOPT_FD);

        $writeListener = array($this, 'handleEvent');

        $this->buffer = new Buffer($socket, $this->fileDescriptor, $this->loop, $writeListener);
    }

    public function attachReadListener()
    {
        $this->loop->addReadStream($this->fileDescriptor, array($this, 'handleEvent'));
    }

    public function handleEvent()
    {
        while ($this->socket !== null && $this->buffer !== null) {
            $events = $this->socket->getSockOpt(ZMQ::SOCKOPT_EVENTS);

            $isPollIn = $events & ZMQ::POLL_IN;
            $isPollOut = $events & ZMQ::POLL_OUT;

            $hasEvents = $isPollIn || ($isPollOut && $this->buffer->listening);

            if (!$hasEvents) {
                break;
            }

            if ($isPollIn) {
                $this->handleReadEvent();
            }

            if ($isPollOut && $this->buffer->listening) {
                $this->buffer->handleWriteEvent();
            }
        }
    }

    public function handleReadEvent()
    {
        $messages = $this->socket->recvmulti(ZMQ::MODE_NOBLOCK);

        if ($messages !== false) {
            if (count($messages) === 1) {
                $this->emit('message', array($messages[0]));
            }

            $this->emit('messages', array($messages));
        }
    }

    
    public function getWrappedSocket()
    {
        return $this->socket;
    }

    
    public function subscribe($channel)
    {
        $this->socket->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, $channel);
    }

    
    public function unsubscribe($channel)
    {
        $this->socket->setSockOpt(ZMQ::SOCKOPT_UNSUBSCRIBE, $channel);
    }

    
    public function send($message)
    {
        if($this->buffer !== null) {
            $this->buffer->send($message);
        }
    }

    public function close()
    {
        if ($this->closed) {
            return;
        }

        $this->emit('end', array($this));
        $this->loop->removeWriteStream($this->fileDescriptor);
        $this->loop->removeReadStream($this->fileDescriptor);
        $this->buffer->removeAllListeners();
        $this->removeAllListeners();
        $this->buffer = null;
        $this->socket = null;
        $this->closed = true;
    }

    public function end()
    {
        if ($this->closed) {
            return;
        }

        $that = $this;

        $this->buffer->on('end', function () use ($that) {
            $that->close();
        });

        $this->buffer->end();
    }

    
    public function __call($method, array $parameters)
    {
        return call_user_func_array(array($this->socket, $method), $parameters);
    }
}
