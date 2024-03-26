<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

class DefaultReadFilter implements IReadFilter
{
    
    public function readCell($column, $row, $worksheetName = '')
    {
        return true;
    }
}
