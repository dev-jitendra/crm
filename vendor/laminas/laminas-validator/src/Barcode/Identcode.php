<?php

namespace Laminas\Validator\Barcode;

class Identcode extends AbstractAdapter
{
    
    protected $length = 12;

    
    protected $characters = '0123456789';

    
    protected $checksum = 'identcode';

    
    public function __construct()
    {
        $this->setLength(12);
        $this->setCharacters('0123456789');
        $this->setChecksum('identcode');
    }
}
