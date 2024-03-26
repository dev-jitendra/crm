<?php

namespace Picqer\Barcode\Types;

use Picqer\Barcode\Barcode;
use Picqer\Barcode\Exceptions\InvalidCharacterException;
use Picqer\Barcode\Helpers\BinarySequenceConverter;



class TypeStandard2of5 implements TypeInterface
{
    protected $checksum = false;

    public function getBarcodeData(string $code): Barcode
    {
        $chr['0'] = '10101110111010';
        $chr['1'] = '11101010101110';
        $chr['2'] = '10111010101110';
        $chr['3'] = '11101110101010';
        $chr['4'] = '10101110101110';
        $chr['5'] = '11101011101010';
        $chr['6'] = '10111011101010';
        $chr['7'] = '10101011101110';
        $chr['8'] = '11101010111010';
        $chr['9'] = '10111010111010';
        if ($this->checksum) {
            
            $code .= $this->checksum_s25($code);
        }
        $seq = '11011010';

        for ($i = 0; $i < strlen($code); ++$i) {
            $digit = $code[$i];
            if (! isset($chr[$digit])) {
                throw new InvalidCharacterException('Char ' . $digit . ' is unsupported');
            }
            $seq .= $chr[$digit];
        }
        $seq .= '1101011';

        return BinarySequenceConverter::convert($code, $seq);
    }

    
    protected function checksum_s25($code)
    {
        $len = strlen($code);
        $sum = 0;
        for ($i = 0; $i < $len; $i += 2) {
            $sum += $code[$i];
        }
        $sum *= 3;
        for ($i = 1; $i < $len; $i += 2) {
            $sum += ($code[$i]);
        }
        $r = $sum % 10;
        if ($r > 0) {
            $r = (10 - $r);
        }

        return $r;
    }
}
