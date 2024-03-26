<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LookupRef
{
    
    public static function cellAddress($row, $column, $relativity = 1, $referenceStyle = true, $sheetText = '')
    {
        $row = Functions::flattenSingleValue($row);
        $column = Functions::flattenSingleValue($column);
        $relativity = Functions::flattenSingleValue($relativity);
        $sheetText = Functions::flattenSingleValue($sheetText);

        if (($row < 1) || ($column < 1)) {
            return Functions::VALUE();
        }

        if ($sheetText > '') {
            if (strpos($sheetText, ' ') !== false) {
                $sheetText = "'" . $sheetText . "'";
            }
            $sheetText .= '!';
        }
        if ((!is_bool($referenceStyle)) || $referenceStyle) {
            $rowRelative = $columnRelative = '$';
            $column = Coordinate::stringFromColumnIndex($column);
            if (($relativity == 2) || ($relativity == 4)) {
                $columnRelative = '';
            }
            if (($relativity == 3) || ($relativity == 4)) {
                $rowRelative = '';
            }

            return $sheetText . $columnRelative . $column . $rowRelative . $row;
        }
        if (($relativity == 2) || ($relativity == 4)) {
            $column = '[' . $column . ']';
        }
        if (($relativity == 3) || ($relativity == 4)) {
            $row = '[' . $row . ']';
        }

        return $sheetText . 'R' . $row . 'C' . $column;
    }

    
    public static function COLUMN($cellAddress = null)
    {
        if ($cellAddress === null || trim($cellAddress) === '') {
            return 0;
        }

        if (is_array($cellAddress)) {
            foreach ($cellAddress as $columnKey => $value) {
                $columnKey = preg_replace('/[^a-z]/i', '', $columnKey);

                return (int) Coordinate::columnIndexFromString($columnKey);
            }
        } else {
            [$sheet, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
            if (strpos($cellAddress, ':') !== false) {
                [$startAddress, $endAddress] = explode(':', $cellAddress);
                $startAddress = preg_replace('/[^a-z]/i', '', $startAddress);
                $endAddress = preg_replace('/[^a-z]/i', '', $endAddress);
                $returnValue = [];
                do {
                    $returnValue[] = (int) Coordinate::columnIndexFromString($startAddress);
                } while ($startAddress++ != $endAddress);

                return $returnValue;
            }
            $cellAddress = preg_replace('/[^a-z]/i', '', $cellAddress);

            return (int) Coordinate::columnIndexFromString($cellAddress);
        }
    }

    
    public static function COLUMNS($cellAddress = null)
    {
        if ($cellAddress === null || $cellAddress === '') {
            return 1;
        } elseif (!is_array($cellAddress)) {
            return Functions::VALUE();
        }

        reset($cellAddress);
        $isMatrix = (is_numeric(key($cellAddress)));
        [$columns, $rows] = Calculation::getMatrixDimensions($cellAddress);

        if ($isMatrix) {
            return $rows;
        }

        return $columns;
    }

    
    public static function ROW($cellAddress = null)
    {
        if ($cellAddress === null || trim($cellAddress) === '') {
            return 0;
        }

        if (is_array($cellAddress)) {
            foreach ($cellAddress as $columnKey => $rowValue) {
                foreach ($rowValue as $rowKey => $cellValue) {
                    return (int) preg_replace('/\D/', '', $rowKey);
                }
            }
        } else {
            [$sheet, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
            if (strpos($cellAddress, ':') !== false) {
                [$startAddress, $endAddress] = explode(':', $cellAddress);
                $startAddress = preg_replace('/\D/', '', $startAddress);
                $endAddress = preg_replace('/\D/', '', $endAddress);
                $returnValue = [];
                do {
                    $returnValue[][] = (int) $startAddress;
                } while ($startAddress++ != $endAddress);

                return $returnValue;
            }
            [$cellAddress] = explode(':', $cellAddress);

            return (int) preg_replace('/\D/', '', $cellAddress);
        }
    }

    
    public static function ROWS($cellAddress = null)
    {
        if ($cellAddress === null || $cellAddress === '') {
            return 1;
        } elseif (!is_array($cellAddress)) {
            return Functions::VALUE();
        }

        reset($cellAddress);
        $isMatrix = (is_numeric(key($cellAddress)));
        [$columns, $rows] = Calculation::getMatrixDimensions($cellAddress);

        if ($isMatrix) {
            return $columns;
        }

        return $rows;
    }

    
    public static function HYPERLINK($linkURL = '', $displayName = null, ?Cell $pCell = null)
    {
        $linkURL = ($linkURL === null) ? '' : Functions::flattenSingleValue($linkURL);
        $displayName = ($displayName === null) ? '' : Functions::flattenSingleValue($displayName);

        if ((!is_object($pCell)) || (trim($linkURL) == '')) {
            return Functions::REF();
        }

        if ((is_object($displayName)) || trim($displayName) == '') {
            $displayName = $linkURL;
        }

        $pCell->getHyperlink()->setUrl($linkURL);
        $pCell->getHyperlink()->setTooltip($displayName);

        return $displayName;
    }

    
    public static function INDIRECT($cellAddress = null, ?Cell $pCell = null)
    {
        $cellAddress = Functions::flattenSingleValue($cellAddress);
        if ($cellAddress === null || $cellAddress === '') {
            return Functions::REF();
        }

        $cellAddress1 = $cellAddress;
        $cellAddress2 = null;
        if (strpos($cellAddress, ':') !== false) {
            [$cellAddress1, $cellAddress2] = explode(':', $cellAddress);
        }

        if (
            (!preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $cellAddress1, $matches)) ||
            (($cellAddress2 !== null) && (!preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $cellAddress2, $matches)))
        ) {
            if (!preg_match('/^' . Calculation::CALCULATION_REGEXP_DEFINEDNAME . '$/i', $cellAddress1, $matches)) {
                return Functions::REF();
            }

            if (strpos($cellAddress, '!') !== false) {
                [$sheetName, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
                $sheetName = trim($sheetName, "'");
                $pSheet = $pCell->getWorksheet()->getParent()->getSheetByName($sheetName);
            } else {
                $pSheet = $pCell->getWorksheet();
            }

            return Calculation::getInstance()->extractNamedRange($cellAddress, $pSheet, false);
        }

        if (strpos($cellAddress, '!') !== false) {
            [$sheetName, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
            $sheetName = trim($sheetName, "'");
            $pSheet = $pCell->getWorksheet()->getParent()->getSheetByName($sheetName);
        } else {
            $pSheet = $pCell->getWorksheet();
        }

        return Calculation::getInstance()->extractCellRange($cellAddress, $pSheet, false);
    }

    
    public static function OFFSET($cellAddress = null, $rows = 0, $columns = 0, $height = null, $width = null, ?Cell $pCell = null)
    {
        $rows = Functions::flattenSingleValue($rows);
        $columns = Functions::flattenSingleValue($columns);
        $height = Functions::flattenSingleValue($height);
        $width = Functions::flattenSingleValue($width);
        if ($cellAddress === null) {
            return 0;
        }

        if (!is_object($pCell)) {
            return Functions::REF();
        }

        $sheetName = null;
        if (strpos($cellAddress, '!')) {
            [$sheetName, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
            $sheetName = trim($sheetName, "'");
        }
        if (strpos($cellAddress, ':')) {
            [$startCell, $endCell] = explode(':', $cellAddress);
        } else {
            $startCell = $endCell = $cellAddress;
        }
        [$startCellColumn, $startCellRow] = Coordinate::coordinateFromString($startCell);
        [$endCellColumn, $endCellRow] = Coordinate::coordinateFromString($endCell);

        $startCellRow += $rows;
        $startCellColumn = Coordinate::columnIndexFromString($startCellColumn) - 1;
        $startCellColumn += $columns;

        if (($startCellRow <= 0) || ($startCellColumn < 0)) {
            return Functions::REF();
        }
        $endCellColumn = Coordinate::columnIndexFromString($endCellColumn) - 1;
        if (($width != null) && (!is_object($width))) {
            $endCellColumn = $startCellColumn + $width - 1;
        } else {
            $endCellColumn += $columns;
        }
        $startCellColumn = Coordinate::stringFromColumnIndex($startCellColumn + 1);

        if (($height != null) && (!is_object($height))) {
            $endCellRow = $startCellRow + $height - 1;
        } else {
            $endCellRow += $rows;
        }

        if (($endCellRow <= 0) || ($endCellColumn < 0)) {
            return Functions::REF();
        }
        $endCellColumn = Coordinate::stringFromColumnIndex($endCellColumn + 1);

        $cellAddress = $startCellColumn . $startCellRow;
        if (($startCellColumn != $endCellColumn) || ($startCellRow != $endCellRow)) {
            $cellAddress .= ':' . $endCellColumn . $endCellRow;
        }

        if ($sheetName !== null) {
            $pSheet = $pCell->getWorksheet()->getParent()->getSheetByName($sheetName);
        } else {
            $pSheet = $pCell->getWorksheet();
        }

        return Calculation::getInstance()->extractCellRange($cellAddress, $pSheet, false);
    }

    
    public static function CHOOSE(...$chooseArgs)
    {
        $chosenEntry = Functions::flattenArray(array_shift($chooseArgs));
        $entryCount = count($chooseArgs) - 1;

        if (is_array($chosenEntry)) {
            $chosenEntry = array_shift($chosenEntry);
        }
        if ((is_numeric($chosenEntry)) && (!is_bool($chosenEntry))) {
            --$chosenEntry;
        } else {
            return Functions::VALUE();
        }
        $chosenEntry = floor($chosenEntry);
        if (($chosenEntry < 0) || ($chosenEntry > $entryCount)) {
            return Functions::VALUE();
        }

        if (is_array($chooseArgs[$chosenEntry])) {
            return Functions::flattenArray($chooseArgs[$chosenEntry]);
        }

        return $chooseArgs[$chosenEntry];
    }

    
    public static function MATCH($lookupValue, $lookupArray, $matchType = 1)
    {
        $lookupArray = Functions::flattenArray($lookupArray);
        $lookupValue = Functions::flattenSingleValue($lookupValue);
        $matchType = ($matchType === null) ? 1 : (int) Functions::flattenSingleValue($matchType);

        
        if (is_string($lookupValue)) {
            $lookupValue = StringHelper::strToLower($lookupValue);
        }

        
        if ((!is_numeric($lookupValue)) && (!is_string($lookupValue)) && (!is_bool($lookupValue))) {
            return Functions::NA();
        }

        
        if (($matchType !== 0) && ($matchType !== -1) && ($matchType !== 1)) {
            return Functions::NA();
        }

        
        $lookupArraySize = count($lookupArray);
        if ($lookupArraySize <= 0) {
            return Functions::NA();
        }

        if ($matchType == 1) {
            

            $lookupArray = array_reverse($lookupArray);
            $keySet = array_reverse(array_keys($lookupArray));
        }

        
        foreach ($lookupArray as $i => $lookupArrayValue) {
            
            if (
                (!is_numeric($lookupArrayValue)) && (!is_string($lookupArrayValue)) &&
                (!is_bool($lookupArrayValue)) && ($lookupArrayValue !== null)
            ) {
                return Functions::NA();
            }
            
            if (is_string($lookupArrayValue)) {
                $lookupArray[$i] = StringHelper::strToLower($lookupArrayValue);
            }
            if (($lookupArrayValue === null) && (($matchType == 1) || ($matchType == -1))) {
                unset($lookupArray[$i]);
            }
        }

        
        
        

        if ($matchType === 0 || $matchType === 1) {
            foreach ($lookupArray as $i => $lookupArrayValue) {
                $typeMatch = ((gettype($lookupValue) === gettype($lookupArrayValue)) || (is_numeric($lookupValue) && is_numeric($lookupArrayValue)));
                $exactTypeMatch = $typeMatch && $lookupArrayValue === $lookupValue;
                $nonOnlyNumericExactMatch = !$typeMatch && $lookupArrayValue === $lookupValue;
                $exactMatch = $exactTypeMatch || $nonOnlyNumericExactMatch;

                if ($matchType === 0) {
                    if ($typeMatch && is_string($lookupValue) && (bool) preg_match('/([\?\*])/', $lookupValue)) {
                        $splitString = $lookupValue;
                        $chars = array_map(function ($i) use ($splitString) {
                            return mb_substr($splitString, $i, 1);
                        }, range(0, mb_strlen($splitString) - 1));

                        $length = count($chars);
                        $pattern = '/^';
                        for ($j = 0; $j < $length; ++$j) {
                            if ($chars[$j] === '~') {
                                if (isset($chars[$j + 1])) {
                                    if ($chars[$j + 1] === '*') {
                                        $pattern .= preg_quote($chars[$j + 1], '/');
                                        ++$j;
                                    } elseif ($chars[$j + 1] === '?') {
                                        $pattern .= preg_quote($chars[$j + 1], '/');
                                        ++$j;
                                    }
                                } else {
                                    $pattern .= preg_quote($chars[$j], '/');
                                }
                            } elseif ($chars[$j] === '*') {
                                $pattern .= '.*';
                            } elseif ($chars[$j] === '?') {
                                $pattern .= '.{1}';
                            } else {
                                $pattern .= preg_quote($chars[$j], '/');
                            }
                        }

                        $pattern .= '$/';
                        if ((bool) preg_match($pattern, $lookupArrayValue)) {
                            
                            return $i + 1;
                        }
                    } elseif ($exactMatch) {
                        
                        return $i + 1;
                    }
                } elseif (($matchType === 1) && $typeMatch && ($lookupArrayValue <= $lookupValue)) {
                    $i = array_search($i, $keySet);

                    
                    return $i + 1;
                }
            }
        } else {
            $maxValueKey = null;

            
            
            
            foreach ($lookupArray as $i => $lookupArrayValue) {
                $typeMatch = gettype($lookupValue) === gettype($lookupArrayValue);
                $exactTypeMatch = $typeMatch && $lookupArrayValue === $lookupValue;
                $nonOnlyNumericExactMatch = !$typeMatch && $lookupArrayValue === $lookupValue;
                $exactMatch = $exactTypeMatch || $nonOnlyNumericExactMatch;

                if ($exactMatch) {
                    
                    
                    return $i + 1;
                } elseif ($typeMatch & $lookupArrayValue >= $lookupValue) {
                    $maxValueKey = $i + 1;
                } elseif ($typeMatch & $lookupArrayValue < $lookupValue) {
                    
                    break;
                }
            }

            if ($maxValueKey !== null) {
                return $maxValueKey;
            }
        }

        
        return Functions::NA();
    }

    
    public static function INDEX($arrayValues, $rowNum = 0, $columnNum = 0)
    {
        $rowNum = Functions::flattenSingleValue($rowNum);
        $columnNum = Functions::flattenSingleValue($columnNum);

        if (($rowNum < 0) || ($columnNum < 0)) {
            return Functions::VALUE();
        }

        if (!is_array($arrayValues) || ($rowNum > count($arrayValues))) {
            return Functions::REF();
        }

        $rowKeys = array_keys($arrayValues);
        $columnKeys = @array_keys($arrayValues[$rowKeys[0]]);

        if ($columnNum > count($columnKeys)) {
            return Functions::VALUE();
        } elseif ($columnNum == 0) {
            if ($rowNum == 0) {
                return $arrayValues;
            }
            $rowNum = $rowKeys[--$rowNum];
            $returnArray = [];
            foreach ($arrayValues as $arrayColumn) {
                if (is_array($arrayColumn)) {
                    if (isset($arrayColumn[$rowNum])) {
                        $returnArray[] = $arrayColumn[$rowNum];
                    } else {
                        return [$rowNum => $arrayValues[$rowNum]];
                    }
                } else {
                    return $arrayValues[$rowNum];
                }
            }

            return $returnArray;
        }
        $columnNum = $columnKeys[--$columnNum];
        if ($rowNum > count($rowKeys)) {
            return Functions::VALUE();
        } elseif ($rowNum == 0) {
            return $arrayValues[$columnNum];
        }
        $rowNum = $rowKeys[--$rowNum];

        return $arrayValues[$rowNum][$columnNum];
    }

    
    public static function TRANSPOSE($matrixData)
    {
        $returnMatrix = [];
        if (!is_array($matrixData)) {
            $matrixData = [[$matrixData]];
        }

        $column = 0;
        foreach ($matrixData as $matrixRow) {
            $row = 0;
            foreach ($matrixRow as $matrixCell) {
                $returnMatrix[$row][$column] = $matrixCell;
                ++$row;
            }
            ++$column;
        }

        return $returnMatrix;
    }

    private static function vlookupSort($a, $b)
    {
        reset($a);
        $firstColumn = key($a);
        $aLower = StringHelper::strToLower($a[$firstColumn]);
        $bLower = StringHelper::strToLower($b[$firstColumn]);
        if ($aLower == $bLower) {
            return 0;
        }

        return ($aLower < $bLower) ? -1 : 1;
    }

    
    public static function VLOOKUP($lookup_value, $lookup_array, $index_number, $not_exact_match = true)
    {
        $lookup_value = Functions::flattenSingleValue($lookup_value);
        $index_number = Functions::flattenSingleValue($index_number);
        $not_exact_match = Functions::flattenSingleValue($not_exact_match);

        
        if ($index_number < 1) {
            return Functions::VALUE();
        }

        
        if ((!is_array($lookup_array)) || (empty($lookup_array))) {
            return Functions::REF();
        }
        $f = array_keys($lookup_array);
        $firstRow = array_pop($f);
        if ((!is_array($lookup_array[$firstRow])) || ($index_number > count($lookup_array[$firstRow]))) {
            return Functions::REF();
        }
        $columnKeys = array_keys($lookup_array[$firstRow]);
        $returnColumn = $columnKeys[--$index_number];
        $firstColumn = array_shift($columnKeys);

        if (!$not_exact_match) {
            uasort($lookup_array, ['self', 'vlookupSort']);
        }

        $lookupLower = StringHelper::strToLower($lookup_value);
        $rowNumber = $rowValue = false;
        foreach ($lookup_array as $rowKey => $rowData) {
            $firstLower = StringHelper::strToLower($rowData[$firstColumn]);

            
            if (
                (is_numeric($lookup_value) && is_numeric($rowData[$firstColumn]) && ($rowData[$firstColumn] > $lookup_value)) ||
                (!is_numeric($lookup_value) && !is_numeric($rowData[$firstColumn]) && ($firstLower > $lookupLower))
            ) {
                break;
            }
            
            if (
                (is_numeric($lookup_value) && is_numeric($rowData[$firstColumn])) ||
                (!is_numeric($lookup_value) && !is_numeric($rowData[$firstColumn]))
            ) {
                if ($not_exact_match) {
                    $rowNumber = $rowKey;

                    continue;
                } elseif (
                    ($firstLower == $lookupLower)
                    
                    
                    
                    && (($rowNumber == false) || ($rowKey < $rowNumber))
                ) {
                    $rowNumber = $rowKey;
                }
            }
        }

        if ($rowNumber !== false) {
            
            return $lookup_array[$rowNumber][$returnColumn];
        }

        return Functions::NA();
    }

    
    public static function HLOOKUP($lookup_value, $lookup_array, $index_number, $not_exact_match = true)
    {
        $lookup_value = Functions::flattenSingleValue($lookup_value);
        $index_number = Functions::flattenSingleValue($index_number);
        $not_exact_match = Functions::flattenSingleValue($not_exact_match);

        
        if ($index_number < 1) {
            return Functions::VALUE();
        }

        
        if ((!is_array($lookup_array)) || (empty($lookup_array))) {
            return Functions::REF();
        }
        $f = array_keys($lookup_array);
        $firstRow = reset($f);
        if ((!is_array($lookup_array[$firstRow])) || ($index_number > count($lookup_array))) {
            return Functions::REF();
        }

        $firstkey = $f[0] - 1;
        $returnColumn = $firstkey + $index_number;
        $firstColumn = array_shift($f);
        $rowNumber = null;
        foreach ($lookup_array[$firstColumn] as $rowKey => $rowData) {
            
            $bothNumeric = is_numeric($lookup_value) && is_numeric($rowData);
            $bothNotNumeric = !is_numeric($lookup_value) && !is_numeric($rowData);
            $lookupLower = StringHelper::strToLower($lookup_value);
            $rowDataLower = StringHelper::strToLower($rowData);

            if (
                $not_exact_match && (
                ($bothNumeric && $rowData > $lookup_value) ||
                ($bothNotNumeric && $rowDataLower > $lookupLower)
                )
            ) {
                break;
            }

            
            if ($bothNumeric || $bothNotNumeric) {
                if ($not_exact_match) {
                    $rowNumber = $rowKey;

                    continue;
                } elseif (
                    $rowDataLower === $lookupLower
                    && ($rowNumber === null || $rowKey < $rowNumber)
                ) {
                    $rowNumber = $rowKey;
                }
            }
        }

        if ($rowNumber !== null) {
            
            return $lookup_array[$returnColumn][$rowNumber];
        }

        return Functions::NA();
    }

    
    public static function LOOKUP($lookup_value, $lookup_vector, $result_vector = null)
    {
        $lookup_value = Functions::flattenSingleValue($lookup_value);

        if (!is_array($lookup_vector)) {
            return Functions::NA();
        }
        $hasResultVector = isset($result_vector);
        $lookupRows = count($lookup_vector);
        $l = array_keys($lookup_vector);
        $l = array_shift($l);
        $lookupColumns = count($lookup_vector[$l]);
        
        if (($lookupRows === 1 && $lookupColumns > 1) || (!$hasResultVector && $lookupRows === 2 && $lookupColumns !== 2)) {
            $lookup_vector = self::TRANSPOSE($lookup_vector);
            $lookupRows = count($lookup_vector);
            $l = array_keys($lookup_vector);
            $lookupColumns = count($lookup_vector[array_shift($l)]);
        }

        if ($result_vector === null) {
            $result_vector = $lookup_vector;
        }
        $resultRows = count($result_vector);
        $l = array_keys($result_vector);
        $l = array_shift($l);
        $resultColumns = count($result_vector[$l]);
        
        if ($resultRows === 1 && $resultColumns > 1) {
            $result_vector = self::TRANSPOSE($result_vector);
            $resultRows = count($result_vector);
            $r = array_keys($result_vector);
            $resultColumns = count($result_vector[array_shift($r)]);
        }

        if ($lookupRows === 2 && !$hasResultVector) {
            $result_vector = array_pop($lookup_vector);
            $lookup_vector = array_shift($lookup_vector);
        }

        if ($lookupColumns !== 2) {
            foreach ($lookup_vector as &$value) {
                if (is_array($value)) {
                    $k = array_keys($value);
                    $key1 = $key2 = array_shift($k);
                    ++$key2;
                    $dataValue1 = $value[$key1];
                } else {
                    $key1 = 0;
                    $key2 = 1;
                    $dataValue1 = $value;
                }
                $dataValue2 = array_shift($result_vector);
                if (is_array($dataValue2)) {
                    $dataValue2 = array_shift($dataValue2);
                }
                $value = [$key1 => $dataValue1, $key2 => $dataValue2];
            }
            unset($value);
        }

        return self::VLOOKUP($lookup_value, $lookup_vector, 2);
    }

    
    public static function FORMULATEXT($cellReference = '', ?Cell $pCell = null)
    {
        if ($pCell === null) {
            return Functions::REF();
        }

        preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $cellReference, $matches);

        $cellReference = $matches[6] . $matches[7];
        $worksheetName = trim($matches[3], "'");
        $worksheet = (!empty($worksheetName))
            ? $pCell->getWorksheet()->getParent()->getSheetByName($worksheetName)
            : $pCell->getWorksheet();

        if (!$worksheet->getCell($cellReference)->isFormula()) {
            return Functions::NA();
        }

        return $worksheet->getCell($cellReference)->getValue();
    }
}
