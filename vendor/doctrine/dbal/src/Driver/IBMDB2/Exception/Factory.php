<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Driver\IBMDB2\Exception;

use Doctrine\DBAL\Driver\AbstractException;

use function preg_match;


final class Factory
{
    
    public static function create(string $message, callable $constructor): AbstractException
    {
        $code = 0;

        if (preg_match('/ SQL(\d+)N /', $message, $matches) === 1) {
            $code = -(int) $matches[1];
        }

        return $constructor($code);
    }
}
