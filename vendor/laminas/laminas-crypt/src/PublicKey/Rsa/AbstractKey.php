<?php

namespace Laminas\Crypt\PublicKey\Rsa;

abstract class AbstractKey
{
    public const DEFAULT_KEY_SIZE = 2048;

    
    protected $pemString;

    
    protected $opensslKeyResource;

    
    protected $details = [];

    
    public function getSize()
    {
        return $this->details['bits'];
    }

    
    public function getOpensslKeyResource()
    {
        return $this->opensslKeyResource;
    }

    
    abstract public function encrypt($data);

    
    abstract public function decrypt($data);

    
    abstract public function toString();

    
    public function __toString()
    {
        return $this->toString();
    }
}
