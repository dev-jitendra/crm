<?php

declare(strict_types=1);

namespace Laminas\Stdlib;

use Traversable;

interface MessageInterface
{
    
    public function setMetadata($spec, $value = null);

    
    public function getMetadata($key = null);

    
    public function setContent($content);

    
    public function getContent();
}
