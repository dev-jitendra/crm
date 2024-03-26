<?php

namespace RobThree\Auth\Providers\Time;


class NTPTimeProvider implements ITimeProvider
{
    public $host;
    public $port;
    public $timeout;

    function __construct($host = 'pool.ntp.org', $port = 123, $timeout = 1)
    {
        $this->host = $host;

        if (!is_int($port) || $port <= 0 || $port > 65535)
            throw new \TimeException('Port must be 0 < port < 65535');
        $this->port = $port;

        if (!is_int($timeout) || $timeout < 0)
            throw new \TimeException('Timeout must be >= 0');
        $this->timeout = $timeout;
    }

    public function getTime() {
        try {
            
            $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            socket_connect($sock, $this->host, $this->port);

            
            $msg = "\010" . str_repeat("\0", 47);
            socket_send($sock, $msg, strlen($msg), 0);

            
            socket_recv($sock, $recv, 48, MSG_WAITALL);
            socket_close($sock);

            
            $data = unpack('N12', $recv);
            $timestamp = sprintf('%u', $data[9]);

            
            return $timestamp - 2208988800;
        }
        catch (Exception $ex) {
            throw new \TimeException(sprintf('Unable to retrieve time from %s (%s)', $this->host, $ex->getMessage()));
        }
    }
}