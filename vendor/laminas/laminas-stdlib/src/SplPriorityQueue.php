<?php

declare(strict_types=1);

namespace Laminas\Stdlib;

use Serializable;
use UnexpectedValueException;

use function array_key_exists;
use function get_debug_type;
use function is_array;
use function serialize;
use function sprintf;
use function unserialize;

use const PHP_INT_MAX;


class SplPriorityQueue extends \SplPriorityQueue implements Serializable
{
    
    protected $serial = PHP_INT_MAX;

    
    public function insert($datum, $priority)
    {
        if (! is_array($priority)) {
            $priority = [$priority, $this->serial--];
        }

        parent::insert($datum, $priority);
    }

    
    public function toArray()
    {
        $array = [];
        foreach (clone $this as $item) {
            $array[] = $item;
        }
        return $array;
    }

    
    public function serialize()
    {
        return serialize($this->__serialize());
    }

    
    public function __serialize()
    {
        $clone = clone $this;
        $clone->setExtractFlags(self::EXTR_BOTH);

        $data = [];
        foreach ($clone as $item) {
            $data[] = $item;
        }
        return $data;
    }

    
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

    
    public function __unserialize($data)
    {
        $this->serial = PHP_INT_MAX;

        foreach ($data as $item) {
            if (! is_array($item)) {
                throw new UnexpectedValueException(sprintf(
                    'Cannot deserialize %s instance: corrupt item; expected array, received %s',
                    self::class,
                    get_debug_type($item)
                ));
            }

            if (! array_key_exists('data', $item)) {
                throw new UnexpectedValueException(sprintf(
                    'Cannot deserialize %s instance: corrupt item; missing "data" element',
                    self::class
                ));
            }

            $priority = 1;
            if (array_key_exists('priority', $item)) {
                $priority = (int) $item['priority'];
            }

            $this->insert($item['data'], $priority);
        }
    }
}
