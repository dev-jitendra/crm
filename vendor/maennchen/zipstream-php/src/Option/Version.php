<?php
declare(strict_types=1);

namespace ZipStream\Option;

use MyCLabs\Enum\Enum;


class Version extends Enum
{
    const STORE = 0x000A; 
    const DEFLATE = 0x0014; 
    const ZIP64 = 0x002D; 
}
