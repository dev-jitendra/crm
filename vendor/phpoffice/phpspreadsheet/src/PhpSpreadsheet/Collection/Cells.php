<?php

namespace PhpOffice\PhpSpreadsheet\Collection;

use Generator;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Psr\SimpleCache\CacheInterface;

class Cells
{
    
    private $cache;

    
    private $parent;

    
    private $currentCell;

    
    private $currentCoordinate;

    
    private $currentCellIsDirty = false;

    
    private $index = [];

    
    private $cachePrefix;

    
    public function __construct(Worksheet $parent, CacheInterface $cache)
    {
        
        
        
        $this->parent = $parent;
        $this->cache = $cache;
        $this->cachePrefix = $this->getUniqueID();
    }

    
    public function getParent()
    {
        return $this->parent;
    }

    
    public function has($pCoord)
    {
        if ($pCoord === $this->currentCoordinate) {
            return true;
        }

        
        return isset($this->index[$pCoord]);
    }

    
    public function update(Cell $cell)
    {
        return $this->add($cell->getCoordinate(), $cell);
    }

    
    public function delete($pCoord): void
    {
        if ($pCoord === $this->currentCoordinate && $this->currentCell !== null) {
            $this->currentCell->detach();
            $this->currentCoordinate = null;
            $this->currentCell = null;
            $this->currentCellIsDirty = false;
        }

        unset($this->index[$pCoord]);

        
        $this->cache->delete($this->cachePrefix . $pCoord);
    }

    
    public function getCoordinates()
    {
        return array_keys($this->index);
    }

    
    public function getSortedCoordinates()
    {
        $sortKeys = [];
        foreach ($this->getCoordinates() as $coord) {
            $column = '';
            $row = 0;
            sscanf($coord, '%[A-Z]%d', $column, $row);
            $sortKeys[sprintf('%09d%3s', $row, $column)] = $coord;
        }
        ksort($sortKeys);

        return array_values($sortKeys);
    }

    
    public function getHighestRowAndColumn()
    {
        
        $col = ['A' => '1A'];
        $row = [1];
        foreach ($this->getCoordinates() as $coord) {
            $c = '';
            $r = 0;
            sscanf($coord, '%[A-Z]%d', $c, $r);
            $row[$r] = $r;
            $col[$c] = strlen($c) . $c;
        }

        
        $highestRow = max($row);
        $highestColumn = substr(max($col), 1);

        return [
            'row' => $highestRow,
            'column' => $highestColumn,
        ];
    }

    
    public function getCurrentCoordinate()
    {
        return $this->currentCoordinate;
    }

    
    public function getCurrentColumn()
    {
        $column = '';
        $row = 0;

        sscanf($this->currentCoordinate, '%[A-Z]%d', $column, $row);

        return $column;
    }

    
    public function getCurrentRow()
    {
        $column = '';
        $row = 0;

        sscanf($this->currentCoordinate, '%[A-Z]%d', $column, $row);

        return (int) $row;
    }

    
    public function getHighestColumn($row = null)
    {
        if ($row === null) {
            $colRow = $this->getHighestRowAndColumn();

            return $colRow['column'];
        }

        $columnList = [1];
        foreach ($this->getCoordinates() as $coord) {
            $c = '';
            $r = 0;

            sscanf($coord, '%[A-Z]%d', $c, $r);
            if ($r != $row) {
                continue;
            }
            $columnList[] = Coordinate::columnIndexFromString($c);
        }

        return Coordinate::stringFromColumnIndex(max($columnList));
    }

    
    public function getHighestRow($column = null)
    {
        if ($column === null) {
            $colRow = $this->getHighestRowAndColumn();

            return $colRow['row'];
        }

        $rowList = [0];
        foreach ($this->getCoordinates() as $coord) {
            $c = '';
            $r = 0;

            sscanf($coord, '%[A-Z]%d', $c, $r);
            if ($c != $column) {
                continue;
            }
            $rowList[] = $r;
        }

        return max($rowList);
    }

    
    private function getUniqueID()
    {
        return uniqid('phpspreadsheet.', true) . '.';
    }

    
    public function cloneCellCollection(Worksheet $parent)
    {
        $this->storeCurrentCell();
        $newCollection = clone $this;

        $newCollection->parent = $parent;
        if (($newCollection->currentCell !== null) && (is_object($newCollection->currentCell))) {
            $newCollection->currentCell->attach($this);
        }

        
        $oldKeys = $newCollection->getAllCacheKeys();
        $oldValues = $newCollection->cache->getMultiple($oldKeys);
        $newValues = [];
        $oldCachePrefix = $newCollection->cachePrefix;

        
        $newCollection->cachePrefix = $newCollection->getUniqueID();
        foreach ($oldValues as $oldKey => $value) {
            $newValues[str_replace($oldCachePrefix, $newCollection->cachePrefix, $oldKey)] = clone $value;
        }

        
        $stored = $newCollection->cache->setMultiple($newValues);
        if (!$stored) {
            $newCollection->__destruct();

            throw new PhpSpreadsheetException('Failed to copy cells in cache');
        }

        return $newCollection;
    }

    
    public function removeRow($row): void
    {
        foreach ($this->getCoordinates() as $coord) {
            $c = '';
            $r = 0;

            sscanf($coord, '%[A-Z]%d', $c, $r);
            if ($r == $row) {
                $this->delete($coord);
            }
        }
    }

    
    public function removeColumn($column): void
    {
        foreach ($this->getCoordinates() as $coord) {
            $c = '';
            $r = 0;

            sscanf($coord, '%[A-Z]%d', $c, $r);
            if ($c == $column) {
                $this->delete($coord);
            }
        }
    }

    
    private function storeCurrentCell(): void
    {
        if ($this->currentCellIsDirty && !empty($this->currentCoordinate)) {
            $this->currentCell->detach();

            $stored = $this->cache->set($this->cachePrefix . $this->currentCoordinate, $this->currentCell);
            if (!$stored) {
                $this->__destruct();

                throw new PhpSpreadsheetException("Failed to store cell {$this->currentCoordinate} in cache");
            }
            $this->currentCellIsDirty = false;
        }

        $this->currentCoordinate = null;
        $this->currentCell = null;
    }

    
    public function add($pCoord, Cell $cell)
    {
        if ($pCoord !== $this->currentCoordinate) {
            $this->storeCurrentCell();
        }
        $this->index[$pCoord] = true;

        $this->currentCoordinate = $pCoord;
        $this->currentCell = $cell;
        $this->currentCellIsDirty = true;

        return $cell;
    }

    
    public function get($pCoord)
    {
        if ($pCoord === $this->currentCoordinate) {
            return $this->currentCell;
        }
        $this->storeCurrentCell();

        
        if (!$this->has($pCoord)) {
            return null;
        }

        
        $cell = $this->cache->get($this->cachePrefix . $pCoord);
        if ($cell === null) {
            throw new PhpSpreadsheetException("Cell entry {$pCoord} no longer exists in cache. This probably means that the cache was cleared by someone else.");
        }

        
        $this->currentCoordinate = $pCoord;
        $this->currentCell = $cell;
        
        $this->currentCell->attach($this);

        
        return $this->currentCell;
    }

    
    public function unsetWorksheetCells(): void
    {
        if ($this->currentCell !== null) {
            $this->currentCell->detach();
            $this->currentCell = null;
            $this->currentCoordinate = null;
        }

        
        $this->__destruct();

        $this->index = [];

        
        $this->parent = null;
    }

    
    public function __destruct()
    {
        $this->cache->deleteMultiple($this->getAllCacheKeys());
    }

    
    private function getAllCacheKeys()
    {
        foreach ($this->getCoordinates() as $coordinate) {
            yield $this->cachePrefix . $coordinate;
        }
    }
}
