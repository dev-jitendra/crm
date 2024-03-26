<?php

namespace Laminas\Validator\Barcode;

use function strpbrk;
use function substr;

class Codabar extends AbstractAdapter
{
    
    public function __construct()
    {
        $this->setLength(-1);
        $this->setCharacters('0123456789-$:/.+ABCDTN*E');
        $this->useChecksum(false);
    }

    
    public function hasValidCharacters($value)
    {
        if (strpbrk($value, 'ABCD')) {
            $first = $value[0];
            if (! strpbrk($first, 'ABCD')) {
                
                return false;
            }

            $last = substr($value, -1, 1);
            if (! strpbrk($last, 'ABCD')) {
                
                return false;
            }

            $value = substr($value, 1, -1);
        } elseif (strpbrk($value, 'TN*E')) {
            $first = $value[0];
            if (! strpbrk($first, 'TN*E')) {
                
                return false;
            }

            $last = substr($value, -1, 1);
            if (! strpbrk($last, 'TN*E')) {
                
                return false;
            }

            $value = substr($value, 1, -1);
        }

        $chars = $this->getCharacters();
        $this->setCharacters('0123456789-$:/.+');
        $result = parent::hasValidCharacters($value);
        $this->setCharacters($chars);
        return $result;
    }
}
