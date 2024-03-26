<?php

namespace Doctrine\DBAL\Exception;

use Doctrine\DBAL\Driver\Exception as TheDriverException;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query;

use function assert;


class DriverException extends Exception implements TheDriverException
{
    
    private ?Query $query;

    
    public function __construct(TheDriverException $driverException, ?Query $query)
    {
        if ($query !== null) {
            $message = 'An exception occurred while executing a query: ' . $driverException->getMessage();
        } else {
            $message = 'An exception occurred in the driver: ' . $driverException->getMessage();
        }

        parent::__construct($message, $driverException->getCode(), $driverException);

        $this->query = $query;
    }

    
    public function getSQLState()
    {
        $previous = $this->getPrevious();
        assert($previous instanceof TheDriverException);

        return $previous->getSQLState();
    }

    public function getQuery(): ?Query
    {
        return $this->query;
    }
}
