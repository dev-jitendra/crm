<?php

namespace Laminas\Mail\Transport;

use Laminas\Mail\Exception;
use Laminas\Mail\Exception\InvalidArgumentException;
use Laminas\Stdlib\AbstractOptions;

use function gettype;
use function is_callable;
use function is_dir;
use function is_object;
use function is_writable;
use function mt_rand;
use function sprintf;
use function sys_get_temp_dir;
use function time;


class FileOptions extends AbstractOptions
{
    
    protected $path;

    
    protected $callback;

    
    public function setPath($path)
    {
        if (! is_dir($path) || ! is_writable($path)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a valid path in which to write mail files; received "%s"',
                __METHOD__,
                (string) $path
            ));
        }
        $this->path = $path;
        return $this;
    }

    
    public function getPath()
    {
        if (null === $this->path) {
            $this->setPath(sys_get_temp_dir());
        }
        return $this->path;
    }

    
    public function setCallback($callback)
    {
        if (! is_callable($callback)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a valid callback; received "%s"',
                __METHOD__,
                is_object($callback) ? $callback::class : gettype($callback)
            ));
        }
        $this->callback = $callback;
        return $this;
    }

    
    public function getCallback()
    {
        if (null === $this->callback) {
            $this->setCallback(static fn() => 'LaminasMail_' . time() . '_' . mt_rand() . '.eml');
        }
        return $this->callback;
    }
}
