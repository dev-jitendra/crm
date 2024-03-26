<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AdvancedValueBinder extends DefaultValueBinder implements IValueBinder
{
    
    public function bindValue(Cell $cell, $value = null)
    {
        
        if (is_string($value)) {
            $value = StringHelper::sanitizeUTF8($value);
        }

        
        $dataType = parent::dataTypeForValue($value);

        
        if ($dataType === DataType::TYPE_STRING && !$value instanceof RichText) {
            
            if ($value == Calculation::getTRUE()) {
                $cell->setValueExplicit(true, DataType::TYPE_BOOL);

                return true;
            } elseif ($value == Calculation::getFALSE()) {
                $cell->setValueExplicit(false, DataType::TYPE_BOOL);

                return true;
            }

            
            if (preg_match('/^' . Calculation::CALCULATION_REGEXP_NUMBER . '$/', $value)) {
                $cell->setValueExplicit((float) $value, DataType::TYPE_NUMERIC);

                return true;
            }

            
            if (preg_match('/^([+-]?)\s*(\d+)\s?\/\s*(\d+)$/', $value, $matches)) {
                
                $value = $matches[2] / $matches[3];
                if ($matches[1] == '-') {
                    $value = 0 - $value;
                }
                $cell->setValueExplicit((float) $value, DataType::TYPE_NUMERIC);
                
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getNumberFormat()->setFormatCode('??/??');

                return true;
            } elseif (preg_match('/^([+-]?)(\d*) +(\d*)\s?\/\s*(\d*)$/', $value, $matches)) {
                
                $value = $matches[2] + ($matches[3] / $matches[4]);
                if ($matches[1] == '-') {
                    $value = 0 - $value;
                }
                $cell->setValueExplicit((float) $value, DataType::TYPE_NUMERIC);
                
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getNumberFormat()->setFormatCode('# ??/??');

                return true;
            }

            
            if (preg_match('/^\-?\d*\.?\d*\s?\%$/', $value)) {
                
                $value = (float) str_replace('%', '', $value) / 100;
                $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
                
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

                return true;
            }

            
            $currencyCode = StringHelper::getCurrencyCode();
            $decimalSeparator = StringHelper::getDecimalSeparator();
            $thousandsSeparator = StringHelper::getThousandsSeparator();
            if (preg_match('/^' . preg_quote($currencyCode, '/') . ' *(\d{1,3}(' . preg_quote($thousandsSeparator, '/') . '\d{3})*|(\d+))(' . preg_quote($decimalSeparator, '/') . '\d{2})?$/', $value)) {
                
                $value = (float) trim(str_replace([$currencyCode, $thousandsSeparator, $decimalSeparator], ['', '', '.'], $value));
                $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
                
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getNumberFormat()->setFormatCode(
                        str_replace('$', $currencyCode, NumberFormat::FORMAT_CURRENCY_USD_SIMPLE)
                    );

                return true;
            } elseif (preg_match('/^\$ *(\d{1,3}(\,\d{3})*|(\d+))(\.\d{2})?$/', $value)) {
                
                $value = (float) trim(str_replace(['$', ','], '', $value));
                $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
                
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                return true;
            }

            
            if (preg_match('/^(\d|[0-1]\d|2[0-3]):[0-5]\d$/', $value)) {
                
                [$h, $m] = explode(':', $value);
                $days = $h / 24 + $m / 1440;
                $cell->setValueExplicit($days, DataType::TYPE_NUMERIC);
                
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_TIME3);

                return true;
            }

            
            if (preg_match('/^(\d|[0-1]\d|2[0-3]):[0-5]\d:[0-5]\d$/', $value)) {
                
                [$h, $m, $s] = explode(':', $value);
                $days = $h / 24 + $m / 1440 + $s / 86400;
                
                $cell->setValueExplicit($days, DataType::TYPE_NUMERIC);
                
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_TIME4);

                return true;
            }

            
            if (($d = Date::stringToExcel($value)) !== false) {
                
                $cell->setValueExplicit($d, DataType::TYPE_NUMERIC);
                
                if (strpos($value, ':') !== false) {
                    $formatCode = 'yyyy-mm-dd h:mm';
                } else {
                    $formatCode = 'yyyy-mm-dd';
                }
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getNumberFormat()->setFormatCode($formatCode);

                return true;
            }

            
            if (strpos($value, "\n") !== false) {
                $value = StringHelper::sanitizeUTF8($value);
                $cell->setValueExplicit($value, DataType::TYPE_STRING);
                
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getAlignment()->setWrapText(true);

                return true;
            }
        }

        
        return parent::bindValue($cell, $value);
    }
}
