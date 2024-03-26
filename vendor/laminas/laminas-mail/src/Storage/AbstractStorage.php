<?php

namespace Laminas\Mail\Storage;

use ArrayAccess;
use Countable;
use Laminas\Mail\Storage\Message;
use ReturnTypeWillChange;
use SeekableIterator;

use function str_starts_with;
use function strtolower;
use function substr;

abstract class AbstractStorage implements
    ArrayAccess,
    Countable,
    SeekableIterator
{
    
    protected $has = [
        'uniqueid'  => true,
        'delete'    => false,
        'create'    => false,
        'top'       => false,
        'fetchPart' => true,
        'flags'     => false,
    ];

    
    protected $iterationPos = 0;

    
    protected $iterationMax;

    
    protected $messageClass = Message::class;

    
    public function __get($var)
    {
        if (str_starts_with($var, 'has')) {
            $var = strtolower(substr($var, 3));
            return $this->has[$var] ?? null;
        }

        throw new Exception\InvalidArgumentException($var . ' not found');
    }

    
    public function getCapabilities()
    {
        return $this->has;
    }

    
    abstract public function countMessages();

    
    abstract public function getSize($id = 0);

    
    abstract public function getMessage($id);

    
    abstract public function getRawHeader($id, $part = null, $topLines = 0);

    
    abstract public function getRawContent($id, $part = null);

    
    abstract public function __construct($params);

    
    public function __destruct()
    {
        $this->close();
    }

    
    abstract public function close();

    
    abstract public function noop();

    
    abstract public function removeMessage($id);

    
    abstract public function getUniqueId($id = null);

    
    abstract public function getNumberByUniqueId($id);

    

    
    #[ReturnTypeWillChange]
    public function count()
    {
        return $this->countMessages();
    }

    
    #[ReturnTypeWillChange]
    public function offsetExists($id)
    {
        try {
            if ($this->getMessage($id)) {
                return true;
            }
        } catch (Exception\ExceptionInterface) {
        }

        return false;
    }

    
    #[ReturnTypeWillChange]
    public function offsetGet($id)
    {
        return $this->getMessage($id);
    }

    
    #[ReturnTypeWillChange]
    public function offsetSet(mixed $id, mixed $value)
    {
        throw new Exception\RuntimeException('cannot write mail messages via array access');
    }

    
    #[ReturnTypeWillChange]
    public function offsetUnset($id)
    {
        return $this->removeMessage($id);
    }

    
    #[ReturnTypeWillChange]
    public function rewind()
    {
        $this->iterationMax = $this->countMessages();
        $this->iterationPos = 1;
    }

    
    #[ReturnTypeWillChange]
    public function current()
    {
        return $this->getMessage($this->iterationPos);
    }

    
    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->iterationPos;
    }

    
    #[ReturnTypeWillChange]
    public function next()
    {
        ++$this->iterationPos;
    }

    
    #[ReturnTypeWillChange]
    public function valid()
    {
        if ($this->iterationMax === null) {
            $this->iterationMax = $this->countMessages();
        }
        return $this->iterationPos && $this->iterationPos <= $this->iterationMax;
    }

    
    #[ReturnTypeWillChange]
    public function seek($pos)
    {
        if ($this->iterationMax === null) {
            $this->iterationMax = $this->countMessages();
        }

        if ($pos > $this->iterationMax) {
            throw new Exception\OutOfBoundsException('this position does not exist');
        }
        $this->iterationPos = $pos;
    }
}
