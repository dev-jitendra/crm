<?php

namespace Picqer\Barcode\Types;



class TypeCode39Checksum extends TypeCode39
{
    protected $extended = false;
    protected $checksum = true;
}
