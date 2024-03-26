<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Cell\Cell;

class Functions
{
    const PRECISION = 8.88E-016;

    
    const M_2DIVPI = 0.63661977236758134307553505349006;

    
    const COMPATIBILITY_EXCEL = 'Excel';
    const COMPATIBILITY_GNUMERIC = 'Gnumeric';
    const COMPATIBILITY_OPENOFFICE = 'OpenOfficeCalc';

    const RETURNDATE_PHP_NUMERIC = 'P';
    const RETURNDATE_UNIX_TIMESTAMP = 'P';
    const RETURNDATE_PHP_OBJECT = 'O';
    const RETURNDATE_PHP_DATETIME_OBJECT = 'O';
    const RETURNDATE_EXCEL = 'E';

    
    protected static $compatibilityMode = self::COMPATIBILITY_EXCEL;

    
    protected static $returnDateType = self::RETURNDATE_EXCEL;

    
    protected static $errorCodes = [
        'null' => '#NULL!',
        'divisionbyzero' => '#DIV/0!',
        'value' => '#VALUE!',
        'reference' => '#REF!',
        'name' => '#NAME?',
        'num' => '#NUM!',
        'na' => '#N/A',
        'gettingdata' => '#GETTING_DATA',
    ];

    
    public static function setCompatibilityMode($compatibilityMode)
    {
        if (
            ($compatibilityMode == self::COMPATIBILITY_EXCEL) ||
            ($compatibilityMode == self::COMPATIBILITY_GNUMERIC) ||
            ($compatibilityMode == self::COMPATIBILITY_OPENOFFICE)
        ) {
            self::$compatibilityMode = $compatibilityMode;

            return true;
        }

        return false;
    }

    
    public static function getCompatibilityMode()
    {
        return self::$compatibilityMode;
    }

    
    public static function setReturnDateType($returnDateType)
    {
        if (
            ($returnDateType == self::RETURNDATE_UNIX_TIMESTAMP) ||
            ($returnDateType == self::RETURNDATE_PHP_DATETIME_OBJECT) ||
            ($returnDateType == self::RETURNDATE_EXCEL)
        ) {
            self::$returnDateType = $returnDateType;

            return true;
        }

        return false;
    }

    
    public static function getReturnDateType()
    {
        return self::$returnDateType;
    }

    
    public static function DUMMY()
    {
        return '#Not Yet Implemented';
    }

    
    public static function DIV0()
    {
        return self::$errorCodes['divisionbyzero'];
    }

    
    public static function NA()
    {
        return self::$errorCodes['na'];
    }

    
    public static function NAN()
    {
        return self::$errorCodes['num'];
    }

    
    public static function NAME()
    {
        return self::$errorCodes['name'];
    }

    
    public static function REF()
    {
        return self::$errorCodes['reference'];
    }

    
    public static function null()
    {
        return self::$errorCodes['null'];
    }

    
    public static function VALUE()
    {
        return self::$errorCodes['value'];
    }

    public static function isMatrixValue($idx)
    {
        return (substr_count($idx, '.') <= 1) || (preg_match('/\.[A-Z]/', $idx) > 0);
    }

    public static function isValue($idx)
    {
        return substr_count($idx, '.') == 0;
    }

    public static function isCellValue($idx)
    {
        return substr_count($idx, '.') > 1;
    }

    public static function ifCondition($condition)
    {
        $condition = self::flattenSingleValue($condition);

        if ($condition === '') {
            $condition = '=""';
        }

        if (!is_string($condition) || !in_array($condition[0], ['>', '<', '='])) {
            if (!is_numeric($condition)) {
                $condition = Calculation::wrapResult(strtoupper($condition));
            }

            return str_replace('""""', '""', '=' . $condition);
        }
        preg_match('/(=|<[>=]?|>=?)(.*)/', $condition, $matches);
        [, $operator, $operand] = $matches;

        if (is_numeric(trim($operand, '"'))) {
            $operand = trim($operand, '"');
        } elseif (!is_numeric($operand)) {
            $operand = str_replace('"', '""', $operand);
            $operand = Calculation::wrapResult(strtoupper($operand));
        }

        return str_replace('""""', '""', $operator . $operand);
    }

    
    public static function errorType($value = '')
    {
        $value = self::flattenSingleValue($value);

        $i = 1;
        foreach (self::$errorCodes as $errorCode) {
            if ($value === $errorCode) {
                return $i;
            }
            ++$i;
        }

        return self::NA();
    }

    
    public static function isBlank($value = null)
    {
        if ($value !== null) {
            $value = self::flattenSingleValue($value);
        }

        return $value === null;
    }

    
    public static function isErr($value = '')
    {
        $value = self::flattenSingleValue($value);

        return self::isError($value) && (!self::isNa(($value)));
    }

    
    public static function isError($value = '')
    {
        $value = self::flattenSingleValue($value);

        if (!is_string($value)) {
            return false;
        }

        return in_array($value, self::$errorCodes);
    }

    
    public static function isNa($value = '')
    {
        $value = self::flattenSingleValue($value);

        return $value === self::NA();
    }

    
    public static function isEven($value = null)
    {
        $value = self::flattenSingleValue($value);

        if ($value === null) {
            return self::NAME();
        } elseif ((is_bool($value)) || ((is_string($value)) && (!is_numeric($value)))) {
            return self::VALUE();
        }

        return $value % 2 == 0;
    }

    
    public static function isOdd($value = null)
    {
        $value = self::flattenSingleValue($value);

        if ($value === null) {
            return self::NAME();
        } elseif ((is_bool($value)) || ((is_string($value)) && (!is_numeric($value)))) {
            return self::VALUE();
        }

        return abs($value) % 2 == 1;
    }

    
    public static function isNumber($value = null)
    {
        $value = self::flattenSingleValue($value);

        if (is_string($value)) {
            return false;
        }

        return is_numeric($value);
    }

    
    public static function isLogical($value = null)
    {
        $value = self::flattenSingleValue($value);

        return is_bool($value);
    }

    
    public static function isText($value = null)
    {
        $value = self::flattenSingleValue($value);

        return is_string($value) && !self::isError($value);
    }

    
    public static function isNonText($value = null)
    {
        return !self::isText($value);
    }

    
    public static function n($value = null)
    {
        while (is_array($value)) {
            $value = array_shift($value);
        }

        switch (gettype($value)) {
            case 'double':
            case 'float':
            case 'integer':
                return $value;
            case 'boolean':
                return (int) $value;
            case 'string':
                
                if ((strlen($value) > 0) && ($value[0] == '#')) {
                    return $value;
                }

                break;
        }

        return 0;
    }

    
    public static function TYPE($value = null)
    {
        $value = self::flattenArrayIndexed($value);
        if (is_array($value) && (count($value) > 1)) {
            end($value);
            $a = key($value);
            
            if (self::isCellValue($a)) {
                return 16;
            
            } elseif (self::isMatrixValue($a)) {
                return 64;
            }
        } elseif (empty($value)) {
            
            return 1;
        }
        $value = self::flattenSingleValue($value);

        if (($value === null) || (is_float($value)) || (is_int($value))) {
            return 1;
        } elseif (is_bool($value)) {
            return 4;
        } elseif (is_array($value)) {
            return 64;
        } elseif (is_string($value)) {
            
            if ((strlen($value) > 0) && ($value[0] == '#')) {
                return 16;
            }

            return 2;
        }

        return 0;
    }

    
    public static function flattenArray($array)
    {
        if (!is_array($array)) {
            return (array) $array;
        }

        $arrayValues = [];
        foreach ($array as $value) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    if (is_array($val)) {
                        foreach ($val as $v) {
                            $arrayValues[] = $v;
                        }
                    } else {
                        $arrayValues[] = $val;
                    }
                }
            } else {
                $arrayValues[] = $value;
            }
        }

        return $arrayValues;
    }

    
    public static function flattenArrayIndexed($array)
    {
        if (!is_array($array)) {
            return (array) $array;
        }

        $arrayValues = [];
        foreach ($array as $k1 => $value) {
            if (is_array($value)) {
                foreach ($value as $k2 => $val) {
                    if (is_array($val)) {
                        foreach ($val as $k3 => $v) {
                            $arrayValues[$k1 . '.' . $k2 . '.' . $k3] = $v;
                        }
                    } else {
                        $arrayValues[$k1 . '.' . $k2] = $val;
                    }
                }
            } else {
                $arrayValues[$k1] = $value;
            }
        }

        return $arrayValues;
    }

    
    public static function flattenSingleValue($value = '')
    {
        while (is_array($value)) {
            $value = array_shift($value);
        }

        return $value;
    }

    
    public static function isFormula($cellReference = '', ?Cell $pCell = null)
    {
        if ($pCell === null) {
            return self::REF();
        }

        preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $cellReference, $matches);

        $cellReference = $matches[6] . $matches[7];
        $worksheetName = str_replace("''", "'", trim($matches[2], "'"));

        $worksheet = (!empty($worksheetName))
            ? $pCell->getWorksheet()->getParent()->getSheetByName($worksheetName)
            : $pCell->getWorksheet();

        return $worksheet->getCell($cellReference)->isFormula();
    }
}
