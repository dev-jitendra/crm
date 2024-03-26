<?php

namespace Laminas\Validator\Barcode;

class Planet extends AbstractAdapter
{
    
    public function __construct()
    {
        $this->setLength([12, 14]);
        $this->setCharacters('0123456789');
        $this->setChecksum('postnet');
    }
}
