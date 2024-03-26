<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Writer\Ods;

abstract class WriterPart
{
    
    private $parentWriter;

    
    public function getParentWriter()
    {
        return $this->parentWriter;
    }

    
    public function __construct(Ods $writer)
    {
        $this->parentWriter = $writer;
    }
}
