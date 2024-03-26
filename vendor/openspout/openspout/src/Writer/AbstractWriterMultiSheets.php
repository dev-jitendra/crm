<?php

declare(strict_types=1);

namespace OpenSpout\Writer;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\Common\Entity\Sheet;
use OpenSpout\Writer\Common\Manager\WorkbookManagerInterface;
use OpenSpout\Writer\Exception\SheetNotFoundException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;

abstract class AbstractWriterMultiSheets extends AbstractWriter
{
    private WorkbookManagerInterface $workbookManager;

    
    final public function getSheets(): array
    {
        $this->throwIfWorkbookIsNotAvailable();

        $externalSheets = [];
        $worksheets = $this->workbookManager->getWorksheets();

        foreach ($worksheets as $worksheet) {
            $externalSheets[] = $worksheet->getExternalSheet();
        }

        return $externalSheets;
    }

    
    final public function addNewSheetAndMakeItCurrent(): Sheet
    {
        $this->throwIfWorkbookIsNotAvailable();
        $worksheet = $this->workbookManager->addNewSheetAndMakeItCurrent();

        return $worksheet->getExternalSheet();
    }

    
    final public function getCurrentSheet(): Sheet
    {
        $this->throwIfWorkbookIsNotAvailable();

        return $this->workbookManager->getCurrentWorksheet()->getExternalSheet();
    }

    
    final public function setCurrentSheet(Sheet $sheet): void
    {
        $this->throwIfWorkbookIsNotAvailable();
        $this->workbookManager->setCurrentSheet($sheet);
    }

    abstract protected function createWorkbookManager(): WorkbookManagerInterface;

    protected function openWriter(): void
    {
        if (!isset($this->workbookManager)) {
            $this->workbookManager = $this->createWorkbookManager();
            $this->workbookManager->addNewSheetAndMakeItCurrent();
        }
    }

    
    protected function addRowToWriter(Row $row): void
    {
        $this->throwIfWorkbookIsNotAvailable();
        $this->workbookManager->addRowToCurrentWorksheet($row);
    }

    protected function closeWriter(): void
    {
        if (isset($this->workbookManager)) {
            $this->workbookManager->close($this->filePointer);
        }
    }

    
    private function throwIfWorkbookIsNotAvailable(): void
    {
        if (!isset($this->workbookManager)) {
            throw new WriterNotOpenedException('The writer must be opened before performing this action.');
        }
    }
}
