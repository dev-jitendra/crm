<?php

namespace Laminas\Validator\Barcode;

class Leitcode extends AbstractAdapter
{
    
    public function __construct()
    {
        $this->setLength(14);
        $this->setCharacters('0123456789');
        $this->setChecksum('identcode');
    }
}
