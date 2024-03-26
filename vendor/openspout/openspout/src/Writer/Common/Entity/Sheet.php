<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Entity;

use OpenSpout\Writer\AutoFilter;
use OpenSpout\Writer\Common\ColumnWidth;
use OpenSpout\Writer\Common\Manager\SheetManager;
use OpenSpout\Writer\XLSX\Entity\SheetView;


final class Sheet
{
    public const DEFAULT_SHEET_NAME_PREFIX = 'Sheet';

    
    private readonly int $index;

    
    private readonly string $associatedWorkbookId;

    
    private string $name;

    
    private bool $isVisible;

    
    private readonly SheetManager $sheetManager;

    private ?SheetView $sheetView = null;

    
    private int $writtenRowCount = 0;

    private ?AutoFilter $autoFilter = null;

    
    private array $COLUMN_WIDTHS = [];

    
    public function __construct(int $sheetIndex, string $associatedWorkbookId, SheetManager $sheetManager)
    {
        $this->index = $sheetIndex;
        $this->associatedWorkbookId = $associatedWorkbookId;

        $this->sheetManager = $sheetManager;
        $this->sheetManager->markWorkbookIdAsUsed($associatedWorkbookId);

        $this->setName(self::DEFAULT_SHEET_NAME_PREFIX.($sheetIndex + 1));
        $this->setIsVisible(true);
    }

    
    public function getIndex(): int
    {
        return $this->index;
    }

    public function getAssociatedWorkbookId(): string
    {
        return $this->associatedWorkbookId;
    }

    
    public function getName(): string
    {
        return $this->name;
    }

    
    public function setName(string $name): self
    {
        $this->sheetManager->throwIfNameIsInvalid($name, $this);

        $this->name = $name;

        $this->sheetManager->markSheetNameAsUsed($this);

        return $this;
    }

    
    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    
    public function setIsVisible(bool $isVisible): self
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    
    public function setSheetView(SheetView $sheetView): self
    {
        $this->sheetView = $sheetView;

        return $this;
    }

    public function getSheetView(): ?SheetView
    {
        return $this->sheetView;
    }

    
    public function incrementWrittenRowCount(): void
    {
        ++$this->writtenRowCount;
    }

    
    public function getWrittenRowCount(): int
    {
        return $this->writtenRowCount;
    }

    
    public function setAutoFilter(?AutoFilter $autoFilter): self
    {
        $this->autoFilter = $autoFilter;

        return $this;
    }

    public function getAutoFilter(): ?AutoFilter
    {
        return $this->autoFilter;
    }

    
    public function setColumnWidth(float $width, int ...$columns): void
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

    
    public function setColumnWidthForRange(float $width, int $start, int $end): void
    {
        $this->COLUMN_WIDTHS[] = new ColumnWidth($start, $end, $width);
    }

    
    public function getColumnWidths(): array
    {
        return $this->COLUMN_WIDTHS;
    }
}
