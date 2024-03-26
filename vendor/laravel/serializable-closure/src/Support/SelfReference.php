<?php

namespace Laravel\SerializableClosure\Support;

class SelfReference
{
    
    public $hash;

    
    public function __construct($hash)
    {
        $this->hash = $hash;
    }
}
