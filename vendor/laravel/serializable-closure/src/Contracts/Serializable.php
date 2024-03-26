<?php

namespace Laravel\SerializableClosure\Contracts;

interface Serializable
{
    
    public function __invoke();

    
    public function getClosure();
}
