<?php

declare(strict_types=1);

namespace Laminas\Stdlib\Guard;

use Exception;
use Laminas\Stdlib\Exception\InvalidArgumentException;

use function sprintf;


trait EmptyGuardTrait
{
    
    protected function guardAgainstEmpty(
        mixed $data,
        $dataName = 'Argument',
        $exceptionClass = InvalidArgumentException::class
    ) {
        if (empty($data)) {
            $message = sprintf('%s cannot be empty', $dataName);
            throw new $exceptionClass($message);
        }
    }
}
