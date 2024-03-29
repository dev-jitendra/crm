<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

class DataValidation
{
    
    const TYPE_NONE = 'none';
    const TYPE_CUSTOM = 'custom';
    const TYPE_DATE = 'date';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_LIST = 'list';
    const TYPE_TEXTLENGTH = 'textLength';
    const TYPE_TIME = 'time';
    const TYPE_WHOLE = 'whole';

    
    const STYLE_STOP = 'stop';
    const STYLE_WARNING = 'warning';
    const STYLE_INFORMATION = 'information';

    
    const OPERATOR_BETWEEN = 'between';
    const OPERATOR_EQUAL = 'equal';
    const OPERATOR_GREATERTHAN = 'greaterThan';
    const OPERATOR_GREATERTHANOREQUAL = 'greaterThanOrEqual';
    const OPERATOR_LESSTHAN = 'lessThan';
    const OPERATOR_LESSTHANOREQUAL = 'lessThanOrEqual';
    const OPERATOR_NOTBETWEEN = 'notBetween';
    const OPERATOR_NOTEQUAL = 'notEqual';

    
    private $formula1 = '';

    
    private $formula2 = '';

    
    private $type = self::TYPE_NONE;

    
    private $errorStyle = self::STYLE_STOP;

    
    private $operator = self::OPERATOR_BETWEEN;

    
    private $allowBlank = false;

    
    private $showDropDown = false;

    
    private $showInputMessage = false;

    
    private $showErrorMessage = false;

    
    private $errorTitle = '';

    
    private $error = '';

    
    private $promptTitle = '';

    
    private $prompt = '';

    
    public function __construct()
    {
    }

    
    public function getFormula1()
    {
        return $this->formula1;
    }

    
    public function setFormula1($value)
    {
        $this->formula1 = $value;

        return $this;
    }

    
    public function getFormula2()
    {
        return $this->formula2;
    }

    
    public function setFormula2($value)
    {
        $this->formula2 = $value;

        return $this;
    }

    
    public function getType()
    {
        return $this->type;
    }

    
    public function setType($value)
    {
        $this->type = $value;

        return $this;
    }

    
    public function getErrorStyle()
    {
        return $this->errorStyle;
    }

    
    public function setErrorStyle($value)
    {
        $this->errorStyle = $value;

        return $this;
    }

    
    public function getOperator()
    {
        return $this->operator;
    }

    
    public function setOperator($value)
    {
        $this->operator = $value;

        return $this;
    }

    
    public function getAllowBlank()
    {
        return $this->allowBlank;
    }

    
    public function setAllowBlank($value)
    {
        $this->allowBlank = $value;

        return $this;
    }

    
    public function getShowDropDown()
    {
        return $this->showDropDown;
    }

    
    public function setShowDropDown($value)
    {
        $this->showDropDown = $value;

        return $this;
    }

    
    public function getShowInputMessage()
    {
        return $this->showInputMessage;
    }

    
    public function setShowInputMessage($value)
    {
        $this->showInputMessage = $value;

        return $this;
    }

    
    public function getShowErrorMessage()
    {
        return $this->showErrorMessage;
    }

    
    public function setShowErrorMessage($value)
    {
        $this->showErrorMessage = $value;

        return $this;
    }

    
    public function getErrorTitle()
    {
        return $this->errorTitle;
    }

    
    public function setErrorTitle($value)
    {
        $this->errorTitle = $value;

        return $this;
    }

    
    public function getError()
    {
        return $this->error;
    }

    
    public function setError($value)
    {
        $this->error = $value;

        return $this;
    }

    
    public function getPromptTitle()
    {
        return $this->promptTitle;
    }

    
    public function setPromptTitle($value)
    {
        $this->promptTitle = $value;

        return $this;
    }

    
    public function getPrompt()
    {
        return $this->prompt;
    }

    
    public function setPrompt($value)
    {
        $this->prompt = $value;

        return $this;
    }

    
    public function getHashCode()
    {
        return md5(
            $this->formula1 .
            $this->formula2 .
            $this->type .
            $this->errorStyle .
            $this->operator .
            ($this->allowBlank ? 't' : 'f') .
            ($this->showDropDown ? 't' : 'f') .
            ($this->showInputMessage ? 't' : 'f') .
            ($this->showErrorMessage ? 't' : 'f') .
            $this->errorTitle .
            $this->error .
            $this->promptTitle .
            $this->prompt .
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
