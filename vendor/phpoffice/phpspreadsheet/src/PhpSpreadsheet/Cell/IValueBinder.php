<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

interface IValueBinder
{
    
    public function bindValue(Cell $cell, $value);
}
