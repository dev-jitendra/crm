<?php

namespace Picqer\Barcode\Types;



class TypeInterleaved25 extends TypeInterleaved25Checksum
{
    protected function getChecksum(string $code): string
    {
        return '';
    }
}
