<?php

namespace Laminas\Crypt\Symmetric\Padding;

use function chr;
use function mb_strlen;
use function mb_substr;
use function ord;
use function str_repeat;


class Pkcs7 implements PaddingInterface
{
    
    public function pad($string, $blockSize = 32)
    {
        $pad = $blockSize - (mb_strlen($string, '8bit') % $blockSize);
        return $string . str_repeat(chr($pad), $pad);
    }

    
    public function strip($string)
    {
        $end  = mb_substr($string, -1, null, '8bit');
        $last = ord($end);
        $len  = mb_strlen($string, '8bit') - $last;
        if (mb_substr($string, $len, null, '8bit') === str_repeat($end, $last)) {
            return mb_substr($string, 0, $len, '8bit');
        }
        return false;
    }
}
