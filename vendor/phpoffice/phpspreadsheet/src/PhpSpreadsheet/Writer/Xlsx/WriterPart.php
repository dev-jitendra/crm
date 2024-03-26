<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

abstract class WriterPart
{
    
    private $parentWriter;

    
    public function getParentWriter()
    {
        return $this->parentWriter;
    }

    
    public function __construct(Xlsx $pWriter)
    {
        $this->parentWriter = $pWriter;
    }
}
