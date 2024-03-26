<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Manager;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\Common\Entity\Sheet;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Exception\SheetNotFoundException;
use OpenSpout\Writer\Exception\WriterException;


interface WorkbookManagerInterface
{
    
    public function addNewSheetAndMakeItCurrent(): Worksheet;

    
    public function getWorksheets(): array;

    
    public function getCurrentWorksheet(): Worksheet;

    
    public function setCurrentSheet(Sheet $sheet): void;

    
    public function addRowToCurrentWorksheet(Row $row): void;

    
    public function close($finalFilePointer): void;
}
