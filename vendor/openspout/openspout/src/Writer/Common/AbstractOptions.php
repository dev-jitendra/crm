<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common;

use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\TempFolderOptionTrait;

abstract class AbstractOptions
{
    use TempFolderOptionTrait;

    public Style $DEFAULT_ROW_STYLE;
    public bool $SHOULD_CREATE_NEW_SHEETS_AUTOMATICALLY = true;
    public ?float $DEFAULT_COLUMN_WIDTH = null;
    public ?float $DEFAULT_ROW_HEIGHT = null;

    
    private array $COLUMN_WIDTHS = [];

    public function __construct()
    {
        $this->DEFAULT_ROW_STYLE = new Style();
    }

    
    final public function setColumnWidth(float $width, int ...$columns): void
    {
        
        $sequence = [];
        foreach ($columns as $column) {
            $sequenceLength = \count($sequence);
            if ($sequenceLength > 0) {
                $previousValue = $sequence[$sequenceLength - 1];
                if ($column !== $previousValue + 1) {
                    $this->setColumnWidthForRange($width, $sequence[0], $previousValue);
                    $sequence = [];
                }
            }
            $sequence[] = $column;
        }
        $this->setColumnWidthForRange($width, $sequence[0], $sequence[\count($sequence) - 1]);
    }

    
    final public function setColumnWidthForRange(float $width, int $start, int $end): void
    {
        $this->COLUMN_WIDTHS[] = new ColumnWidth($start, $end, $width);
    }

    
    final public function getColumnWidths(): array
    {
        return $this->COLUMN_WIDTHS;
    }
}
