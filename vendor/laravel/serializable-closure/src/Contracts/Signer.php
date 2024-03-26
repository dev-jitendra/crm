<?php

namespace Laravel\SerializableClosure\Contracts;

interface Signer
{
    
    public function sign($serializable);

    
    public function verify($signature);
}
