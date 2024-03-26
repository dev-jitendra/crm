<?php

namespace Laminas\Validator\Barcode;

use function strlen;

class Upce extends AbstractAdapter
{
    
    public function __construct()
    {
        $this->setLength([6, 7, 8]);
        $this->setCharacters('0123456789');
        $this->setChecksum('gtin');
    }

    
    public function hasValidLength($value)
    {
        if (strlen($value) !== 8) {
            $this->useChecksum(false);
        } else {
            $this->useChecksum(true);
        }

        return parent::hasValidLength($value);
    }
}
