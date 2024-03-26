<?php

namespace Laminas\Validator\Isbn;

class Isbn13
{
    
    public function getChecksum($value)
    {
        $sum = $this->sum($value);
        return $this->checksum($sum);
    }

    
    private function sum($value)
    {
        $sum = 0;

        for ($i = 0; $i < 12; $i++) {
            if ($i % 2 === 0) {
                $sum += (int) $value[$i];
                continue;
            }

            $sum += 3 * (int) $value[$i];
        }

        return $sum;
    }

    
    private function checksum($sum)
    {
        $checksum = 10 - ($sum % 10);

        if ($checksum === 10) {
            return '0';
        }

        return $checksum;
    }
}
