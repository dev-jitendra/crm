<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


abstract class Coordinate
{
    
    const DEFAULT_RANGE = 'A1:A1';

    
    public static function coordinateFromString($pCoordinateString)
    {
        if (preg_match('/^([$]?[A-Z]{1,3})([$]?\\d{1,7})$/', $pCoordinateString, $matches)) {
            return [$matches[1], $matches[2]];
        } elseif (self::coordinateIsRange($pCoordinateString)) {
            throw new Exception('Cell coordinate string can not be a range of cells');
        } elseif ($pCoordinateString == '') {
            throw new Exception('Cell coordinate can not be zero-length string');
        }

        throw new Exception('Invalid cell coordinate ' . $pCoordinateString);
    }

    
    public static function coordinateIsRange($coord)
    {
        return (strpos($coord, ':') !== false) || (strpos($coord, ',') !== false);
    }

    
    public static function absoluteReference($pCoordinateString)
    {
        if (self::coordinateIsRange($pCoordinateString)) {
            throw new Exception('Cell coordinate string can not be a range of cells');
        }

        
        [$worksheet, $pCoordinateString] = Worksheet::extractSheetTitle($pCoordinateString, true);
        if ($worksheet > '') {
            $worksheet .= '!';
        }

        
        if (ctype_digit($pCoordinateString)) {
            return $worksheet . '$' . $pCoordinateString;
        } elseif (ctype_alpha($pCoordinateString)) {
            return $worksheet . '$' . strtoupper($pCoordinateString);
        }

        return $worksheet . self::absoluteCoordinate($pCoordinateString);
    }

    
    public static function absoluteCoordinate($pCoordinateString)
    {
        if (self::coordinateIsRange($pCoordinateString)) {
            throw new Exception('Cell coordinate string can not be a range of cells');
        }

        
        [$worksheet, $pCoordinateString] = Worksheet::extractSheetTitle($pCoordinateString, true);
        if ($worksheet > '') {
            $worksheet .= '!';
        }

        
        [$column, $row] = self::coordinateFromString($pCoordinateString);
        $column = ltrim($column, '$');
        $row = ltrim($row, '$');

        return $worksheet . '$' . $column . '$' . $row;
    }

    
    public static function splitRange($pRange)
    {
        
        if (empty($pRange)) {
            $pRange = self::DEFAULT_RANGE;
        }

        $exploded = explode(',', $pRange);
        $counter = count($exploded);
        for ($i = 0; $i < $counter; ++$i) {
            $exploded[$i] = explode(':', $exploded[$i]);
        }

        return $exploded;
    }

    
    public static function buildRange(array $pRange)
    {
        
        if (empty($pRange) || !is_array($pRange[0])) {
            throw new Exception('Range does not contain any information');
        }

        
        $counter = count($pRange);
        for ($i = 0; $i < $counter; ++$i) {
            $pRange[$i] = implode(':', $pRange[$i]);
        }

        return implode(',', $pRange);
    }

    
    public static function rangeBoundaries($pRange)
    {
        
        if (empty($pRange)) {
            $pRange = self::DEFAULT_RANGE;
        }

        
        $pRange = strtoupper($pRange);

        
        if (strpos($pRange, ':') === false) {
            $rangeA = $rangeB = $pRange;
        } else {
            [$rangeA, $rangeB] = explode(':', $pRange);
        }

        
        $rangeStart = self::coordinateFromString($rangeA);
        $rangeEnd = self::coordinateFromString($rangeB);

        
        $rangeStart[0] = self::columnIndexFromString($rangeStart[0]);
        $rangeEnd[0] = self::columnIndexFromString($rangeEnd[0]);

        return [$rangeStart, $rangeEnd];
    }

    
    public static function rangeDimension($pRange)
    {
        
        [$rangeStart, $rangeEnd] = self::rangeBoundaries($pRange);

        return [($rangeEnd[0] - $rangeStart[0] + 1), ($rangeEnd[1] - $rangeStart[1] + 1)];
    }

    
    public static function getRangeBoundaries($pRange)
    {
        
        if (empty($pRange)) {
            $pRange = self::DEFAULT_RANGE;
        }

        
        $pRange = strtoupper($pRange);

        
        if (strpos($pRange, ':') === false) {
            $rangeA = $rangeB = $pRange;
        } else {
            [$rangeA, $rangeB] = explode(':', $pRange);
        }

        return [self::coordinateFromString($rangeA), self::coordinateFromString($rangeB)];
    }

    
    public static function columnIndexFromString($pString)
    {
        
        
        
        static $indexCache = [];

        if (isset($indexCache[$pString])) {
            return $indexCache[$pString];
        }
        
        
        
        static $columnLookup = [
            'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13,
            'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26,
            'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6, 'g' => 7, 'h' => 8, 'i' => 9, 'j' => 10, 'k' => 11, 'l' => 12, 'm' => 13,
            'n' => 14, 'o' => 15, 'p' => 16, 'q' => 17, 'r' => 18, 's' => 19, 't' => 20, 'u' => 21, 'v' => 22, 'w' => 23, 'x' => 24, 'y' => 25, 'z' => 26,
        ];

        
        
        if (isset($pString[0])) {
            if (!isset($pString[1])) {
                $indexCache[$pString] = $columnLookup[$pString];

                return $indexCache[$pString];
            } elseif (!isset($pString[2])) {
                $indexCache[$pString] = $columnLookup[$pString[0]] * 26 + $columnLookup[$pString[1]];

                return $indexCache[$pString];
            } elseif (!isset($pString[3])) {
                $indexCache[$pString] = $columnLookup[$pString[0]] * 676 + $columnLookup[$pString[1]] * 26 + $columnLookup[$pString[2]];

                return $indexCache[$pString];
            }
        }

        throw new Exception('Column string index can not be ' . ((isset($pString[0])) ? 'longer than 3 characters' : 'empty'));
    }

    
    public static function stringFromColumnIndex($columnIndex)
    {
        static $indexCache = [];

        if (!isset($indexCache[$columnIndex])) {
            $indexValue = $columnIndex;
            $base26 = null;
            do {
                $characterValue = ($indexValue % 26) ?: 26;
                $indexValue = ($indexValue - $characterValue) / 26;
                $base26 = chr($characterValue + 64) . ($base26 ?: '');
            } while ($indexValue > 0);
            $indexCache[$columnIndex] = $base26;
        }

        return $indexCache[$columnIndex];
    }

    
    public static function extractAllCellReferencesInRange($cellRange): array
    {
        [$ranges, $operators] = self::getCellBlocksFromRangeString($cellRange);

        $cells = [];
        foreach ($ranges as $range) {
            $cells[] = self::getReferencesForCellBlock($range);
        }

        $cells = self::processRangeSetOperators($operators, $cells);

        if (empty($cells)) {
            return [];
        }

        $cellList = array_merge(...$cells);
        $cellList = self::sortCellReferenceArray($cellList);

        return $cellList;
    }

    private static function processRangeSetOperators(array $operators, array $cells): array
    {
        for ($offset = 0; $offset < count($operators); ++$offset) {
            $operator = $operators[$offset];
            if ($operator !== ' ') {
                continue;
            }

            $cells[$offset] = array_intersect($cells[$offset], $cells[$offset + 1]);
            unset($operators[$offset], $cells[$offset + 1]);
            $operators = array_values($operators);
            $cells = array_values($cells);
            --$offset;
        }

        return $cells;
    }

    private static function sortCellReferenceArray(array $cellList): array
    {
        
        $sortKeys = [];
        foreach ($cellList as $coord) {
            [$column, $row] = sscanf($coord, '%[A-Z]%d');
            $sortKeys[sprintf('%3s%09d', $column, $row)] = $coord;
        }
        ksort($sortKeys);

        return array_values($sortKeys);
    }

    
    private static function getReferencesForCellBlock($cellBlock)
    {
        $returnValue = [];

        
        if (!self::coordinateIsRange($cellBlock)) {
            return (array) $cellBlock;
        }

        
        $ranges = self::splitRange($cellBlock);
        foreach ($ranges as $range) {
            
            if (!isset($range[1])) {
                $returnValue[] = $range[0];

                continue;
            }

            
            [$rangeStart, $rangeEnd] = $range;
            [$startColumn, $startRow] = self::coordinateFromString($rangeStart);
            [$endColumn, $endRow] = self::coordinateFromString($rangeEnd);
            $startColumnIndex = self::columnIndexFromString($startColumn);
            $endColumnIndex = self::columnIndexFromString($endColumn);
            ++$endColumnIndex;

            
            $currentColumnIndex = $startColumnIndex;
            $currentRow = $startRow;

            self::validateRange($cellBlock, $startColumnIndex, $endColumnIndex, $currentRow, $endRow);

            
            while ($currentColumnIndex < $endColumnIndex) {
                while ($currentRow <= $endRow) {
                    $returnValue[] = self::stringFromColumnIndex($currentColumnIndex) . $currentRow;
                    ++$currentRow;
                }
                ++$currentColumnIndex;
                $currentRow = $startRow;
            }
        }

        return $returnValue;
    }

    
    public static function mergeRangesInCollection(array $pCoordCollection)
    {
        $hashedValues = [];
        $mergedCoordCollection = [];

        foreach ($pCoordCollection as $coord => $value) {
            if (self::coordinateIsRange($coord)) {
                $mergedCoordCollection[$coord] = $value;

                continue;
            }

            [$column, $row] = self::coordinateFromString($coord);
            $row = (int) (ltrim($row, '$'));
            $hashCode = $column . '-' . (is_object($value) ? $value->getHashCode() : $value);

            if (!isset($hashedValues[$hashCode])) {
                $hashedValues[$hashCode] = (object) [
                    'value' => $value,
                    'col' => $column,
                    'rows' => [$row],
                ];
            } else {
                $hashedValues[$hashCode]->rows[] = $row;
            }
        }

        ksort($hashedValues);

        foreach ($hashedValues as $hashedValue) {
            sort($hashedValue->rows);
            $rowStart = null;
            $rowEnd = null;
            $ranges = [];

            foreach ($hashedValue->rows as $row) {
                if ($rowStart === null) {
                    $rowStart = $row;
                    $rowEnd = $row;
                } elseif ($rowEnd === $row - 1) {
                    $rowEnd = $row;
                } else {
                    if ($rowStart == $rowEnd) {
                        $ranges[] = $hashedValue->col . $rowStart;
                    } else {
                        $ranges[] = $hashedValue->col . $rowStart . ':' . $hashedValue->col . $rowEnd;
                    }

                    $rowStart = $row;
                    $rowEnd = $row;
                }
            }

            if ($rowStart !== null) {
                if ($rowStart == $rowEnd) {
                    $ranges[] = $hashedValue->col . $rowStart;
                } else {
                    $ranges[] = $hashedValue->col . $rowStart . ':' . $hashedValue->col . $rowEnd;
                }
            }

            foreach ($ranges as $range) {
                $mergedCoordCollection[$range] = $hashedValue->value;
            }
        }

        return $mergedCoordCollection;
    }

    
    private static function getCellBlocksFromRangeString($rangeString)
    {
        $rangeString = str_replace('$', '', strtoupper($rangeString));

        
        $tokens = preg_split('/([ ,])/', $rangeString, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        $split = array_chunk($tokens, 2);
        $ranges = array_column($split, 0);
        $operators = array_column($split, 1);

        return [$ranges, $operators];
    }

    
    private static function validateRange($cellBlock, $startColumnIndex, $endColumnIndex, $currentRow, $endRow): void
    {
        if ($startColumnIndex >= $endColumnIndex || $currentRow > $endRow) {
            throw new Exception('Invalid range: "' . $cellBlock . '"');
        }
    }
}
