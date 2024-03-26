<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

class Database
{
    
    private static function fieldExtract($database, $field)
    {
        $field = strtoupper(Functions::flattenSingleValue($field));
        $fieldNames = array_map('strtoupper', array_shift($database));

        if (is_numeric($field)) {
            $keys = array_keys($fieldNames);

            return $keys[$field - 1];
        }
        $key = array_search($field, $fieldNames);

        return ($key) ? $key : null;
    }

    
    private static function filter($database, $criteria)
    {
        $fieldNames = array_shift($database);
        $criteriaNames = array_shift($criteria);

        
        $testConditions = $testValues = [];
        $testConditionsCount = 0;
        foreach ($criteriaNames as $key => $criteriaName) {
            $testCondition = [];
            $testConditionCount = 0;
            foreach ($criteria as $row => $criterion) {
                if ($criterion[$key] > '') {
                    $testCondition[] = '[:' . $criteriaName . ']' . Functions::ifCondition($criterion[$key]);
                    ++$testConditionCount;
                }
            }
            if ($testConditionCount > 1) {
                $testConditions[] = 'OR(' . implode(',', $testCondition) . ')';
                ++$testConditionsCount;
            } elseif ($testConditionCount == 1) {
                $testConditions[] = $testCondition[0];
                ++$testConditionsCount;
            }
        }

        if ($testConditionsCount > 1) {
            $testConditionSet = 'AND(' . implode(',', $testConditions) . ')';
        } elseif ($testConditionsCount == 1) {
            $testConditionSet = $testConditions[0];
        }

        
        foreach ($database as $dataRow => $dataValues) {
            
            $testConditionList = $testConditionSet;
            foreach ($criteriaNames as $key => $criteriaName) {
                $k = array_search($criteriaName, $fieldNames);
                if (isset($dataValues[$k])) {
                    $dataValue = $dataValues[$k];
                    $dataValue = (is_string($dataValue)) ? Calculation::wrapResult(strtoupper($dataValue)) : $dataValue;
                    $testConditionList = str_replace('[:' . $criteriaName . ']', $dataValue, $testConditionList);
                }
            }
            
            $result = Calculation::getInstance()->_calculateFormulaValue('=' . $testConditionList);
            
            if (!$result) {
                unset($database[$dataRow]);
            }
        }

        return $database;
    }

    private static function getFilteredColumn($database, $field, $criteria)
    {
        
        $database = self::filter($database, $criteria);
        
        $colData = [];
        foreach ($database as $row) {
            $colData[] = $row[$field];
        }

        return $colData;
    }

    
    public static function DAVERAGE($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        
        return Statistical::AVERAGE(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    
    public static function DCOUNT($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        
        return Statistical::COUNT(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    
    public static function DCOUNTA($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        
        $database = self::filter($database, $criteria);
        
        $colData = [];
        foreach ($database as $row) {
            $colData[] = $row[$field];
        }

        
        return Statistical::COUNTA(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    
    public static function DGET($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        
        $colData = self::getFilteredColumn($database, $field, $criteria);
        if (count($colData) > 1) {
            return Functions::NAN();
        }

        return $colData[0];
    }

    
    public static function DMAX($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        
        return Statistical::MAX(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    
    public static function DMIN($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        
        return Statistical::MIN(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    
    public static function DPRODUCT($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        
        return MathTrig::PRODUCT(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    
    public static function DSTDEV($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        
        return Statistical::STDEV(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    
    public static function DSTDEVP($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        
        return Statistical::STDEVP(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    
    public static function DSUM($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        
        return MathTrig::SUM(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    
    public static function DVAR($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        
        return Statistical::VARFunc(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }

    
    public static function DVARP($database, $field, $criteria)
    {
        $field = self::fieldExtract($database, $field);
        if ($field === null) {
            return null;
        }

        
        return Statistical::VARP(
            self::getFilteredColumn($database, $field, $criteria)
        );
    }
}
