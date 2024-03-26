<?php

namespace Picqer\Barcode\Types;



class TypeCode39ExtendedChecksum extends TypeCode39
{
    protected $extended = true;
    protected $checksum = true;
}
