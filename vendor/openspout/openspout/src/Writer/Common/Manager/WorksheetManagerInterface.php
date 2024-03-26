<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Manager;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\Common\Entity\Worksheet;


interface WorksheetManagerInterface
{
    
    public function addRow(Worksheet $worksheet, Row $row): void;

    
    public function startSheet(Worksheet $worksheet): void;

    
    public function close(Worksheet $worksheet): void;
}
