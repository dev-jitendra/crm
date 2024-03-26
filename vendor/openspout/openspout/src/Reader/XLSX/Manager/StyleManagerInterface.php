<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager;


interface StyleManagerInterface
{
    
    public function shouldFormatNumericValueAsDate(int $styleId): bool;

    
    public function getNumberFormatCode(int $styleId): string;
}
