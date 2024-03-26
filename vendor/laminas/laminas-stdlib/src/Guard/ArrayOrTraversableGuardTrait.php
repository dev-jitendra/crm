<?php

declare(strict_types=1);

namespace Laminas\Stdlib\Guard;

use Exception;
use Laminas\Stdlib\Exception\InvalidArgumentException;
use Traversable;

use function get_debug_type;
use function is_array;
use function sprintf;


trait ArrayOrTraversableGuardTrait
{
    
    protected function guardForArrayOrTraversable(
        mixed $data,
        $dataName = 'Argument',
        $exceptionClass = InvalidArgumentException::class
    ) {
        if (! is_array($data) && ! $data instanceof Traversable) {
            $message = sprintf(
                "%s must be an array or Traversable, [%s] given",
                $dataName,
                get_debug_type($data)
            );
            throw new $exceptionClass($message);
        }
    }
}
