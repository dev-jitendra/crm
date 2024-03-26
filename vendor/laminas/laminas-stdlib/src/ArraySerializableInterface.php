<?php

declare(strict_types=1);

namespace Laminas\Stdlib;

interface ArraySerializableInterface
{
    
    public function exchangeArray(array $array);

    
    public function getArrayCopy();
}
