<?php

declare(strict_types=1);

namespace Laminas\Stdlib;

use ArrayIterator;
use ArrayObject as PhpArrayObject;
use ReturnTypeWillChange;

use function array_reverse;


class ArrayStack extends PhpArrayObject
{
    
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        $array = $this->getArrayCopy();
        return new ArrayIterator(array_reverse($array));
    }
}
