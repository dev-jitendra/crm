<?php

namespace Laminas\Mail\Transport;

use Laminas\Mail\Exception;
use Laminas\Mail\Exception\InvalidArgumentException;
use Laminas\Stdlib\AbstractOptions;

use function gettype;
use function is_object;
use function is_string;
use function sprintf;


class SmtpOptions extends AbstractOptions
{
    
    protected $name = 'localhost';

    
    protected $connectionClass = 'smtp';

    
    protected $connectionConfig = [];

    
    protected $host = '127.0.0.1';

    
    protected $port = 25;

    
    protected $connectionTimeLimit;

    
    public function getName()
    {
        return $this->name;
    }

    
    public function setName($name)
    {
        if (! is_string($name) && $name !== null) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Name must be a string or null; argument of type "%s" provided',
                is_object($name) ? $name::class : gettype($name)
            ));
        }
        $this->name = $name;
        return $this;
    }

    
    public function getConnectionClass()
    {
        return $this->connectionClass;
    }

    
    public function setConnectionClass($connectionClass)
    {
        if (! is_string($connectionClass) && $connectionClass !== null) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Connection class must be a string or null; argument of type "%s" provided',
                is_object($connectionClass) ? $connectionClass::class : gettype($connectionClass)
            ));
        }
        $this->connectionClass = $connectionClass;
        return $this;
    }

    
    public function getConnectionConfig()
    {
        return $this->connectionConfig;
    }

    
    public function setConnectionConfig(array $connectionConfig)
    {
        $this->connectionConfig = $connectionConfig;
        return $this;
    }

    
    public function getHost()
    {
        return $this->host;
    }

    
    public function setHost($host)
    {
        $this->host = (string) $host;
        return $this;
    }

    
    public function getPort()
    {
        return $this->port;
    }

    
    public function setPort($port)
    {
        $port = (int) $port;
        if ($port < 1) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Port must be greater than 1; received "%d"',
                $port
            ));
        }
        $this->port = $port;
        return $this;
    }

    
    public function getConnectionTimeLimit()
    {
        return $this->connectionTimeLimit;
    }

    
    public function setConnectionTimeLimit($seconds)
    {
        $this->connectionTimeLimit = $seconds === null
            ? null
            : (int) $seconds;

        return $this;
    }
}
