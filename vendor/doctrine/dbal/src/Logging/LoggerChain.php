<?php

namespace Doctrine\DBAL\Logging;

use Doctrine\Deprecations\Deprecation;


class LoggerChain implements SQLLogger
{
    
    private iterable $loggers;

    
    public function __construct(iterable $loggers = [])
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            'LoggerChain is deprecated',
        );

        $this->loggers = $loggers;
    }

    
    public function startQuery($sql, ?array $params = null, ?array $types = null)
    {
        foreach ($this->loggers as $logger) {
            $logger->startQuery($sql, $params, $types);
        }
    }

    
    public function stopQuery()
    {
        foreach ($this->loggers as $logger) {
            $logger->stopQuery();
        }
    }
}
