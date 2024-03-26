<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class StringValueBinder implements IValueBinder
{
    
    public function bindValue(Cell $cell, $value)
    {
        
        if (is_string($value)) {
            $value = StringHelper::sanitizeUTF8($value);
        }

        $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);

        
        return true;
    }
}
