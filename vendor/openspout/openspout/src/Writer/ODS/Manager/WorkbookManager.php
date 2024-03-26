<?php

declare(strict_types=1);

namespace OpenSpout\Writer\ODS\Manager;

use OpenSpout\Writer\Common\Entity\Workbook;
use OpenSpout\Writer\Common\Manager\AbstractWorkbookManager;
use OpenSpout\Writer\Common\Manager\Style\StyleMerger;
use OpenSpout\Writer\ODS\Helper\FileSystemHelper;
use OpenSpout\Writer\ODS\Manager\Style\StyleManager;
use OpenSpout\Writer\ODS\Options;


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

    
    protected function writeAllFilesToDiskAndZipThem($finalFilePointer): void
    {
        $worksheets = $this->getWorksheets();
        $numWorksheets = \count($worksheets);

        $this->fileSystemHelper
            ->createContentFile($this->worksheetManager, $this->styleManager, $worksheets)
            ->deleteWorksheetTempFolder()
            ->createStylesFile($this->styleManager, $numWorksheets)
            ->zipRootFolderAndCopyToStream($finalFilePointer)
        ;
    }
}
