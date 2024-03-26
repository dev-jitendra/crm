<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Exception;

class AddressHelper
{
    
    public static function convertToA1(
        string $address,
        int $currentRowNumber = 1,
        int $currentColumnNumber = 1
    ): string {
        $validityCheck = preg_match('/^(R(\[?-?\d*\]?))(C(\[?-?\d*\]?))$/i', $address, $cellReference);

        if ($validityCheck === 0) {
            throw new Exception('Invalid R1C1-format Cell Reference');
        }

        $rowReference = $cellReference[2];
        
        if ($rowReference === '') {
            $rowReference = (string) $currentRowNumber;
        }
        
        if ($rowReference[0] === '[') {
            $rowReference = $currentRowNumber + trim($rowReference, '[]');
        }
        $columnReference = $cellReference[4];
        
        if ($columnReference === '') {
            $columnReference = (string) $currentColumnNumber;
        }
        
        if (is_string($columnReference) && $columnReference[0] === '[') {
            $columnReference = $currentColumnNumber + trim($columnReference, '[]');
        }

        if ($columnReference <= 0 || $rowReference <= 0) {
            throw new Exception('Invalid R1C1-format Cell Reference, Value out of range');
        }
        $A1CellReference = Coordinate::stringFromColumnIndex($columnReference) . $rowReference;

        return $A1CellReference;
    }

    
    public static function convertFormulaToA1(
        string $formula,
        int $currentRowNumber = 1,
        int $currentColumnNumber = 1
    ): string {
        if (substr($formula, 0, 3) == 'of:') {
            $formula = substr($formula, 3);
            $temp = explode('"', $formula);
            $key = false;
            foreach ($temp as &$value) {
                
                if ($key = !$key) {
                    $value = str_replace(['[.', '.', ']'], '', $value);
                }
            }
        } else {
            
            $temp = explode('"', $formula);
            $key = false;
            foreach ($temp as &$value) {
                
                if ($key = !$key) {
                    preg_match_all('/(R(\[?-?\d*\]?))(C(\[?-?\d*\]?))/', $value, $cellReferences, PREG_SET_ORDER + PREG_OFFSET_CAPTURE);
                    
                    
                    
                    $cellReferences = array_reverse($cellReferences);
                    
                    
                    foreach ($cellReferences as $cellReference) {
                        $A1CellReference = self::convertToA1($cellReference[0][0], $currentRowNumber, $currentColumnNumber);
                        $value = substr_replace($value, $A1CellReference, $cellReference[0][1], strlen($cellReference[0][0]));
                    }
                }
            }
        }
        unset($value);
        
        $formula = implode('"', $temp);

        return $formula;
    }

    
    public static function convertToR1C1(
        string $address,
        ?int $currentRowNumber = null,
        ?int $currentColumnNumber = null
    ): string {
        $validityCheck = preg_match('/^\$?([A-Z]{1,3})\$?(\d{1,7})$/i', $address, $cellReference);

        if ($validityCheck === 0) {
            throw new Exception('Invalid A1-format Cell Reference');
        }

        $columnId = Coordinate::columnIndexFromString($cellReference[1]);
        $rowId = (int) $cellReference[2];

        if ($currentRowNumber !== null) {
            if ($rowId === $currentRowNumber) {
                $rowId = '';
            } else {
                $rowId = '[' . ($rowId - $currentRowNumber) . ']';
            }
        }

        if ($currentColumnNumber !== null) {
            if ($columnId === $currentColumnNumber) {
                $columnId = '';
            } else {
                $columnId = '[' . ($columnId - $currentColumnNumber) . ']';
            }
        }

        $R1C1Address = "R{$rowId}C{$columnId}";

        return $R1C1Address;
    }
}
