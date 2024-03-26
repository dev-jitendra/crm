<?php

namespace Laminas\Validator\Isbn;

class Isbn10
{
    
    public function getChecksum($value)
    {
        $sum = $this->sum($value);
        return $this->checksum($sum);
    }

    
    private function sum($value)
    {
        $sum = 0;

        for ($i = 0; $i < 9; $i++) {
            $sum += (10 - $i) * (int) $value[$i];
        }

        return $sum;
    }

    
    private function checksum($sum)
    {
        $checksum = 11 - ($sum % 11);

        if ($checksum === 11) {
            return '0';
        }

        if ($checksum === 10) {
            return 'X';
        }

        return $checksum;
    }
}
