<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Collection\Cells;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Cell
{
    
    private static $valueBinder;

    
    private $value;

    
    private $calculatedValue;

    
    private $dataType;

    
    private $parent;

    
    private $xfIndex = 0;

    
    private $formulaAttributes;

    
    public function updateInCollection()
    {
        $this->parent->update($this);

        return $this;
    }

    public function detach(): void
    {
        $this->parent = null;
    }

    public function attach(Cells $parent): void
    {
        $this->parent = $parent;
    }

    
    public function __construct($pValue, $pDataType, Worksheet $pSheet)
    {
        
        $this->value = $pValue;

        
        $this->parent = $pSheet->getCellCollection();

        
        if ($pDataType !== null) {
            if ($pDataType == DataType::TYPE_STRING2) {
                $pDataType = DataType::TYPE_STRING;
            }
            $this->dataType = $pDataType;
        } elseif (!self::getValueBinder()->bindValue($this, $pValue)) {
            throw new Exception('Value could not be bound to cell.');
        }
    }

    
    public function getColumn()
    {
        return $this->parent->getCurrentColumn();
    }

    
    public function getRow()
    {
        return $this->parent->getCurrentRow();
    }

    
    public function getCoordinate()
    {
        return $this->parent->getCurrentCoordinate();
    }

    
    public function getValue()
    {
        return $this->value;
    }

    
    public function getFormattedValue()
    {
        return (string) NumberFormat::toFormattedString(
            $this->getCalculatedValue(),
            $this->getStyle()
                ->getNumberFormat()->getFormatCode()
        );
    }

    
    public function setValue($pValue)
    {
        if (!self::getValueBinder()->bindValue($this, $pValue)) {
            throw new Exception('Value could not be bound to cell.');
        }

        return $this;
    }

    
    public function setValueExplicit($pValue, $pDataType)
    {
        
        switch ($pDataType) {
            case DataType::TYPE_NULL:
                $this->value = $pValue;

                break;
            case DataType::TYPE_STRING2:
                $pDataType = DataType::TYPE_STRING;
                
            case DataType::TYPE_STRING:
                
            case DataType::TYPE_INLINE:
                
                $this->value = DataType::checkString($pValue);

                break;
            case DataType::TYPE_NUMERIC:
                if (is_string($pValue) && !is_numeric($pValue)) {
                    throw new Exception('Invalid numeric value for datatype Numeric');
                }
                $this->value = 0 + $pValue;

                break;
            case DataType::TYPE_FORMULA:
                $this->value = (string) $pValue;

                break;
            case DataType::TYPE_BOOL:
                $this->value = (bool) $pValue;

                break;
            case DataType::TYPE_ERROR:
                $this->value = DataType::checkErrorCode($pValue);

                break;
            default:
                throw new Exception('Invalid datatype: ' . $pDataType);

                break;
        }

        
        $this->dataType = $pDataType;

        return $this->updateInCollection();
    }

    
    public function getCalculatedValue($resetLog = true)
    {
        if ($this->dataType == DataType::TYPE_FORMULA) {
            try {
                $index = $this->getWorksheet()->getParent()->getActiveSheetIndex();
                $result = Calculation::getInstance(
                    $this->getWorksheet()->getParent()
                )->calculateCellValue($this, $resetLog);
                $this->getWorksheet()->getParent()->setActiveSheetIndex($index);
                
                if (is_array($result)) {
                    while (is_array($result)) {
                        $result = array_shift($result);
                    }
                }
            } catch (Exception $ex) {
                if (($ex->getMessage() === 'Unable to access External Workbook') && ($this->calculatedValue !== null)) {
                    return $this->calculatedValue; 
                } elseif (strpos($ex->getMessage(), 'undefined name') !== false) {
                    return \PhpOffice\PhpSpreadsheet\Calculation\Functions::NAME();
                }

                throw new \PhpOffice\PhpSpreadsheet\Calculation\Exception(
                    $this->getWorksheet()->getTitle() . '!' . $this->getCoordinate() . ' -> ' . $ex->getMessage()
                );
            }

            if ($result === '#Not Yet Implemented') {
                return $this->calculatedValue; 
            }

            return $result;
        } elseif ($this->value instanceof RichText) {
            return $this->value->getPlainText();
        }

        return $this->value;
    }

    
    public function setCalculatedValue($pValue)
    {
        if ($pValue !== null) {
            $this->calculatedValue = (is_numeric($pValue)) ? (float) $pValue : $pValue;
        }

        return $this->updateInCollection();
    }

    
    public function getOldCalculatedValue()
    {
        return $this->calculatedValue;
    }

    
    public function getDataType()
    {
        return $this->dataType;
    }

    
    public function setDataType($pDataType)
    {
        if ($pDataType == DataType::TYPE_STRING2) {
            $pDataType = DataType::TYPE_STRING;
        }
        $this->dataType = $pDataType;

        return $this->updateInCollection();
    }

    
    public function isFormula()
    {
        return $this->dataType == DataType::TYPE_FORMULA;
    }

    
    public function hasDataValidation()
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot check for data validation when cell is not bound to a worksheet');
        }

        return $this->getWorksheet()->dataValidationExists($this->getCoordinate());
    }

    
    public function getDataValidation()
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot get data validation for cell that is not bound to a worksheet');
        }

        return $this->getWorksheet()->getDataValidation($this->getCoordinate());
    }

    
    public function setDataValidation(?DataValidation $pDataValidation = null)
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot set data validation for cell that is not bound to a worksheet');
        }

        $this->getWorksheet()->setDataValidation($this->getCoordinate(), $pDataValidation);

        return $this->updateInCollection();
    }

    
    public function hasValidValue()
    {
        $validator = new DataValidator();

        return $validator->isValid($this);
    }

    
    public function hasHyperlink()
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot check for hyperlink when cell is not bound to a worksheet');
        }

        return $this->getWorksheet()->hyperlinkExists($this->getCoordinate());
    }

    
    public function getHyperlink()
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot get hyperlink for cell that is not bound to a worksheet');
        }

        return $this->getWorksheet()->getHyperlink($this->getCoordinate());
    }

    
    public function setHyperlink(?Hyperlink $pHyperlink = null)
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot set hyperlink for cell that is not bound to a worksheet');
        }

        $this->getWorksheet()->setHyperlink($this->getCoordinate(), $pHyperlink);

        return $this->updateInCollection();
    }

    
    public function getParent()
    {
        return $this->parent;
    }

    
    public function getWorksheet()
    {
        return $this->parent->getParent();
    }

    
    public function isInMergeRange()
    {
        return (bool) $this->getMergeRange();
    }

    
    public function isMergeRangeValueCell()
    {
        if ($mergeRange = $this->getMergeRange()) {
            $mergeRange = Coordinate::splitRange($mergeRange);
            [$startCell] = $mergeRange[0];
            if ($this->getCoordinate() === $startCell) {
                return true;
            }
        }

        return false;
    }

    
    public function getMergeRange()
    {
        foreach ($this->getWorksheet()->getMergeCells() as $mergeRange) {
            if ($this->isInRange($mergeRange)) {
                return $mergeRange;
            }
        }

        return false;
    }

    
    public function getStyle()
    {
        return $this->getWorksheet()->getStyle($this->getCoordinate());
    }

    
    public function rebindParent(Worksheet $parent)
    {
        $this->parent = $parent->getCellCollection();

        return $this->updateInCollection();
    }

    
    public function isInRange($pRange)
    {
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($pRange);

        
        $myColumn = Coordinate::columnIndexFromString($this->getColumn());
        $myRow = $this->getRow();

        
        return ($rangeStart[0] <= $myColumn) && ($rangeEnd[0] >= $myColumn) &&
                ($rangeStart[1] <= $myRow) && ($rangeEnd[1] >= $myRow);
    }

    
    public static function compareCells(self $a, self $b)
    {
        if ($a->getRow() < $b->getRow()) {
            return -1;
        } elseif ($a->getRow() > $b->getRow()) {
            return 1;
        } elseif (Coordinate::columnIndexFromString($a->getColumn()) < Coordinate::columnIndexFromString($b->getColumn())) {
            return -1;
        }

        return 1;
    }

    
    public static function getValueBinder()
    {
        if (self::$valueBinder === null) {
            self::$valueBinder = new DefaultValueBinder();
        }

        return self::$valueBinder;
    }

    
    public static function setValueBinder(IValueBinder $binder): void
    {
        self::$valueBinder = $binder;
    }

    
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ((is_object($value)) && ($key != 'parent')) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }

    
    public function getXfIndex()
    {
        return $this->xfIndex;
    }

    
    public function setXfIndex($pValue)
    {
        $this->xfIndex = $pValue;

        return $this->updateInCollection();
    }

    
    public function setFormulaAttributes($pAttributes)
    {
        $this->formulaAttributes = $pAttributes;

        return $this;
    }

    
    public function getFormulaAttributes()
    {
        return $this->formulaAttributes;
    }

    
    public function __toString()
    {
        return (string) $this->getValue();
    }
}
