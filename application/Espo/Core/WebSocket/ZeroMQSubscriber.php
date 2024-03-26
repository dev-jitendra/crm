<?php


namespace Espo\Core\WebSocket;

use Espo\Core\Utils\Config;

use React\EventLoop\LoopInterface;
use React\ZMQ\Context as ZMQContext;
use Evenement\EventEmitter;
use React\ZMQ\SocketWrapper;

use ZMQ;

class ZeroMQSubscriber implements Subscriber
{
    private const DSN = 'tcp:

    public function __construct(private Config $config)
    {}

    public function subscribe(Pusher $pusher, LoopInterface $loop): void
    {
        $dsn = $this->config->get('webSocketZeroMQSubscriberDsn') ?? self::DSN;

        $context = new ZMQContext($loop);

        
        
        $pull = $context->getSocket(ZMQ::SOCKET_PULL);

        $pull->bind($dsn);
        $pull->on('message', [$pusher, 'onMessageReceive']);
    }
}
