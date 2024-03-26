<?php


namespace Espo\Core\WebSocket;

use Espo\Core\Utils\Config;

use ZMQContext;
use ZMQ;

class ZeroMQSender implements Sender
{
    private const DSN = 'tcp:

    public function __construct(private Config $config)
    {}

    public function send(string $message): void
    {
        $dsn = $this->config->get('webSocketZeroMQSubmissionDsn') ?? self::DSN;

        $context = new ZMQContext();

        $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');

        $socket->connect($dsn);
        $socket->send($message);
        $socket->setSockOpt(ZMQ::SOCKOPT_LINGER, 1000);
        $socket->disconnect($dsn);
    }
}
