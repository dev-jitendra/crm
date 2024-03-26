<?php

namespace Laminas\Crypt\Symmetric\Padding;


class NoPadding implements PaddingInterface
{
    
    public function pad($string, $blockSize = 32)
    {
        return $string;
    }

    
    public function strip($string)
    {
        return $string;
    }
}
