<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Manager;

use OpenSpout\Writer\Common\Entity\Workbook;
use OpenSpout\Writer\Common\Manager\AbstractWorkbookManager;
use OpenSpout\Writer\Common\Manager\Style\StyleMerger;
use OpenSpout\Writer\XLSX\Helper\FileSystemHelper;
use OpenSpout\Writer\XLSX\Manager\Style\StyleManager;
use OpenSpout\Writer\XLSX\Options;


final class WorkbookManager extends AbstractWorkbookManager
{
    
    private static int $maxRowsPerWorksheet = 1048576;

    public function __construct(
        Workbook $workbook,
        Options $options,
        WorksheetManager $worksheetManager,
        StyleManager $styleManager,
        StyleMerger $styleMerger,
        FileSystemHelper $fileSystemHelper
    ) {
        parent::__construct(
            $workbook,
            $options,
            $worksheetManager,
            $styleManager,
            $styleMerger,
            $fileSystemHelper
        );
    }

    
    protected function getMaxRowsPerWorksheet(): int
    {
        return self::$maxRowsPerWorksheet;
    }

    
    protected function closeRemainingObjects(): void
    {
        $this->worksheetManager->getSharedStringsManager()->close();
    }

    
    protected function writeAllFilesToDiskAndZipThem($finalFilePointer): void
    {
        $worksheets = $this->getWorksheets();

        $this->fileSystemHelper
            ->createContentFiles($this->options, $worksheets)
            ->deleteWorksheetTempFolder()
            ->createContentTypesFile($worksheets)
            ->createWorkbookFile($worksheets)
            ->createWorkbookRelsFile($worksheets)
            ->createWorksheetRelsFiles($worksheets)
            ->createStylesFile($this->styleManager)
            ->zipRootFolderAndCopyToStream($finalFilePointer)
        ;
    }
}
