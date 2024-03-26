<?php

namespace Laminas\Mail\Storage;

use Laminas\Stdlib\ErrorHandler;

use function array_combine;
use function file_get_contents;
use function is_resource;
use function ltrim;
use function stream_get_contents;

class Message extends Part implements Message\MessageInterface
{
    
    protected $flags = [];

    
    public function __construct(array $params)
    {
        if (isset($params['file'])) {
            if (! is_resource($params['file'])) {
                ErrorHandler::start();
                $params['raw'] = file_get_contents($params['file']);
                $error         = ErrorHandler::stop();
                if ($params['raw'] === false) {
                    throw new Exception\RuntimeException('could not open file', 0, $error);
                }
            } else {
                $params['raw'] = stream_get_contents($params['file']);
            }

            $params['raw'] = ltrim($params['raw']);
        }

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
