<?php

namespace Laminas\Crypt\Symmetric\Padding;

interface PaddingInterface
{
    
    public function pad($string, $blockSize = 32);

    
    public function strip($string);
}
