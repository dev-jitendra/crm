<?php

namespace Laminas\Validator\Barcode;

use function chr;
use function is_array;
use function is_string;
use function method_exists;
use function str_replace;
use function str_split;
use function strlen;
use function substr;

abstract class AbstractAdapter implements AdapterInterface
{
    
    protected $options = [
        'length'      => null, 
        'characters'  => null, 
        'checksum'    => null, 
        'useChecksum' => true, 
    ];

    
    public function hasValidLength($value)
    {
        if (! is_string($value)) {
            return false;
        }

        $fixum  = strlen($value);
        $length = $this->getLength();

        if (is_array($length)) {
            foreach ($length as $value) {
                if ($fixum === $value) {
                    return true;
                }

                if ($value === -1) {
                    return true;
                }
            }

            return false;
        }

        if ($fixum === $length) {
            return true;
        }

        if ($length === -1) {
            return true;
        }

        if ($length === 'even') {
            $count = $fixum % 2;
            return 0 === $count;
        }

        if ($length === 'odd') {
            $count = $fixum % 2;
            return 1 === $count;
        }

        return false;
    }

    
    public function hasValidCharacters($value)
    {
        if (! is_string($value)) {
            return false;
        }

        $characters = $this->getCharacters();
        if ($characters === 128) {
            for ($x = 0; $x < 128; ++$x) {
                $value = str_replace(chr($x), '', $value);
            }
        } else {
            $chars = str_split($characters);
            foreach ($chars as $char) {
                $value = str_replace($char, '', $value);
            }
        }

        if (strlen($value) > 0) {
            return false;
        }

        return true;
    }

    
    public function hasValidChecksum($value)
    {
        $checksum = $this->getChecksum();
        if (! empty($checksum)) {
            if (method_exists($this, $checksum)) {
                return $this->$checksum($value);
            }
        }

        return false;
    }

    
    public function getLength()
    {
        return $this->options['length'];
    }

    
    public function getCharacters()
    {
        return $this->options['characters'];
    }

    
    public function getChecksum()
    {
        return $this->options['checksum'];
    }

    
    protected function setChecksum($checksum)
    {
        $this->options['checksum'] = $checksum;
        return $this;
    }

    
    public function useChecksum($check = null)
    {
        if ($check === null) {
            return $this->options['useChecksum'];
        }

        $this->options['useChecksum'] = (bool) $check;
        return $this;
    }

    
    protected function setLength($length)
    {
        $this->options['length'] = $length;
        return $this;
    }

    
    protected function setCharacters($characters)
    {
        $this->options['characters'] = $characters;
        return $this;
    }

    
    protected function gtin($value)
    {
        $barcode = substr($value, 0, -1);
        $sum     = 0;
        $length  = strlen($barcode) - 1;

        for ($i = 0; $i <= $length; $i++) {
            if (($i % 2) === 0) {
                $sum += $barcode[$length - $i] * 3;
            } else {
                $sum += $barcode[$length - $i];
            }
        }

        $calc     = $sum % 10;
        $checksum = $calc === 0 ? 0 : 10 - $calc;

        return $value[$length + 1] === (string) $checksum;
    }

    
    protected function identcode($value)
    {
        $barcode = substr($value, 0, -1);
        $sum     = 0;
        $length  = strlen($value) - 2;

        for ($i = 0; $i <= $length; $i++) {
            if (($i % 2) === 0) {
                $sum += $barcode[$length - $i] * 4;
            } else {
                $sum += $barcode[$length - $i] * 9;
            }
        }

        $calc     = $sum % 10;
        $checksum = $calc === 0 ? 0 : 10 - $calc;

        return $value[$length + 1] === (string) $checksum;
    }

    
    protected function code25($value)
    {
        $barcode = substr($value, 0, -1);
        $sum     = 0;
        $length  = strlen($barcode) - 1;

        for ($i = 0; $i <= $length; $i++) {
            if (($i % 2) === 0) {
                $sum += $barcode[$i] * 3;
            } else {
                $sum += $barcode[$i];
            }
        }

        $calc     = $sum % 10;
        $checksum = $calc === 0 ? 0 : 10 - $calc;

        return $value[$length + 1] === (string) $checksum;
    }

    
    protected function postnet($value)
    {
        $checksum = substr($value, -1, 1);
        $values   = str_split(substr($value, 0, -1));

        $check = 0;
        foreach ($values as $row) {
            $check += $row;
        }

        $check %= 10;
        $check  = 10 - $check;

        return (string) $check === $checksum;
    }
}
