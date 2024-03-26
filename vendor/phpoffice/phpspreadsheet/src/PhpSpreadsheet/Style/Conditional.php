<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\IComparable;

class Conditional implements IComparable
{
    
    const CONDITION_NONE = 'none';
    const CONDITION_CELLIS = 'cellIs';
    const CONDITION_CONTAINSTEXT = 'containsText';
    const CONDITION_EXPRESSION = 'expression';
    const CONDITION_CONTAINSBLANKS = 'containsBlanks';
    const CONDITION_NOTCONTAINSBLANKS = 'notContainsBlanks';

    
    const OPERATOR_NONE = '';
    const OPERATOR_BEGINSWITH = 'beginsWith';
    const OPERATOR_ENDSWITH = 'endsWith';
    const OPERATOR_EQUAL = 'equal';
    const OPERATOR_GREATERTHAN = 'greaterThan';
    const OPERATOR_GREATERTHANOREQUAL = 'greaterThanOrEqual';
    const OPERATOR_LESSTHAN = 'lessThan';
    const OPERATOR_LESSTHANOREQUAL = 'lessThanOrEqual';
    const OPERATOR_NOTEQUAL = 'notEqual';
    const OPERATOR_CONTAINSTEXT = 'containsText';
    const OPERATOR_NOTCONTAINS = 'notContains';
    const OPERATOR_BETWEEN = 'between';
    const OPERATOR_NOTBETWEEN = 'notBetween';

    
    private $conditionType = self::CONDITION_NONE;

    
    private $operatorType = self::OPERATOR_NONE;

    
    private $text;

    
    private $stopIfTrue = false;

    
    private $condition = [];

    
    private $style;

    
    public function __construct()
    {
        
        $this->style = new Style(false, true);
    }

    
    public function getConditionType()
    {
        return $this->conditionType;
    }

    
    public function setConditionType($pValue)
    {
        $this->conditionType = $pValue;

        return $this;
    }

    
    public function getOperatorType()
    {
        return $this->operatorType;
    }

    
    public function setOperatorType($pValue)
    {
        $this->operatorType = $pValue;

        return $this;
    }

    
    public function getText()
    {
        return $this->text;
    }

    
    public function setText($value)
    {
        $this->text = $value;

        return $this;
    }

    
    public function getStopIfTrue()
    {
        return $this->stopIfTrue;
    }

    
    public function setStopIfTrue($value)
    {
        $this->stopIfTrue = $value;

        return $this;
    }

    
    public function getConditions()
    {
        return $this->condition;
    }

    
    public function setConditions($pValue)
    {
        if (!is_array($pValue)) {
            $pValue = [$pValue];
        }
        $this->condition = $pValue;

        return $this;
    }

    
    public function addCondition($pValue)
    {
        $this->condition[] = $pValue;

        return $this;
    }

    
    public function getStyle()
    {
        return $this->style;
    }

    
    public function setStyle(?Style $pValue = null)
    {
        $this->style = $pValue;

        return $this;
    }

    
    public function getHashCode()
    {
        return md5(
            $this->conditionType .
            $this->operatorType .
            implode(';', $this->condition) .
            $this->style->getHashCode() .
            __CLASS__
        );
    }

    
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
