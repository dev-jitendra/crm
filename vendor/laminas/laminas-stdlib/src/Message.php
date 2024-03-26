<?php

declare(strict_types=1);

namespace Laminas\Stdlib;

use Traversable;

use function array_key_exists;
use function get_debug_type;
use function is_array;
use function is_scalar;
use function sprintf;

class Message implements MessageInterface
{
    
    protected $metadata = [];

    
    protected $content = '';

    
    public function setMetadata($spec, $value = null)
    {
        if (is_scalar($spec)) {
            $this->metadata[$spec] = $value;
            return $this;
        }
        if (! is_array($spec) && ! $spec instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected a string, array, or Traversable argument in first position; received "%s"',
                get_debug_type($spec)
            ));
        }
        foreach ($spec as $key => $value) {
            $this->metadata[$key] = $value;
        }
        return $this;
    }

    
    public function getMetadata($key = null, $default = null)
    {
        if (null === $key) {
            return $this->metadata;
        }

        if (! is_scalar($key)) {
            throw new Exception\InvalidArgumentException('Non-scalar argument provided for key');
        }

        if (array_key_exists($key, $this->metadata)) {
            return $this->metadata[$key];
        }

        return $default;
    }

    
    public function setContent($value)
    {
        $this->content = $value;
        return $this;
    }

    
    public function getContent()
    {
        return $this->content;
    }

    
    public function toString()
    {
        $request = '';
        foreach ($this->getMetadata() as $key => $value) {
            $request .= sprintf(
                "%s: %s\r\n",
                (string) $key,
                (string) $value
            );
        }
        $request .= "\r\n" . $this->getContent();
        return $request;
    }
}
