<?php

namespace Laminas\Validator\Barcode;

class Itf14 extends AbstractAdapter
{
    
    public function __construct()
    {
        $this->setLength(14);
        $this->setCharacters('0123456789');
        $this->setChecksum('gtin');
    }
}
