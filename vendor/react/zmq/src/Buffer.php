<?php

namespace React\ZMQ;

use Evenement\EventEmitter;
use React\EventLoop\LoopInterface;
use ZMQ;
use ZMQSocket;
use ZMQSocketException;

class Buffer extends EventEmitter
{
    
    public $socket;

    
    public $closed = false;

    
    public $listening = false;

    
    protected $loop;

    
    protected $fileDescriptor;

    
    protected $writeListener;

    
    protected $messages = array();

    
    public function __construct(ZMQSocket $socket, $fileDescriptor, LoopInterface $loop, callable $writeListener)
    {
        $this->socket = $socket;
        $this->fileDescriptor = $fileDescriptor;
        $this->loop = $loop;
        $this->writeListener = $writeListener;
    }

    
    public function send($message)
    {
        if ($this->closed) {
            return;
        }

        $this->messages[] = $message;

        if (!$this->listening) {
            $this->listening = true;
            $this->loop->addWriteStream($this->fileDescriptor, $this->writeListener);
        }
    }

    public function end()
    {
        $this->closed = true;

        if (!$this->listening) {
            $this->emit('end');
        }
    }

    public function handleWriteEvent()
    {
        foreach ($this->messages as $i => $message) {
            try {
                if (!is_array($message)) {
                    $message = array($message);
                }

                $sent = (bool) $this->socket->sendmulti($message, ZMQ::MODE_NOBLOCK);

                if ($sent) {
                    unset($this->messages[$i]);

                    if (0 === count($this->messages)) {
                        $this->loop->removeWriteStream($this->fileDescriptor);
                        $this->listening = false;
                        $this->emit('end');
                    }
                }
            } catch (ZMQSocketException $e) {
                $this->emit('error', array($e));
            }
        }
    }
}
