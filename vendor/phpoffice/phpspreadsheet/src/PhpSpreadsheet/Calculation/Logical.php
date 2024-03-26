<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

class Logical
{
    
    public static function true()
    {
        return true;
    }

    
    public static function false()
    {
        return false;
    }

    private static function countTrueValues(array $args)
    {
        $returnValue = 0;

        foreach ($args as $arg) {
            
            if (is_bool($arg)) {
                $returnValue += $arg;
            } elseif ((is_numeric($arg)) && (!is_string($arg))) {
                $returnValue += ((int) $arg != 0);
            } elseif (is_string($arg)) {
                $arg = strtoupper($arg);
                if (($arg == 'TRUE') || ($arg == Calculation::getTRUE())) {
                    $arg = true;
                } elseif (($arg == 'FALSE') || ($arg == Calculation::getFALSE())) {
                    $arg = false;
                } else {
                    return Functions::VALUE();
                }
                $returnValue += ($arg != 0);
            }
        }

        return $returnValue;
    }

    
    public static function logicalAnd(...$args)
    {
        $args = Functions::flattenArray($args);

        if (count($args) == 0) {
            return Functions::VALUE();
        }

        $args = array_filter($args, function ($value) {
            return $value !== null || (is_string($value) && trim($value) == '');
        });
        $argCount = count($args);

        $returnValue = self::countTrueValues($args);
        if (is_string($returnValue)) {
            return $returnValue;
        }

        return ($returnValue > 0) && ($returnValue == $argCount);
    }

    
    public static function logicalOr(...$args)
    {
        $args = Functions::flattenArray($args);

        if (count($args) == 0) {
            return Functions::VALUE();
        }

        $args = array_filter($args, function ($value) {
            return $value !== null || (is_string($value) && trim($value) == '');
        });

        $returnValue = self::countTrueValues($args);
        if (is_string($returnValue)) {
            return $returnValue;
        }

        return $returnValue > 0;
    }

    
    public static function logicalXor(...$args)
    {
        $args = Functions::flattenArray($args);

        if (count($args) == 0) {
            return Functions::VALUE();
        }

        $args = array_filter($args, function ($value) {
            return $value !== null || (is_string($value) && trim($value) == '');
        });

        $returnValue = self::countTrueValues($args);
        if (is_string($returnValue)) {
            return $returnValue;
        }

        return $returnValue % 2 == 1;
    }

    
    public static function NOT($logical = false)
    {
        $logical = Functions::flattenSingleValue($logical);

        if (is_string($logical)) {
            $logical = strtoupper($logical);
            if (($logical == 'TRUE') || ($logical == Calculation::getTRUE())) {
                return false;
            } elseif (($logical == 'FALSE') || ($logical == Calculation::getFALSE())) {
                return true;
            }

            return Functions::VALUE();
        }

        return !$logical;
    }

    
    public static function statementIf($condition = true, $returnIfTrue = 0, $returnIfFalse = false)
    {
        if (Functions::isError($condition)) {
            return $condition;
        }

        $condition = ($condition === null) ? true : (bool) Functions::flattenSingleValue($condition);
        $returnIfTrue = ($returnIfTrue === null) ? 0 : Functions::flattenSingleValue($returnIfTrue);
        $returnIfFalse = ($returnIfFalse === null) ? false : Functions::flattenSingleValue($returnIfFalse);

        return ($condition) ? $returnIfTrue : $returnIfFalse;
    }

    
    public static function statementSwitch(...$arguments)
    {
        $result = Functions::VALUE();

        if (count($arguments) > 0) {
            $targetValue = Functions::flattenSingleValue($arguments[0]);
            $argc = count($arguments) - 1;
            $switchCount = floor($argc / 2);
            $switchSatisfied = false;
            $hasDefaultClause = $argc % 2 !== 0;
            $defaultClause = $argc % 2 === 0 ? null : $arguments[count($arguments) - 1];

            if ($switchCount) {
                for ($index = 0; $index < $switchCount; ++$index) {
                    if ($targetValue == $arguments[$index * 2 + 1]) {
                        $result = $arguments[$index * 2 + 2];
                        $switchSatisfied = true;

                        break;
                    }
                }
            }

            if (!$switchSatisfied) {
                $result = $hasDefaultClause ? $defaultClause : Functions::NA();
            }
        }

        return $result;
    }

    
    public static function IFERROR($testValue = '', $errorpart = '')
    {
        $testValue = ($testValue === null) ? '' : Functions::flattenSingleValue($testValue);
        $errorpart = ($errorpart === null) ? '' : Functions::flattenSingleValue($errorpart);

        return self::statementIf(Functions::isError($testValue), $errorpart, $testValue);
    }

    
    public static function IFNA($testValue = '', $napart = '')
    {
        $testValue = ($testValue === null) ? '' : Functions::flattenSingleValue($testValue);
        $napart = ($napart === null) ? '' : Functions::flattenSingleValue($napart);

        return self::statementIf(Functions::isNa($testValue), $napart, $testValue);
    }

    
    public static function IFS(...$arguments)
    {
        if (count($arguments) % 2 != 0) {
            return Functions::NA();
        }
        
        $falseValueException = new Exception();
        for ($i = 0; $i < count($arguments); $i += 2) {
            $testValue = ($arguments[$i] === null) ? '' : Functions::flattenSingleValue($arguments[$i]);
            $returnIfTrue = ($arguments[$i + 1] === null) ? '' : Functions::flattenSingleValue($arguments[$i + 1]);
            $result = self::statementIf($testValue, $returnIfTrue, $falseValueException);

            if ($result !== $falseValueException) {
                return $result;
            }
        }

        return Functions::NA();
    }
}
