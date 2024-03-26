<?php

namespace Laminas\Validator\Barcode;

class Gtin12 extends AbstractAdapter
{
    
    public function __construct()
    {
        $this->setLength(12);
        $this->setCharacters('0123456789');
        $this->setChecksum('gtin');
    }
}
