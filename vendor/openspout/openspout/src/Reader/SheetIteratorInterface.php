<?php

declare(strict_types=1);

namespace OpenSpout\Reader;

use Iterator;


interface SheetIteratorInterface extends Iterator
{
    
    public function current(): SheetInterface;
}
