<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;

class AutoFilter
{
    private $worksheet;

    private $worksheetXml;

    public function __construct(Worksheet $workSheet, SimpleXMLElement $worksheetXml)
    {
        $this->worksheet = $workSheet;
        $this->worksheetXml = $worksheetXml;
    }

    public function load(): void
    {
        
        $autoFilterRange = preg_replace('/\$/', '', $this->worksheetXml->autoFilter['ref']);
        if (strpos($autoFilterRange, ':') !== false) {
            $this->readAutoFilter($autoFilterRange, $this->worksheetXml);
        }
    }

    private function readAutoFilter($autoFilterRange, $xmlSheet): void
    {
        $autoFilter = $this->worksheet->getAutoFilter();
        $autoFilter->setRange($autoFilterRange);

        foreach ($xmlSheet->autoFilter->filterColumn as $filterColumn) {
            $column = $autoFilter->getColumnByOffset((int) $filterColumn['colId']);
            
            if ($filterColumn->filters) {
                $column->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
                $filters = $filterColumn->filters;
                if ((isset($filters['blank'])) && ($filters['blank'] == 1)) {
                    
                    $column->createRule()->setRule(null, '')->setRuleType(Rule::AUTOFILTER_RULETYPE_FILTER);
                }
                
                
                foreach ($filters->filter as $filterRule) {
                    
                    $column->createRule()->setRule(null, (string) $filterRule['val'])->setRuleType(Rule::AUTOFILTER_RULETYPE_FILTER);
                }

                
                $this->readDateRangeAutoFilter($filters, $column);
            }

            
            $this->readCustomAutoFilter($filterColumn, $column);
            
            $this->readDynamicAutoFilter($filterColumn, $column);
            
            $this->readTopTenAutoFilter($filterColumn, $column);
        }
    }

    private function readDateRangeAutoFilter(SimpleXMLElement $filters, Column $column): void
    {
        foreach ($filters->dateGroupItem as $dateGroupItem) {
            
            $column->createRule()->setRule(
                null,
                [
                    'year' => (string) $dateGroupItem['year'],
                    'month' => (string) $dateGroupItem['month'],
                    'day' => (string) $dateGroupItem['day'],
                    'hour' => (string) $dateGroupItem['hour'],
                    'minute' => (string) $dateGroupItem['minute'],
                    'second' => (string) $dateGroupItem['second'],
                ],
                (string) $dateGroupItem['dateTimeGrouping']
            )->setRuleType(Rule::AUTOFILTER_RULETYPE_DATEGROUP);
        }
    }

    private function readCustomAutoFilter(SimpleXMLElement $filterColumn, Column $column): void
    {
        if ($filterColumn->customFilters) {
            $column->setFilterType(Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER);
            $customFilters = $filterColumn->customFilters;
            
            
            if ((isset($customFilters['and'])) && ($customFilters['and'] == 1)) {
                $column->setJoin(Column::AUTOFILTER_COLUMN_JOIN_AND);
            }
            foreach ($customFilters->customFilter as $filterRule) {
                $column->createRule()->setRule(
                    (string) $filterRule['operator'],
                    (string) $filterRule['val']
                )->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
            }
        }
    }

    private function readDynamicAutoFilter(SimpleXMLElement $filterColumn, Column $column): void
    {
        if ($filterColumn->dynamicFilter) {
            $column->setFilterType(Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER);
            
            foreach ($filterColumn->dynamicFilter as $filterRule) {
                
                $column->createRule()->setRule(
                    null,
                    (string) $filterRule['val'],
                    (string) $filterRule['type']
                )->setRuleType(Rule::AUTOFILTER_RULETYPE_DYNAMICFILTER);
                if (isset($filterRule['val'])) {
                    $column->setAttribute('val', (string) $filterRule['val']);
                }
                if (isset($filterRule['maxVal'])) {
                    $column->setAttribute('maxVal', (string) $filterRule['maxVal']);
                }
            }
        }
    }

    private function readTopTenAutoFilter(SimpleXMLElement $filterColumn, Column $column): void
    {
        if ($filterColumn->top10) {
            $column->setFilterType(Column::AUTOFILTER_FILTERTYPE_TOPTENFILTER);
            
            foreach ($filterColumn->top10 as $filterRule) {
                $column->createRule()->setRule(
                    (((isset($filterRule['percent'])) && ($filterRule['percent'] == 1))
                        ? Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT
                        : Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BY_VALUE
                    ),
                    (string) $filterRule['val'],
                    (((isset($filterRule['top'])) && ($filterRule['top'] == 1))
                        ? Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP
                        : Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BOTTOM
                    )
                )->setRuleType(Rule::AUTOFILTER_RULETYPE_TOPTENFILTER);
            }
        }
    }
}
