<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Driver\API;

use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Query;

interface ExceptionConverter
{
    
    public function convert(Exception $exception, ?Query $query): DriverException;
}
