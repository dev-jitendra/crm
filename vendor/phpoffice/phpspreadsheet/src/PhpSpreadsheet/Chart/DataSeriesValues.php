<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataSeriesValues
{
    const DATASERIES_TYPE_STRING = 'String';
    const DATASERIES_TYPE_NUMBER = 'Number';

    private static $dataTypeValues = [
        self::DATASERIES_TYPE_STRING,
        self::DATASERIES_TYPE_NUMBER,
    ];

    
    private $dataType;

    
    private $dataSource;

    
    private $formatCode;

    
    private $pointMarker;

    
    private $pointCount = 0;

    
    private $dataValues = [];

    
    private $fillColor;

    
    private $lineWidth = 12700;

    
    public function __construct($dataType = self::DATASERIES_TYPE_NUMBER, $dataSource = null, $formatCode = null, $pointCount = 0, $dataValues = [], $marker = null, $fillColor = null)
    {
        $this->setDataType($dataType);
        $this->dataSource = $dataSource;
        $this->formatCode = $formatCode;
        $this->pointCount = $pointCount;
        $this->dataValues = $dataValues;
        $this->pointMarker = $marker;
        $this->fillColor = $fillColor;
    }

    
    public function getDataType()
    {
        return $this->dataType;
    }

    
    public function setDataType($dataType)
    {
        if (!in_array($dataType, self::$dataTypeValues)) {
            throw new Exception('Invalid datatype for chart data series values');
        }
        $this->dataType = $dataType;

        return $this;
    }

    
    public function getDataSource()
    {
        return $this->dataSource;
    }

    
    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    
    public function getPointMarker()
    {
        return $this->pointMarker;
    }

    
    public function setPointMarker($marker)
    {
        $this->pointMarker = $marker;

        return $this;
    }

    
    public function getFormatCode()
    {
        return $this->formatCode;
    }

    
    public function setFormatCode($formatCode)
    {
        $this->formatCode = $formatCode;

        return $this;
    }

    
    public function getPointCount()
    {
        return $this->pointCount;
    }

    
    public function getFillColor()
    {
        return $this->fillColor;
    }

    
    public function setFillColor($color)
    {
        if (is_array($color)) {
            foreach ($color as $colorValue) {
                $this->validateColor($colorValue);
            }
        } else {
            $this->validateColor($color);
        }
        $this->fillColor = $color;

        return $this;
    }

    
    private function validateColor($color)
    {
        if (!preg_match('/^[a-f0-9]{6}$/i', $color)) {
            throw new Exception(sprintf('Invalid hex color for chart series (color: "%s")', $color));
        }

        return true;
    }

    
    public function getLineWidth()
    {
        return $this->lineWidth;
    }

    
    public function setLineWidth($width)
    {
        $minWidth = 12700;
        $this->lineWidth = max($minWidth, $width);

        return $this;
    }

    
    public function isMultiLevelSeries()
    {
        if (count($this->dataValues) > 0) {
            return is_array(array_values($this->dataValues)[0]);
        }

        return null;
    }

    
    public function multiLevelCount()
    {
        $levelCount = 0;
        foreach ($this->dataValues as $dataValueSet) {
            $levelCount = max($levelCount, count($dataValueSet));
        }

        return $levelCount;
    }

    
    public function getDataValues()
    {
        return $this->dataValues;
    }

    
    public function getDataValue()
    {
        $count = count($this->dataValues);
        if ($count == 0) {
            return null;
        } elseif ($count == 1) {
            return $this->dataValues[0];
        }

        return $this->dataValues;
    }

    
    public function setDataValues($dataValues)
    {
        $this->dataValues = Functions::flattenArray($dataValues);
        $this->pointCount = count($dataValues);

        return $this;
    }

    public function refresh(Worksheet $worksheet, $flatten = true): void
    {
        if ($this->dataSource !== null) {
            $calcEngine = Calculation::getInstance($worksheet->getParent());
            $newDataValues = Calculation::unwrapResult(
                $calcEngine->_calculateFormulaValue(
                    '=' . $this->dataSource,
                    null,
                    $worksheet->getCell('A1')
                )
            );
            if ($flatten) {
                $this->dataValues = Functions::flattenArray($newDataValues);
                foreach ($this->dataValues as &$dataValue) {
                    if (is_string($dataValue) && !empty($dataValue) && $dataValue[0] == '#') {
                        $dataValue = 0.0;
                    }
                }
                unset($dataValue);
            } else {
                [$worksheet, $cellRange] = Worksheet::extractSheetTitle($this->dataSource, true);
                $dimensions = Coordinate::rangeDimension(str_replace('$', '', $cellRange));
                if (($dimensions[0] == 1) || ($dimensions[1] == 1)) {
                    $this->dataValues = Functions::flattenArray($newDataValues);
                } else {
                    $newArray = array_values(array_shift($newDataValues));
                    foreach ($newArray as $i => $newDataSet) {
                        $newArray[$i] = [$newDataSet];
                    }

                    foreach ($newDataValues as $newDataSet) {
                        $i = 0;
                        foreach ($newDataSet as $newDataVal) {
                            array_unshift($newArray[$i++], $newDataVal);
                        }
                    }
                    $this->dataValues = $newArray;
                }
            }
            $this->pointCount = count($this->dataValues);
        }
    }
}
