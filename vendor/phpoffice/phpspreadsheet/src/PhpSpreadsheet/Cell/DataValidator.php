<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Exception;


class DataValidator
{
    
    public function isValid(Cell $cell)
    {
        if (!$cell->hasDataValidation()) {
            return true;
        }

        $cellValue = $cell->getValue();
        $dataValidation = $cell->getDataValidation();

        if (!$dataValidation->getAllowBlank() && ($cellValue === null || $cellValue === '')) {
            return false;
        }

        
        switch ($dataValidation->getType()) {
            case DataValidation::TYPE_LIST:
                return $this->isValueInList($cell);
        }

        return false;
    }

    
    private function isValueInList(Cell $cell)
    {
        $cellValue = $cell->getValue();
        $dataValidation = $cell->getDataValidation();

        $formula1 = $dataValidation->getFormula1();
        if (!empty($formula1)) {
            
            if ($formula1[0] === '"') {
                return in_array(strtolower($cellValue), explode(',', strtolower(trim($formula1, '"'))), true);
            } elseif (strpos($formula1, ':') > 0) {
                
                $matchFormula = '=MATCH(' . $cell->getCoordinate() . ', ' . $formula1 . ', 0)';
                $calculation = Calculation::getInstance($cell->getWorksheet()->getParent());

                try {
                    $result = $calculation->calculateFormula($matchFormula, $cell->getCoordinate(), $cell);

                    return $result !== Functions::NA();
                } catch (Exception $ex) {
                    return false;
                }
            }
        }

        return true;
    }
}
