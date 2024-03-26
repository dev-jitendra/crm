<?php

declare(strict_types=1);

namespace Laminas\Stdlib;

use Countable;
use IteratorAggregate;
use ReturnTypeWillChange;
use Serializable;
use UnexpectedValueException;

use function array_map;
use function count;
use function is_array;
use function serialize;
use function sprintf;
use function unserialize;


class PriorityQueue implements Countable, IteratorAggregate, Serializable
{
    public const EXTR_DATA     = 0x00000001;
    public const EXTR_PRIORITY = 0x00000002;
    public const EXTR_BOTH     = 0x00000003;

    
    protected $queueClass = SplPriorityQueue::class;

    
    protected $items = [];

    
    protected $queue;

    
    public function insert($data, $priority = 1)
    {
        
        $priority      = (int) $priority;
        $this->items[] = [
            'data'     => $data,
            'priority' => $priority,
        ];
        $this->getQueue()->insert($data, $priority);
        return $this;
    }

    
    public function remove(mixed $datum)
    {
        $found = false;
        $key   = null;
        foreach ($this->items as $key => $item) {
            if ($item['data'] === $datum) {
                $found = true;
                break;
            }
        }
        if ($found && $key !== null) {
            unset($this->items[$key]);
            $this->queue = null;

            if (! $this->isEmpty()) {
                $queue = $this->getQueue();
                foreach ($this->items as $item) {
                    $queue->insert($item['data'], $item['priority']);
                }
            }
            return true;
        }
        return false;
    }

    
    public function isEmpty()
    {
        return 0 === $this->count();
    }

    
    #[ReturnTypeWillChange]
    public function count()
    {
        return count($this->items);
    }

    
    public function top()
    {
        $queue = clone $this->getQueue();

        return $queue->top();
    }

    
    public function extract()
    {
        $value = $this->getQueue()->extract();

        $keyToRemove     = null;
        $highestPriority = null;
        foreach ($this->items as $key => $item) {
            if ($item['data'] !== $value) {
                continue;
            }

            if (null === $highestPriority) {
                $highestPriority = $item['priority'];
                $keyToRemove     = $key;
                continue;
            }

            if ($highestPriority >= $item['priority']) {
                continue;
            }

            $highestPriority = $item['priority'];
            $keyToRemove     = $key;
        }

        if ($keyToRemove !== null) {
            unset($this->items[$keyToRemove]);
        }

        return $value;
    }

    
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        $queue = $this->getQueue();
        return clone $queue;
    }

    
    public function serialize()
    {
        return serialize($this->__serialize());
    }

    
    public function __serialize()
    {
        return $this->items;
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
        foreach ($data as $item) {
            $this->insert($item['data'], $item['priority']);
        }
    }

    
    public function toArray($flag = self::EXTR_DATA)
    {
        return match ($flag) {
            self::EXTR_BOTH => $this->items,
            self::EXTR_PRIORITY => array_map(static fn($item): int => $item['priority'], $this->items),
            default => array_map(static fn($item): mixed => $item['data'], $this->items),
        };
    }

    
    public function setInternalQueueClass($class)
    {
        
        $this->queueClass = (string) $class;
        return $this;
    }

    
    public function contains($datum)
    {
        foreach ($this->items as $item) {
            if ($item['data'] === $datum) {
                return true;
            }
        }
        return false;
    }

    
    public function hasPriority($priority)
    {
        foreach ($this->items as $item) {
            if ($item['priority'] === $priority) {
                return true;
            }
        }
        return false;
    }

    
    protected function getQueue()
    {
        if (null === $this->queue) {
            
            $queue = new $this->queueClass();
            
            $this->queue = $queue;
            
            if (! $this->queue instanceof \SplPriorityQueue) {
                throw new Exception\DomainException(sprintf(
                    'PriorityQueue expects an internal queue of type SplPriorityQueue; received "%s"',
                    $this->queue::class
                ));
            }
        }

        return $this->queue;
    }

    
    public function __clone()
    {
        if (null !== $this->queue) {
            $this->queue = clone $this->queue;
        }
    }
}
