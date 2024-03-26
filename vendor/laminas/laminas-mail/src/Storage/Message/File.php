<?php

namespace Laminas\Mail\Storage\Message;

use Laminas\Mail\Storage\Exception\ExceptionInterface;
use Laminas\Mail\Storage\Part;

use function array_combine;

class File extends Part\File implements MessageInterface
{
    
    protected $flags = [];

    
    public function __construct(array $params)
    {
        if (! empty($params['flags'])) {
            
            $this->flags = array_combine($params['flags'], $params['flags']);
        }

        parent::__construct($params);
    }

    
    public function getTopLines()
    {
        return $this->topLines;
    }

    
    public function hasFlag($flag)
    {
        return isset($this->flags[$flag]);
    }

    
    public function getFlags()
    {
        return $this->flags;
    }
}
