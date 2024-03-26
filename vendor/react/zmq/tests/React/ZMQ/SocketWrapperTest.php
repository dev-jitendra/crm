<?php

namespace React\ZMQ;

use PHPUnit\Framework\TestCase;
use ZMQ;

class SocketWrapperTest extends TestCase
{
    
    public function itShouldWrapARealZMQSocket()
    {
        $loop = $this->getMockBuilder('React\EventLoop\LoopInterface')->getMock();

        $socket = $this->getMockBuilder('ZMQSocket')
            ->disableOriginalConstructor()
            ->getMock();

        $socket
            ->expects($this->once())
            ->method('connect')
            ->with('tcp:

        $wrapped = new SocketWrapper($socket, $loop);

        $wrapped->connect('tcp:
    }

    
    public function subscribeShouldSetSocketOption()
    {
        $loop = $this->getMockBuilder('React\EventLoop\LoopInterface')->getMock();

        $socket = $this->getMockBuilder('ZMQSocket')
            ->disableOriginalConstructor()
            ->getMock();

        $socket
            ->expects($this->once())
            ->method('setSockOpt')
            ->with(ZMQ::SOCKOPT_SUBSCRIBE, 'foo');

        $wrapped = new SocketWrapper($socket, $loop);

        $wrapped->subscribe('foo');
    }

    
    public function unsubscribeShouldSetSocketOption()
    {
        $loop = $this->getMockBuilder('React\EventLoop\LoopInterface')->getMock();

        $socket = $this->getMockBuilder('ZMQSocket')
            ->disableOriginalConstructor()
            ->getMock();

        $socket
            ->expects($this->once())
            ->method('setSockOpt')
            ->with(ZMQ::SOCKOPT_UNSUBSCRIBE, 'foo');

        $wrapped = new SocketWrapper($socket, $loop);

        $wrapped->unsubscribe('foo');
    }

    
    public function sendShouldBufferMessages()
    {
        $loop = $this->getMockBuilder('React\EventLoop\LoopInterface')->getMock();

        $loop
            ->expects($this->once())
            ->method('addWriteStream')
            ->with(14);

        $socket = $this->getMockBuilder('ZMQSocket')
            ->disableOriginalConstructor()
            ->getMock();

        $socket
            ->expects($this->any())
            ->method('getSockOpt')
            ->with(ZMQ::SOCKOPT_FD)
            ->will($this->returnValue(14));

        $wrapped = new SocketWrapper($socket, $loop);

        $wrapped->send('foobar');
    }

    
    public function closeShouldStopListening()
    {
        $loop = $this->getMockBuilder('React\EventLoop\LoopInterface')->getMock();

        $loop
            ->expects($this->once())
            ->method('removeReadStream')
            ->with(14);

        $loop
            ->expects($this->once())
            ->method('removeWriteStream')
            ->with(14);

        $socket = $this->getMockBuilder('ZMQSocket')
            ->disableOriginalConstructor()
            ->getMock();

        $socket
            ->expects($this->any())
            ->method('getSockOpt')
            ->with(ZMQ::SOCKOPT_FD)
            ->will($this->returnValue(14));

        $wrapped = new SocketWrapper($socket, $loop);

        $wrapped->close();
    }


    
    public function closeShouldStopHandleEventLoop()
    {

        $loop = $this->getMockBuilder('React\EventLoop\LoopInterface')->getMock();

        $socket = $this->getMockBuilder('ZMQSocket')
            ->disableOriginalConstructor()
            ->getMock();

        $socket
            ->expects($this->exactly(1))
            ->method('getSockOpt');

        $wrapped = new SocketWrapper($socket, $loop);

        $wrapped->close();
        $wrapped->handleEvent();
    }
}
