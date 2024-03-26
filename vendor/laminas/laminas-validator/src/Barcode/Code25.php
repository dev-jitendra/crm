<?php

namespace Laminas\Validator\Barcode;

class Code25 extends AbstractAdapter
{
    
    public function __construct()
    {
        $this->setLength(-1);
        $this->setCharacters('0123456789');
        $this->setChecksum('code25');
        $this->useChecksum(false);
    }
}
