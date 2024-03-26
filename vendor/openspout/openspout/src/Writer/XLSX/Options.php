<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX;

use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\AbstractOptions;
use OpenSpout\Writer\XLSX\Options\PageMargin;
use OpenSpout\Writer\XLSX\Options\PageSetup;

final class Options extends AbstractOptions
{
    public const DEFAULT_FONT_SIZE = 12;
    public const DEFAULT_FONT_NAME = 'Calibri';

    public bool $SHOULD_USE_INLINE_STRINGS = true;

    
    private array $MERGE_CELLS = [];

    private ?PageMargin $pageMargin = null;

    private ?PageSetup $pageSetup = null;

    public function __construct()
    {
        parent::__construct();

        $defaultRowStyle = new Style();
        $defaultRowStyle->setFontSize(self::DEFAULT_FONT_SIZE);
        $defaultRowStyle->setFontName(self::DEFAULT_FONT_NAME);

        $this->DEFAULT_ROW_STYLE = $defaultRowStyle;
    }

    
    public function mergeCells(
        int $topLeftColumn,
        int $topLeftRow,
        int $bottomRightColumn,
        int $bottomRightRow,
        int $sheetIndex = 0,
    ): void {
        $this->MERGE_CELLS[] = new MergeCell(
            $sheetIndex,
            $topLeftColumn,
            $topLeftRow,
            $bottomRightColumn,
            $bottomRightRow
        );
    }

    
    public function getMergeCells(): array
    {
        return $this->MERGE_CELLS;
    }

    public function setPageMargin(PageMargin $pageMargin): void
    {
        $this->pageMargin = $pageMargin;
    }

    public function getPageMargin(): ?PageMargin
    {
        return $this->pageMargin;
    }

    public function setPageSetup(PageSetup $pageSetup): void
    {
        $this->pageSetup = $pageSetup;
    }

    public function getPageSetup(): ?PageSetup
    {
        return $this->pageSetup;
    }
}
