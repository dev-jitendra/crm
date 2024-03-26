<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AutoFilter
{
    
    private $workSheet;

    
    private $range = '';

    
    private $columns = [];

    
    public function __construct($pRange = '', ?Worksheet $pSheet = null)
    {
        $this->range = $pRange;
        $this->workSheet = $pSheet;
    }

    
    public function getParent()
    {
        return $this->workSheet;
    }

    
    public function setParent(?Worksheet $pSheet = null)
    {
        $this->workSheet = $pSheet;

        return $this;
    }

    
    public function getRange()
    {
        return $this->range;
    }

    
    public function setRange($pRange)
    {
        
        [$worksheet, $pRange] = Worksheet::extractSheetTitle($pRange, true);

        if (strpos($pRange, ':') !== false) {
            $this->range = $pRange;
        } elseif (empty($pRange)) {
            $this->range = '';
        } else {
            throw new PhpSpreadsheetException('Autofilter must be set on a range of cells.');
        }

        if (empty($pRange)) {
            
            $this->columns = [];
        } else {
            
            [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($this->range);
            foreach ($this->columns as $key => $value) {
                $colIndex = Coordinate::columnIndexFromString($key);
                if (($rangeStart[0] > $colIndex) || ($rangeEnd[0] < $colIndex)) {
                    unset($this->columns[$key]);
                }
            }
        }

        return $this;
    }

    
    public function getColumns()
    {
        return $this->columns;
    }

    
    public function testColumnInRange($column)
    {
        if (empty($this->range)) {
            throw new PhpSpreadsheetException('No autofilter range is defined.');
        }

        $columnIndex = Coordinate::columnIndexFromString($column);
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($this->range);
        if (($rangeStart[0] > $columnIndex) || ($rangeEnd[0] < $columnIndex)) {
            throw new PhpSpreadsheetException('Column is outside of current autofilter range.');
        }

        return $columnIndex - $rangeStart[0];
    }

    
    public function getColumnOffset($pColumn)
    {
        return $this->testColumnInRange($pColumn);
    }

    
    public function getColumn($pColumn)
    {
        $this->testColumnInRange($pColumn);

        if (!isset($this->columns[$pColumn])) {
            $this->columns[$pColumn] = new AutoFilter\Column($pColumn, $this);
        }

        return $this->columns[$pColumn];
    }

    
    public function getColumnByOffset($pColumnOffset)
    {
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($this->range);
        $pColumn = Coordinate::stringFromColumnIndex($rangeStart[0] + $pColumnOffset);

        return $this->getColumn($pColumn);
    }

    
    public function setColumn($pColumn)
    {
        if ((is_string($pColumn)) && (!empty($pColumn))) {
            $column = $pColumn;
        } elseif (is_object($pColumn) && ($pColumn instanceof AutoFilter\Column)) {
            $column = $pColumn->getColumnIndex();
        } else {
            throw new PhpSpreadsheetException('Column is not within the autofilter range.');
        }
        $this->testColumnInRange($column);

        if (is_string($pColumn)) {
            $this->columns[$pColumn] = new AutoFilter\Column($pColumn, $this);
        } elseif (is_object($pColumn) && ($pColumn instanceof AutoFilter\Column)) {
            $pColumn->setParent($this);
            $this->columns[$column] = $pColumn;
        }
        ksort($this->columns);

        return $this;
    }

    
    public function clearColumn($pColumn)
    {
        $this->testColumnInRange($pColumn);

        if (isset($this->columns[$pColumn])) {
            unset($this->columns[$pColumn]);
        }

        return $this;
    }

    
    public function shiftColumn($fromColumn, $toColumn)
    {
        $fromColumn = strtoupper($fromColumn);
        $toColumn = strtoupper($toColumn);

        if (($fromColumn !== null) && (isset($this->columns[$fromColumn])) && ($toColumn !== null)) {
            $this->columns[$fromColumn]->setParent();
            $this->columns[$fromColumn]->setColumnIndex($toColumn);
            $this->columns[$toColumn] = $this->columns[$fromColumn];
            $this->columns[$toColumn]->setParent($this);
            unset($this->columns[$fromColumn]);

            ksort($this->columns);
        }

        return $this;
    }

    
    private static function filterTestInSimpleDataSet($cellValue, $dataSet)
    {
        $dataSetValues = $dataSet['filterValues'];
        $blanks = $dataSet['blanks'];
        if (($cellValue == '') || ($cellValue === null)) {
            return $blanks;
        }

        return in_array($cellValue, $dataSetValues);
    }

    
    private static function filterTestInDateGroupSet($cellValue, $dataSet)
    {
        $dateSet = $dataSet['filterValues'];
        $blanks = $dataSet['blanks'];
        if (($cellValue == '') || ($cellValue === null)) {
            return $blanks;
        }

        if (is_numeric($cellValue)) {
            $dateValue = Date::excelToTimestamp($cellValue);
            if ($cellValue < 1) {
                
                $dtVal = date('His', $dateValue);
                $dateSet = $dateSet['time'];
            } elseif ($cellValue == floor($cellValue)) {
                
                $dtVal = date('Ymd', $dateValue);
                $dateSet = $dateSet['date'];
            } else {
                
                $dtVal = date('YmdHis', $dateValue);
                $dateSet = $dateSet['dateTime'];
            }
            foreach ($dateSet as $dateValue) {
                
                if (substr($dtVal, 0, strlen($dateValue)) == $dateValue) {
                    return true;
                }
            }
        }

        return false;
    }

    
    private static function filterTestInCustomDataSet($cellValue, $ruleSet)
    {
        $dataSet = $ruleSet['filterRules'];
        $join = $ruleSet['join'];
        $customRuleForBlanks = $ruleSet['customRuleForBlanks'] ?? false;

        if (!$customRuleForBlanks) {
            
            if (($cellValue == '') || ($cellValue === null)) {
                return false;
            }
        }
        $returnVal = ($join == AutoFilter\Column::AUTOFILTER_COLUMN_JOIN_AND);
        foreach ($dataSet as $rule) {
            $retVal = false;

            if (is_numeric($rule['value'])) {
                
                switch ($rule['operator']) {
                    case AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_EQUAL:
                        $retVal = ($cellValue == $rule['value']);

                        break;
                    case AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL:
                        $retVal = ($cellValue != $rule['value']);

                        break;
                    case AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_GREATERTHAN:
                        $retVal = ($cellValue > $rule['value']);

                        break;
                    case AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL:
                        $retVal = ($cellValue >= $rule['value']);

                        break;
                    case AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_LESSTHAN:
                        $retVal = ($cellValue < $rule['value']);

                        break;
                    case AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL:
                        $retVal = ($cellValue <= $rule['value']);

                        break;
                }
            } elseif ($rule['value'] == '') {
                switch ($rule['operator']) {
                    case AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_EQUAL:
                        $retVal = (($cellValue == '') || ($cellValue === null));

                        break;
                    case AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL:
                        $retVal = (($cellValue != '') && ($cellValue !== null));

                        break;
                    default:
                        $retVal = true;

                        break;
                }
            } else {
                
                $retVal = preg_match('/^' . $rule['value'] . '$/i', $cellValue);
            }
            
            switch ($join) {
                case AutoFilter\Column::AUTOFILTER_COLUMN_JOIN_OR:
                    $returnVal = $returnVal || $retVal;
                    
                    
                    if ($returnVal) {
                        return $returnVal;
                    }

                    break;
                case AutoFilter\Column::AUTOFILTER_COLUMN_JOIN_AND:
                    $returnVal = $returnVal && $retVal;

                    break;
            }
        }

        return $returnVal;
    }

    
    private static function filterTestInPeriodDateSet($cellValue, $monthSet)
    {
        
        if (($cellValue == '') || ($cellValue === null)) {
            return false;
        }

        if (is_numeric($cellValue)) {
            $dateValue = date('m', Date::excelToTimestamp($cellValue));
            if (in_array($dateValue, $monthSet)) {
                return true;
            }
        }

        return false;
    }

    
    private static $fromReplace = ['\*', '\?', '~~', '~.*', '~.?'];

    private static $toReplace = ['.*', '.', '~', '\*', '\?'];

    
    private function dynamicFilterDateRange($dynamicRuleType, &$filterColumn)
    {
        $rDateType = Functions::getReturnDateType();
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_NUMERIC);
        $val = $maxVal = null;

        $ruleValues = [];
        $baseDate = DateTime::DATENOW();
        
        switch ($dynamicRuleType) {
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTWEEK:
                $baseDate = strtotime('-7 days', $baseDate);

                break;
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTWEEK:
                $baseDate = strtotime('-7 days', $baseDate);

                break;
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTMONTH:
                $baseDate = strtotime('-1 month', gmmktime(0, 0, 0, 1, date('m', $baseDate), date('Y', $baseDate)));

                break;
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTMONTH:
                $baseDate = strtotime('+1 month', gmmktime(0, 0, 0, 1, date('m', $baseDate), date('Y', $baseDate)));

                break;
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTQUARTER:
                $baseDate = strtotime('-3 month', gmmktime(0, 0, 0, 1, date('m', $baseDate), date('Y', $baseDate)));

                break;
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTQUARTER:
                $baseDate = strtotime('+3 month', gmmktime(0, 0, 0, 1, date('m', $baseDate), date('Y', $baseDate)));

                break;
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTYEAR:
                $baseDate = strtotime('-1 year', gmmktime(0, 0, 0, 1, date('m', $baseDate), date('Y', $baseDate)));

                break;
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTYEAR:
                $baseDate = strtotime('+1 year', gmmktime(0, 0, 0, 1, date('m', $baseDate), date('Y', $baseDate)));

                break;
        }

        switch ($dynamicRuleType) {
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_TODAY:
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_YESTERDAY:
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_TOMORROW:
                $maxVal = (int) Date::PHPtoExcel(strtotime('+1 day', $baseDate));
                $val = (int) Date::PHPToExcel($baseDate);

                break;
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_YEARTODATE:
                $maxVal = (int) Date::PHPtoExcel(strtotime('+1 day', $baseDate));
                $val = (int) Date::PHPToExcel(gmmktime(0, 0, 0, 1, 1, date('Y', $baseDate)));

                break;
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_THISYEAR:
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTYEAR:
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTYEAR:
                $maxVal = (int) Date::PHPToExcel(gmmktime(0, 0, 0, 31, 12, date('Y', $baseDate)));
                ++$maxVal;
                $val = (int) Date::PHPToExcel(gmmktime(0, 0, 0, 1, 1, date('Y', $baseDate)));

                break;
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_THISQUARTER:
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTQUARTER:
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTQUARTER:
                $thisMonth = date('m', $baseDate);
                $thisQuarter = floor(--$thisMonth / 3);
                $maxVal = (int) Date::PHPtoExcel(gmmktime(0, 0, 0, date('t', $baseDate), (1 + $thisQuarter) * 3, date('Y', $baseDate)));
                ++$maxVal;
                $val = (int) Date::PHPToExcel(gmmktime(0, 0, 0, 1, 1 + $thisQuarter * 3, date('Y', $baseDate)));

                break;
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_THISMONTH:
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTMONTH:
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTMONTH:
                $maxVal = (int) Date::PHPtoExcel(gmmktime(0, 0, 0, date('t', $baseDate), date('m', $baseDate), date('Y', $baseDate)));
                ++$maxVal;
                $val = (int) Date::PHPToExcel(gmmktime(0, 0, 0, 1, date('m', $baseDate), date('Y', $baseDate)));

                break;
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_THISWEEK:
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTWEEK:
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTWEEK:
                $dayOfWeek = date('w', $baseDate);
                $val = (int) Date::PHPToExcel($baseDate) - $dayOfWeek;
                $maxVal = $val + 7;

                break;
        }

        switch ($dynamicRuleType) {
            
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_YESTERDAY:
                --$maxVal;
                --$val;

                break;
            case AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_TOMORROW:
                ++$maxVal;
                ++$val;

                break;
        }

        
        $filterColumn->setAttributes(['val' => $val, 'maxVal' => $maxVal]);

        
        $ruleValues[] = ['operator' => AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL, 'value' => $val];
        $ruleValues[] = ['operator' => AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_LESSTHAN, 'value' => $maxVal];
        Functions::setReturnDateType($rDateType);

        return ['method' => 'filterTestInCustomDataSet', 'arguments' => ['filterRules' => $ruleValues, 'join' => AutoFilter\Column::AUTOFILTER_COLUMN_JOIN_AND]];
    }

    private function calculateTopTenValue($columnID, $startRow, $endRow, $ruleType, $ruleValue)
    {
        $range = $columnID . $startRow . ':' . $columnID . $endRow;
        $dataValues = Functions::flattenArray($this->workSheet->rangeToArray($range, null, true, false));

        $dataValues = array_filter($dataValues);
        if ($ruleType == AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP) {
            rsort($dataValues);
        } else {
            sort($dataValues);
        }

        return array_pop(array_slice($dataValues, 0, $ruleValue));
    }

    
    public function showHideRows()
    {
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($this->range);

        
        $this->workSheet->getRowDimension($rangeStart[1])->setVisible(true);

        $columnFilterTests = [];
        foreach ($this->columns as $columnID => $filterColumn) {
            $rules = $filterColumn->getRules();
            switch ($filterColumn->getFilterType()) {
                case AutoFilter\Column::AUTOFILTER_FILTERTYPE_FILTER:
                    $ruleType = null;
                    $ruleValues = [];
                    
                    foreach ($rules as $rule) {
                        $ruleType = $rule->getRuleType();
                        $ruleValues[] = $rule->getValue();
                    }
                    
                    $blanks = false;
                    $ruleDataSet = array_filter($ruleValues);
                    if (count($ruleValues) != count($ruleDataSet)) {
                        $blanks = true;
                    }
                    if ($ruleType == AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_FILTER) {
                        
                        $columnFilterTests[$columnID] = [
                            'method' => 'filterTestInSimpleDataSet',
                            'arguments' => ['filterValues' => $ruleDataSet, 'blanks' => $blanks],
                        ];
                    } else {
                        
                        $arguments = [
                            'date' => [],
                            'time' => [],
                            'dateTime' => [],
                        ];
                        foreach ($ruleDataSet as $ruleValue) {
                            $date = $time = '';
                            if (
                                (isset($ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_YEAR])) &&
                                ($ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_YEAR] !== '')
                            ) {
                                $date .= sprintf('%04d', $ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_YEAR]);
                            }
                            if (
                                (isset($ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_MONTH])) &&
                                ($ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_MONTH] != '')
                            ) {
                                $date .= sprintf('%02d', $ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_MONTH]);
                            }
                            if (
                                (isset($ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_DAY])) &&
                                ($ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_DAY] !== '')
                            ) {
                                $date .= sprintf('%02d', $ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_DAY]);
                            }
                            if (
                                (isset($ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_HOUR])) &&
                                ($ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_HOUR] !== '')
                            ) {
                                $time .= sprintf('%02d', $ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_HOUR]);
                            }
                            if (
                                (isset($ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_MINUTE])) &&
                                ($ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_MINUTE] !== '')
                            ) {
                                $time .= sprintf('%02d', $ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_MINUTE]);
                            }
                            if (
                                (isset($ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_SECOND])) &&
                                ($ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_SECOND] !== '')
                            ) {
                                $time .= sprintf('%02d', $ruleValue[AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_SECOND]);
                            }
                            $dateTime = $date . $time;
                            $arguments['date'][] = $date;
                            $arguments['time'][] = $time;
                            $arguments['dateTime'][] = $dateTime;
                        }
                        
                        $arguments['date'] = array_filter($arguments['date']);
                        $arguments['time'] = array_filter($arguments['time']);
                        $arguments['dateTime'] = array_filter($arguments['dateTime']);
                        $columnFilterTests[$columnID] = [
                            'method' => 'filterTestInDateGroupSet',
                            'arguments' => ['filterValues' => $arguments, 'blanks' => $blanks],
                        ];
                    }

                    break;
                case AutoFilter\Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER:
                    $customRuleForBlanks = false;
                    $ruleValues = [];
                    
                    foreach ($rules as $rule) {
                        $ruleValue = $rule->getValue();
                        if (!is_numeric($ruleValue)) {
                            
                            $ruleValue = preg_quote($ruleValue);
                            $ruleValue = str_replace(self::$fromReplace, self::$toReplace, $ruleValue);
                            if (trim($ruleValue) == '') {
                                $customRuleForBlanks = true;
                                $ruleValue = trim($ruleValue);
                            }
                        }
                        $ruleValues[] = ['operator' => $rule->getOperator(), 'value' => $ruleValue];
                    }
                    $join = $filterColumn->getJoin();
                    $columnFilterTests[$columnID] = [
                        'method' => 'filterTestInCustomDataSet',
                        'arguments' => ['filterRules' => $ruleValues, 'join' => $join, 'customRuleForBlanks' => $customRuleForBlanks],
                    ];

                    break;
                case AutoFilter\Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER:
                    $ruleValues = [];
                    foreach ($rules as $rule) {
                        
                        $dynamicRuleType = $rule->getGrouping();
                        if (
                            ($dynamicRuleType == AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_ABOVEAVERAGE) ||
                            ($dynamicRuleType == AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_BELOWAVERAGE)
                        ) {
                            
                            
                            $averageFormula = '=AVERAGE(' . $columnID . ($rangeStart[1] + 1) . ':' . $columnID . $rangeEnd[1] . ')';
                            $average = Calculation::getInstance()->calculateFormula($averageFormula, null, $this->workSheet->getCell('A1'));
                            
                            $operator = ($dynamicRuleType === AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMIC_ABOVEAVERAGE)
                                ? AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_GREATERTHAN
                                : AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_LESSTHAN;
                            $ruleValues[] = [
                                'operator' => $operator,
                                'value' => $average,
                            ];
                            $columnFilterTests[$columnID] = [
                                'method' => 'filterTestInCustomDataSet',
                                'arguments' => ['filterRules' => $ruleValues, 'join' => AutoFilter\Column::AUTOFILTER_COLUMN_JOIN_OR],
                            ];
                        } else {
                            
                            if ($dynamicRuleType[0] == 'M' || $dynamicRuleType[0] == 'Q') {
                                $periodType = '';
                                $period = 0;
                                
                                sscanf($dynamicRuleType, '%[A-Z]%d', $periodType, $period);
                                if ($periodType == 'M') {
                                    $ruleValues = [$period];
                                } else {
                                    --$period;
                                    $periodEnd = (1 + $period) * 3;
                                    $periodStart = 1 + $period * 3;
                                    $ruleValues = range($periodStart, $periodEnd);
                                }
                                $columnFilterTests[$columnID] = [
                                    'method' => 'filterTestInPeriodDateSet',
                                    'arguments' => $ruleValues,
                                ];
                                $filterColumn->setAttributes([]);
                            } else {
                                
                                $columnFilterTests[$columnID] = $this->dynamicFilterDateRange($dynamicRuleType, $filterColumn);

                                break;
                            }
                        }
                    }

                    break;
                case AutoFilter\Column::AUTOFILTER_FILTERTYPE_TOPTENFILTER:
                    $ruleValues = [];
                    $dataRowCount = $rangeEnd[1] - $rangeStart[1];
                    foreach ($rules as $rule) {
                        
                        $toptenRuleType = $rule->getGrouping();
                        $ruleValue = $rule->getValue();
                        $ruleOperator = $rule->getOperator();
                    }
                    if ($ruleOperator === AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT) {
                        $ruleValue = floor($ruleValue * ($dataRowCount / 100));
                    }
                    if ($ruleValue < 1) {
                        $ruleValue = 1;
                    }
                    if ($ruleValue > 500) {
                        $ruleValue = 500;
                    }

                    $maxVal = $this->calculateTopTenValue($columnID, $rangeStart[1] + 1, $rangeEnd[1], $toptenRuleType, $ruleValue);

                    $operator = ($toptenRuleType == AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP)
                        ? AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL
                        : AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL;
                    $ruleValues[] = ['operator' => $operator, 'value' => $maxVal];
                    $columnFilterTests[$columnID] = [
                        'method' => 'filterTestInCustomDataSet',
                        'arguments' => ['filterRules' => $ruleValues, 'join' => AutoFilter\Column::AUTOFILTER_COLUMN_JOIN_OR],
                    ];
                    $filterColumn->setAttributes(['maxVal' => $maxVal]);

                    break;
            }
        }

        
        for ($row = $rangeStart[1] + 1; $row <= $rangeEnd[1]; ++$row) {
            $result = true;
            foreach ($columnFilterTests as $columnID => $columnFilterTest) {
                $cellValue = $this->workSheet->getCell($columnID . $row)->getCalculatedValue();
                
                $result = $result &&
                    call_user_func_array(
                        [self::class, $columnFilterTest['method']],
                        [$cellValue, $columnFilterTest['arguments']]
                    );
                
                if (!$result) {
                    break;
                }
            }
            
            $this->workSheet->getRowDimension($row)->setVisible($result);
        }

        return $this;
    }

    
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                if ($key === 'workSheet') {
                    
                    $this->{$key} = null;
                } else {
                    $this->{$key} = clone $value;
                }
            } elseif ((is_array($value)) && ($key == 'columns')) {
                
                $this->{$key} = [];
                foreach ($value as $k => $v) {
                    $this->{$key}[$k] = clone $v;
                    
                    $this->{$key}[$k]->setParent($this);
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }

    
    public function __toString()
    {
        return (string) $this->range;
    }
}
