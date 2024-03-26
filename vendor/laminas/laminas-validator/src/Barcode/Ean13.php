<?php

namespace Laminas\Validator\Barcode;

class Ean13 extends AbstractAdapter
{
    
    public function __construct()
    {
        $this->setLength(13);
        $this->setCharacters('0123456789');
        $this->setChecksum('gtin');
    }
}
