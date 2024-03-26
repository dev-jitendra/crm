<?php

declare(strict_types=1);

namespace OpenSpout\Reader\Common\Manager;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;


final class RowManager
{
    
    public function fillMissingIndexesWithEmptyCells(Row $row): void
    {
        $numCells = $row->getNumCells();

        if (0 === $numCells) {
            return;
        }

        $rowCells = $row->getCells();
        $maxCellIndex = $numCells;

        
        $needsSorting = false;

        for ($cellIndex = 0; $cellIndex < $maxCellIndex; ++$cellIndex) {
            if (!isset($rowCells[$cellIndex])) {
                $row->setCellAtIndex(Cell::fromValue(''), $cellIndex);
                $needsSorting = true;
            }
        }

        if ($needsSorting) {
            $rowCells = $row->getCells();
            ksort($rowCells);
            $row->setCells($rowCells);
        }
    }
}
