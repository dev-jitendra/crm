<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Manager;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Helper\StringHelper;
use OpenSpout\Writer\Common\AbstractOptions;
use OpenSpout\Writer\Common\Entity\Sheet;
use OpenSpout\Writer\Common\Entity\Workbook;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Common\Helper\FileSystemWithRootFolderHelperInterface;
use OpenSpout\Writer\Common\Manager\Style\StyleManagerInterface;
use OpenSpout\Writer\Common\Manager\Style\StyleMerger;
use OpenSpout\Writer\Exception\SheetNotFoundException;


abstract class AbstractWorkbookManager implements WorkbookManagerInterface
{
    protected WorksheetManagerInterface $worksheetManager;

    
    protected StyleManagerInterface $styleManager;

    
    protected FileSystemWithRootFolderHelperInterface $fileSystemHelper;

    protected AbstractOptions $options;

    
    private readonly Workbook $workbook;

    
    private readonly StyleMerger $styleMerger;

    
    private Worksheet $currentWorksheet;

    public function __construct(
        Workbook $workbook,
        AbstractOptions $options,
        WorksheetManagerInterface $worksheetManager,
        StyleManagerInterface $styleManager,
        StyleMerger $styleMerger,
        FileSystemWithRootFolderHelperInterface $fileSystemHelper
    ) {
        $this->workbook = $workbook;
        $this->options = $options;
        $this->worksheetManager = $worksheetManager;
        $this->styleManager = $styleManager;
        $this->styleMerger = $styleMerger;
        $this->fileSystemHelper = $fileSystemHelper;
    }

    
    final public function addNewSheetAndMakeItCurrent(): Worksheet
    {
        $worksheet = $this->addNewSheet();
        $this->setCurrentWorksheet($worksheet);

        return $worksheet;
    }

    
    final public function getWorksheets(): array
    {
        return $this->workbook->getWorksheets();
    }

    
    final public function getCurrentWorksheet(): Worksheet
    {
        return $this->currentWorksheet;
    }

    
    final public function setCurrentSheet(Sheet $sheet): void
    {
        $worksheet = $this->getWorksheetFromExternalSheet($sheet);
        if (null !== $worksheet) {
            $this->currentWorksheet = $worksheet;
        } else {
            throw new SheetNotFoundException('The given sheet does not exist in the workbook.');
        }
    }

    
    final public function addRowToCurrentWorksheet(Row $row): void
    {
        $currentWorksheet = $this->getCurrentWorksheet();
        if ($this->hasCurrentWorksheetReachedMaxRows()) {
            if (!$this->options->SHOULD_CREATE_NEW_SHEETS_AUTOMATICALLY) {
                return;
            }

            $currentWorksheet = $this->addNewSheetAndMakeItCurrent();
        }

        $this->addRowToWorksheet($currentWorksheet, $row);
        $currentWorksheet->getExternalSheet()->incrementWrittenRowCount();
    }

    
    final public function close($finalFilePointer): void
    {
        $this->closeAllWorksheets();
        $this->closeRemainingObjects();
        $this->writeAllFilesToDiskAndZipThem($finalFilePointer);
        $this->cleanupTempFolder();
    }

    
    abstract protected function getMaxRowsPerWorksheet(): int;

    
    protected function closeRemainingObjects(): void
    {
        
    }

    
    abstract protected function writeAllFilesToDiskAndZipThem($finalFilePointer): void;

    
    private function getWorksheetFilePath(Sheet $sheet): string
    {
        $sheetsContentTempFolder = $this->fileSystemHelper->getSheetsContentTempFolder();

        return $sheetsContentTempFolder.\DIRECTORY_SEPARATOR.'sheet'.(1 + $sheet->getIndex()).'.xml';
    }

    
    private function cleanupTempFolder(): void
    {
        $rootFolder = $this->fileSystemHelper->getRootFolder();
        $this->fileSystemHelper->deleteFolderRecursively($rootFolder);
    }

    
    private function addNewSheet(): Worksheet
    {
        $worksheets = $this->getWorksheets();

        $newSheetIndex = \count($worksheets);
        $sheetManager = new SheetManager(StringHelper::factory());
        $sheet = new Sheet($newSheetIndex, $this->workbook->getInternalId(), $sheetManager);

        $worksheetFilePath = $this->getWorksheetFilePath($sheet);
        $worksheet = new Worksheet($worksheetFilePath, $sheet);

        $this->worksheetManager->startSheet($worksheet);

        $worksheets[] = $worksheet;
        $this->workbook->setWorksheets($worksheets);

        return $worksheet;
    }

    private function setCurrentWorksheet(Worksheet $worksheet): void
    {
        $this->currentWorksheet = $worksheet;
    }

    
    private function getWorksheetFromExternalSheet(Sheet $sheet): ?Worksheet
    {
        $worksheetFound = null;

        foreach ($this->getWorksheets() as $worksheet) {
            if ($worksheet->getExternalSheet() === $sheet) {
                $worksheetFound = $worksheet;

                break;
            }
        }

        return $worksheetFound;
    }

    
    private function hasCurrentWorksheetReachedMaxRows(): bool
    {
        $currentWorksheet = $this->getCurrentWorksheet();

        return $currentWorksheet->getLastWrittenRowIndex() >= $this->getMaxRowsPerWorksheet();
    }

    
    private function addRowToWorksheet(Worksheet $worksheet, Row $row): void
    {
        $this->applyDefaultRowStyle($row);
        $this->worksheetManager->addRow($worksheet, $row);

        
        $currentMaxNumColumns = $worksheet->getMaxNumColumns();
        $cellsCount = $row->getNumCells();
        $worksheet->setMaxNumColumns(max($currentMaxNumColumns, $cellsCount));
    }

    private function applyDefaultRowStyle(Row $row): void
    {
        $mergedStyle = $this->styleMerger->merge(
            $row->getStyle(),
            $this->options->DEFAULT_ROW_STYLE
        );
        $row->setStyle($mergedStyle);
    }

    
    private function closeAllWorksheets(): void
    {
        $worksheets = $this->getWorksheets();

        foreach ($worksheets as $worksheet) {
            $this->worksheetManager->close($worksheet);
        }
    }
}
