<?php

declare(strict_types=1);

namespace Laminas\Stdlib;

use Countable;
use Iterator;
use ReturnTypeWillChange;
use Serializable;
use SplPriorityQueue as PhpSplPriorityQueue;
use UnexpectedValueException;

use function current;
use function in_array;
use function is_array;
use function is_int;
use function key;
use function max;
use function next;
use function reset;
use function serialize;
use function sprintf;
use function unserialize;


class FastPriorityQueue implements Iterator, Countable, Serializable
{
    public const EXTR_DATA     = PhpSplPriorityQueue::EXTR_DATA;
    public const EXTR_PRIORITY = PhpSplPriorityQueue::EXTR_PRIORITY;
    public const EXTR_BOTH     = PhpSplPriorityQueue::EXTR_BOTH;

    
    protected $extractFlag = self::EXTR_DATA;

    
    protected $values = [];

    
    protected $priorities = [];

    
    protected $subPriorities = [];

    
    protected $maxPriority;

    
    protected $count = 0;

    
    protected $index = 0;

    
    protected $subIndex = 0;

    public function __serialize(): array
    {
        $clone = clone $this;
        $clone->setExtractFlags(self::EXTR_BOTH);

        $data = [];
        foreach ($clone as $item) {
            $data[] = $item;
        }

        return $data;
    }

    public function __unserialize(array $data): void
    {
        foreach ($data as $item) {
            $this->insert($item['data'], $item['priority']);
        }
    }

    
    public function insert(mixed $value, $priority)
    {
        if (! is_int($priority)) {
            throw new Exception\InvalidArgumentException('The priority must be an integer');
        }
        $this->values[$priority][] = $value;
        if (! isset($this->priorities[$priority])) {
            $this->priorities[$priority] = $priority;
            $this->maxPriority           = $this->maxPriority === null ? $priority : max($priority, $this->maxPriority);
        }
        ++$this->count;
    }

    
    public function extract()
    {
        if (! $this->valid()) {
            return false;
        }
        $value = $this->current();
        $this->nextAndRemove();
        return $value;
    }

    
    public function remove(mixed $datum)
    {
        $currentIndex    = $this->index;
        $currentSubIndex = $this->subIndex;
        $currentPriority = $this->maxPriority;

        $this->rewind();
        while ($this->valid()) {
            if (current($this->values[$this->maxPriority]) === $datum) {
                $index = key($this->values[$this->maxPriority]);
                unset($this->values[$this->maxPriority][$index]);

                
                
                reset($this->values[$this->maxPriority]);

                $this->index    = $currentIndex;
                $this->subIndex = $currentSubIndex;

                
                
                
                if (empty($this->values[$this->maxPriority])) {
                    unset($this->values[$this->maxPriority]);
                    unset($this->priorities[$this->maxPriority]);
                    if ($this->maxPriority === $currentPriority) {
                        $this->subIndex = 0;
                    }
                }

                $this->maxPriority = empty($this->priorities) ? null : max($this->priorities);
                --$this->count;
                return true;
            }
            $this->next();
        }
        return false;
    }

    
    #[ReturnTypeWillChange]
    public function count()
    {
        return $this->count;
    }

    
    #[ReturnTypeWillChange]
    public function current()
    {
        switch ($this->extractFlag) {
            case self::EXTR_DATA:
                return current($this->values[$this->maxPriority]);
            case self::EXTR_PRIORITY:
                return $this->maxPriority;
            case self::EXTR_BOTH:
                return [
                    'data'     => current($this->values[$this->maxPriority]),
                    'priority' => $this->maxPriority,
                ];
        }
    }

    
    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->index;
    }

    
    protected function nextAndRemove()
    {
        $key = key($this->values[$this->maxPriority]);

        if (false === next($this->values[$this->maxPriority])) {
            unset($this->priorities[$this->maxPriority]);
            unset($this->values[$this->maxPriority]);
            $this->maxPriority = empty($this->priorities) ? null : max($this->priorities);
            $this->subIndex    = -1;
        } else {
            unset($this->values[$this->maxPriority][$key]);
        }
        ++$this->index;
        ++$this->subIndex;
        --$this->count;
    }

    
    #[ReturnTypeWillChange]
    public function next()
    {
        if (false === next($this->values[$this->maxPriority])) {
            unset($this->subPriorities[$this->maxPriority]);
            reset($this->values[$this->maxPriority]);
            $this->maxPriority = empty($this->subPriorities) ? null : max($this->subPriorities);
            $this->subIndex    = -1;
        }
        ++$this->index;
        ++$this->subIndex;
    }

    
    #[ReturnTypeWillChange]
    public function valid()
    {
        return isset($this->values[$this->maxPriority]);
    }

    
    #[ReturnTypeWillChange]
    public function rewind()
    {
        $this->subPriorities = $this->priorities;
        $this->maxPriority   = empty($this->priorities) ? 0 : max($this->priorities);
        $this->index         = 0;
        $this->subIndex      = 0;
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

    
    public function setExtractFlags($flag)
    {
        $this->extractFlag = match ($flag) {
            self::EXTR_DATA, self::EXTR_PRIORITY, self::EXTR_BOTH => $flag,
            default => throw new Exception\InvalidArgumentException("The extract flag specified is not valid"),
        };
    }

    
    public function isEmpty()
    {
        return empty($this->values);
    }

    
    public function contains(mixed $datum)
    {
        foreach ($this->values as $values) {
            if (in_array($datum, $values)) {
                return true;
            }
        }
        return false;
    }

    
    public function hasPriority($priority)
    {
        return isset($this->values[$priority]);
    }
}
