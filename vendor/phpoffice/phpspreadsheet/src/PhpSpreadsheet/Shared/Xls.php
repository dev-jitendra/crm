<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Xls
{
    
    public static function sizeCol($sheet, $col = 'A')
    {
        
        $font = $sheet->getParent()->getDefaultStyle()->getFont();

        $columnDimensions = $sheet->getColumnDimensions();

        
        if (isset($columnDimensions[$col]) && $columnDimensions[$col]->getWidth() != -1) {
            
            $columnDimension = $columnDimensions[$col];
            $width = $columnDimension->getWidth();
            $pixelWidth = Drawing::cellDimensionToPixels($width, $font);
        } elseif ($sheet->getDefaultColumnDimension()->getWidth() != -1) {
            
            $defaultColumnDimension = $sheet->getDefaultColumnDimension();
            $width = $defaultColumnDimension->getWidth();
            $pixelWidth = Drawing::cellDimensionToPixels($width, $font);
        } else {
            
            $pixelWidth = Font::getDefaultColumnWidthByFont($font, true);
        }

        
        if (isset($columnDimensions[$col]) && !$columnDimensions[$col]->getVisible()) {
            $effectivePixelWidth = 0;
        } else {
            $effectivePixelWidth = $pixelWidth;
        }

        return $effectivePixelWidth;
    }

    
    public static function sizeRow($sheet, $row = 1)
    {
        
        $font = $sheet->getParent()->getDefaultStyle()->getFont();

        $rowDimensions = $sheet->getRowDimensions();

        
        if (isset($rowDimensions[$row]) && $rowDimensions[$row]->getRowHeight() != -1) {
            
            $rowDimension = $rowDimensions[$row];
            $rowHeight = $rowDimension->getRowHeight();
            $pixelRowHeight = (int) ceil(4 * $rowHeight / 3); 
        } elseif ($sheet->getDefaultRowDimension()->getRowHeight() != -1) {
            
            $defaultRowDimension = $sheet->getDefaultRowDimension();
            $rowHeight = $defaultRowDimension->getRowHeight();
            $pixelRowHeight = Drawing::pointsToPixels($rowHeight);
        } else {
            
            $pointRowHeight = Font::getDefaultRowHeightByFont($font);
            $pixelRowHeight = Font::fontSizeToPixels($pointRowHeight);
        }

        
        if (isset($rowDimensions[$row]) && !$rowDimensions[$row]->getVisible()) {
            $effectivePixelRowHeight = 0;
        } else {
            $effectivePixelRowHeight = $pixelRowHeight;
        }

        return $effectivePixelRowHeight;
    }

    
    public static function getDistanceX(Worksheet $sheet, $startColumn = 'A', $startOffsetX = 0, $endColumn = 'A', $endOffsetX = 0)
    {
        $distanceX = 0;

        
        $startColumnIndex = Coordinate::columnIndexFromString($startColumn);
        $endColumnIndex = Coordinate::columnIndexFromString($endColumn);
        for ($i = $startColumnIndex; $i <= $endColumnIndex; ++$i) {
            $distanceX += self::sizeCol($sheet, Coordinate::stringFromColumnIndex($i));
        }

        
        $distanceX -= (int) floor(self::sizeCol($sheet, $startColumn) * $startOffsetX / 1024);

        
        $distanceX -= (int) floor(self::sizeCol($sheet, $endColumn) * (1 - $endOffsetX / 1024));

        return $distanceX;
    }

    
    public static function getDistanceY(Worksheet $sheet, $startRow = 1, $startOffsetY = 0, $endRow = 1, $endOffsetY = 0)
    {
        $distanceY = 0;

        
        for ($row = $startRow; $row <= $endRow; ++$row) {
            $distanceY += self::sizeRow($sheet, $row);
        }

        
        $distanceY -= (int) floor(self::sizeRow($sheet, $startRow) * $startOffsetY / 256);

        
        $distanceY -= (int) floor(self::sizeRow($sheet, $endRow) * (1 - $endOffsetY / 256));

        return $distanceY;
    }

    
    public static function oneAnchor2twoAnchor($sheet, $coordinates, $offsetX, $offsetY, $width, $height)
    {
        [$column, $row] = Coordinate::coordinateFromString($coordinates);
        $col_start = Coordinate::columnIndexFromString($column);
        $row_start = $row - 1;

        $x1 = $offsetX;
        $y1 = $offsetY;

        
        $col_end = $col_start; 
        $row_end = $row_start; 

        
        if ($x1 >= self::sizeCol($sheet, Coordinate::stringFromColumnIndex($col_start))) {
            $x1 = 0;
        }
        if ($y1 >= self::sizeRow($sheet, $row_start + 1)) {
            $y1 = 0;
        }

        $width = $width + $x1 - 1;
        $height = $height + $y1 - 1;

        
        while ($width >= self::sizeCol($sheet, Coordinate::stringFromColumnIndex($col_end))) {
            $width -= self::sizeCol($sheet, Coordinate::stringFromColumnIndex($col_end));
            ++$col_end;
        }

        
        while ($height >= self::sizeRow($sheet, $row_end + 1)) {
            $height -= self::sizeRow($sheet, $row_end + 1);
            ++$row_end;
        }

        
        
        if (self::sizeCol($sheet, Coordinate::stringFromColumnIndex($col_start)) == 0) {
            return;
        }
        if (self::sizeCol($sheet, Coordinate::stringFromColumnIndex($col_end)) == 0) {
            return;
        }
        if (self::sizeRow($sheet, $row_start + 1) == 0) {
            return;
        }
        if (self::sizeRow($sheet, $row_end + 1) == 0) {
            return;
        }

        
        $x1 = $x1 / self::sizeCol($sheet, Coordinate::stringFromColumnIndex($col_start)) * 1024;
        $y1 = $y1 / self::sizeRow($sheet, $row_start + 1) * 256;
        $x2 = ($width + 1) / self::sizeCol($sheet, Coordinate::stringFromColumnIndex($col_end)) * 1024; 
        $y2 = ($height + 1) / self::sizeRow($sheet, $row_end + 1) * 256; 

        $startCoordinates = Coordinate::stringFromColumnIndex($col_start) . ($row_start + 1);
        $endCoordinates = Coordinate::stringFromColumnIndex($col_end) . ($row_end + 1);

        return [
            'startCoordinates' => $startCoordinates,
            'startOffsetX' => $x1,
            'startOffsetY' => $y1,
            'endCoordinates' => $endCoordinates,
            'endOffsetX' => $x2,
            'endOffsetY' => $y2,
        ];
    }
}
