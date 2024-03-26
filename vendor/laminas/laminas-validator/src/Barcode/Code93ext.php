<?php

namespace Laminas\Validator\Barcode;

class Code93ext extends AbstractAdapter
{
    
    public function __construct()
    {
        $this->setLength(-1);
        $this->setCharacters(128);
        $this->useChecksum(false);
    }
}
