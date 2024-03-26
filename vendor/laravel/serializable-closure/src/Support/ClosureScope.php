<?php

namespace Laravel\SerializableClosure\Support;

use SplObjectStorage;

class ClosureScope extends SplObjectStorage
{
    
    public $serializations = 0;

    
    public $toSerialize = 0;
}
