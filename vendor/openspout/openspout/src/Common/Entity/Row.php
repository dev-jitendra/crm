<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity;

use DateInterval;
use DateTimeInterface;
use OpenSpout\Common\Entity\Style\Style;

final class Row
{
    
    private array $cells = [];

    
    private Style $style;

    
    private float $height = 0;

    
    public function __construct(array $cells, ?Style $style = null)
    {
        $this
            ->setCells($cells)
            ->setStyle($style)
        ;
    }

    
    public static function fromValues(array $cellValues = [], ?Style $rowStyle = null): self
    {
        $cells = array_map(static function (null|bool|DateInterval|DateTimeInterface|float|int|string $cellValue): Cell {
            return Cell::fromValue($cellValue);
        }, $cellValues);

        return new self($cells, $rowStyle);
    }

    
    public function getCells(): array
    {
        return $this->cells;
    }

    
    public function setCells(array $cells): self
    {
        $this->cells = [];
        foreach ($cells as $cell) {
            $this->addCell($cell);
        }

        return $this;
    }

    public function setCellAtIndex(Cell $cell, int $cellIndex): self
    {
        $this->cells[$cellIndex] = $cell;

        return $this;
    }

    public function getCellAtIndex(int $cellIndex): ?Cell
    {
        return $this->cells[$cellIndex] ?? null;
    }

    public function addCell(Cell $cell): self
    {
        $this->cells[] = $cell;

        return $this;
    }

    public function getNumCells(): int
    {
        
        
        if ([] === $this->cells) {
            return 0;
        }

        return max(array_keys($this->cells)) + 1;
    }

    public function getStyle(): Style
    {
        return $this->style;
    }

    public function setStyle(?Style $style): self
    {
        $this->style = $style ?? new Style();

        return $this;
    }

    
    public function setHeight(float $height): self
    {
        $this->height = $height;

        return $this;
    }

    
    public function getHeight(): float
    {
        return $this->height;
    }

    
    public function toArray(): array
    {
        return array_map(static function (Cell $cell): null|bool|DateInterval|DateTimeInterface|float|int|string {
            return $cell->getValue();
        }, $this->cells);
    }

    
    public function isEmpty(): bool
    {
        foreach ($this->cells as $cell) {
            if (!$cell instanceof Cell\EmptyCell) {
                return false;
            }
        }

        return true;
    }
}
