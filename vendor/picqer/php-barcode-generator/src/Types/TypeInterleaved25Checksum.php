<?php

namespace Picqer\Barcode\Types;

use Picqer\Barcode\Barcode;
use Picqer\Barcode\BarcodeBar;
use Picqer\Barcode\Exceptions\InvalidCharacterException;



class TypeInterleaved25Checksum implements TypeInterface
{
    public function getBarcodeData(string $code): Barcode
    {
        $chr = [];
        $chr['0'] = '11221';
        $chr['1'] = '21112';
        $chr['2'] = '12112';
        $chr['3'] = '22111';
        $chr['4'] = '11212';
        $chr['5'] = '21211';
        $chr['6'] = '12211';
        $chr['7'] = '11122';
        $chr['8'] = '21121';
        $chr['9'] = '12121';
        $chr['A'] = '11';
        $chr['Z'] = '21';

        
        $code .= $this->getChecksum($code);

        if ((strlen($code) % 2) != 0) {
            
            $code = '0' . $code;
        }

        
        $code = 'AA' . strtolower($code) . 'ZA';

        $barcode = new Barcode($code);
        for ($i = 0; $i < strlen($code); $i = ($i + 2)) {
            $char_bar = $code[$i];
            $char_space = $code[$i + 1];
            if (! isset($chr[$char_bar]) || ! isset($chr[$char_space])) {
                throw new InvalidCharacterException();
            }

            
            $seq = '';
            $chrlen = strlen($chr[$char_bar]);
            for ($s = 0; $s < $chrlen; $s++) {
                $seq .= $chr[$char_bar][$s] . $chr[$char_space][$s];
            }

            for ($j = 0; $j < strlen($seq); ++$j) {
                if (($j % 2) == 0) {
                    $t = true; 
                } else {
                    $t = false; 
                }
                $w = $seq[$j];
                $barcode->addBar(new BarcodeBar($w, 1, $t));
            }
        }

        return $barcode;
    }

    protected function getChecksum(string $code): string
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

        return (string)$r;
    }
}
