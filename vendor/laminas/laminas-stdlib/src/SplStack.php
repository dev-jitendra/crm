<?php

declare(strict_types=1);

namespace Laminas\Stdlib;

use ReturnTypeWillChange;
use Serializable;
use UnexpectedValueException;

use function is_array;
use function serialize;
use function sprintf;
use function unserialize;


class SplStack extends \SplStack implements Serializable
{
    
    public function toArray()
    {
        $array = [];
        foreach ($this as $item) {
            $array[] = $item;
        }
        return $array;
    }

    
    #[ReturnTypeWillChange]
    public function serialize()
    {
        return serialize($this->__serialize());
    }

    
    #[ReturnTypeWillChange]
    public function __serialize()
    {
        return $this->toArray();
    }

    
    #[ReturnTypeWillChange]
    public function unserialize($data)
    {
        $toUnserialize = unserialize($data);
        if (! is_array($toUnserialize)) {
            throw new UnexpectedValueException(sprintf(
                'Cannot deserialize %s instance; corrupt serialization data',
                self::class
            ));
        }

        $this->__unserialize($toUnserialize);
    }

   
    #[ReturnTypeWillChange]
    public function __unserialize($data)
    {
        foreach ($data as $item) {
            $this->unshift($item);
        }
    }
}
