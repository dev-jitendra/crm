<?php

declare(strict_types=1);

namespace Laminas\Stdlib\Guard;

use Exception;
use Laminas\Stdlib\Exception\InvalidArgumentException;

use function sprintf;


trait NullGuardTrait
{
    
    protected function guardAgainstNull(
        mixed $data,
        $dataName = 'Argument',
        $exceptionClass = InvalidArgumentException::class
    ) {
        if (null === $data) {
            $message = sprintf('%s cannot be null', $dataName);
            throw new $exceptionClass($message);
        }
    }
}
