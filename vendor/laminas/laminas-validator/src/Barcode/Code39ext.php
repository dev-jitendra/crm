<?php

namespace Laminas\Validator\Barcode;

class Code39ext extends AbstractAdapter
{
    
    public function __construct()
    {
        $this->setLength(-1);
        $this->setCharacters(128);
        $this->useChecksum(false);
    }
}
