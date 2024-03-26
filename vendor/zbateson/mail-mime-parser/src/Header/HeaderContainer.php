<?php

namespace ZBateson\MailMimeParser\Header;

use ArrayIterator;
use IteratorAggregate;
use ZBateson\MailMimeParser\Header\HeaderFactory;


class HeaderContainer implements IteratorAggregate
{
    
    protected $headerFactory;

    
    private $headers = [];

    
    private $headerObjects = [];

    
    private $headerMap = [];

    
    private $nextIndex = 0;

    
    public function __construct(HeaderFactory $headerFactory)
    {
        $this->headerFactory = $headerFactory;
    }

    
    public function exists($name, $offset = 0)
    {
        $s = $this->headerFactory->getNormalizedHeaderName($name);
        return isset($this->headerMap[$s][$offset]);
    }

    
    private function getAllWithOriginalHeaderNameIfSet($name)
    {
        $s = $this->headerFactory->getNormalizedHeaderName($name);
        if (isset($this->headerMap[$s])) {
            $self = $this;
            $filtered = array_filter($this->headerMap[$s], function ($h) use ($name, $self) {
                return (strcasecmp($self->headers[$h][0], $name) === 0);
            });
            return (!empty($filtered)) ? $filtered : $this->headerMap[$s];
        }
        return null;
    }

    
    public function get($name, $offset = 0)
    {
        $a = $this->getAllWithOriginalHeaderNameIfSet($name);
        if (!empty($a) && isset($a[$offset])) {
            return $this->getByIndex($a[$offset]);
        }
        return null;
    }

    
    public function getAll($name)
    {
        $a = $this->getAllWithOriginalHeaderNameIfSet($name);
        if (!empty($a)) {
            $self = $this;
            return array_map(function ($index) use ($self) {
                return $self->getByIndex($index);
            }, $a);
        }
        return [];
    }

    
    private function getByIndex($index)
    {
        if (!isset($this->headers[$index])) {
            return null;
        }
        if ($this->headerObjects[$index] === null) {
            $this->headerObjects[$index] = $this->headerFactory->newInstance(
                $this->headers[$index][0],
                $this->headers[$index][1]
            );
        }
        return $this->headerObjects[$index];
    }

    
    public function remove($name, $offset = 0)
    {
        $s = $this->headerFactory->getNormalizedHeaderName($name);
        if (isset($this->headerMap[$s][$offset])) {
            $index = $this->headerMap[$s][$offset];
            array_splice($this->headerMap[$s], $offset, 1);
            unset($this->headers[$index]);
            unset($this->headerObjects[$index]);
            return true;
        }
        return false;
    }

    
    public function removeAll($name)
    {
        $s = $this->headerFactory->getNormalizedHeaderName($name);
        if (!empty($this->headerMap[$s])) {
            foreach ($this->headerMap[$s] as $i) {
                unset($this->headers[$i]);
                unset($this->headerObjects[$i]);
            }
            $this->headerMap[$s] = [];
            return true;
        }
        return false;
    }

    
    public function add($name, $value)
    {
        $s = $this->headerFactory->getNormalizedHeaderName($name);
        $this->headers[$this->nextIndex] = [ $name, $value ];
        $this->headerObjects[$this->nextIndex] = null;
        if (!isset($this->headerMap[$s])) {
            $this->headerMap[$s] = [];
        }
        array_push($this->headerMap[$s], $this->nextIndex);
        $this->nextIndex++;
    }

    
    public function set($name, $value, $offset = 0)
    {
        $s = $this->headerFactory->getNormalizedHeaderName($name);
        if (!isset($this->headerMap[$s][$offset])) {
            $this->add($name, $value);
            return;
        }
        $i = $this->headerMap[$s][$offset];
        $this->headers[$i] = [ $name, $value ];
        $this->headerObjects[$i] = null;
    }

    
    public function getHeaderObjects()
    {
        return array_filter(array_map([ $this, 'getByIndex' ], array_keys($this->headers)));
    }

    
    public function getHeaders()
    {
        return array_values(array_filter($this->headers));
    }

    
    public function getIterator()
    {
        return new ArrayIterator($this->getHeaders());
    }
}
