<?php

namespace Laminas\Validator\Barcode;

class Intelligentmail extends AbstractAdapter
{
    
    public function __construct()
    {
        $this->setLength([20, 25, 29, 31]);
        $this->setCharacters('0123456789');
        $this->useChecksum(false);
    }
}
