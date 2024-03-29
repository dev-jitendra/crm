<?php


class HTMLPurifier_VarParser
{

    const C_STRING = 1;
    const ISTRING = 2;
    const TEXT = 3;
    const ITEXT = 4;
    const C_INT = 5;
    const C_FLOAT = 6;
    const C_BOOL = 7;
    const LOOKUP = 8;
    const ALIST = 9;
    const HASH = 10;
    const C_MIXED = 11;

    
    public static $types = array(
        'string' => self::C_STRING,
        'istring' => self::ISTRING,
        'text' => self::TEXT,
        'itext' => self::ITEXT,
        'int' => self::C_INT,
        'float' => self::C_FLOAT,
        'bool' => self::C_BOOL,
        'lookup' => self::LOOKUP,
        'list' => self::ALIST,
        'hash' => self::HASH,
        'mixed' => self::C_MIXED
    );

    
    public static $stringTypes = array(
        self::C_STRING => true,
        self::ISTRING => true,
        self::TEXT => true,
        self::ITEXT => true,
    );

    
    final public function parse($var, $type, $allow_null = false)
    {
        if (is_string($type)) {
            if (!isset(HTMLPurifier_VarParser::$types[$type])) {
                throw new HTMLPurifier_VarParserException("Invalid type '$type'");
            } else {
                $type = HTMLPurifier_VarParser::$types[$type];
            }
        }
        $var = $this->parseImplementation($var, $type, $allow_null);
        if ($allow_null && $var === null) {
            return null;
        }
        
        
        switch ($type) {
            case (self::C_STRING):
            case (self::ISTRING):
            case (self::TEXT):
            case (self::ITEXT):
                if (!is_string($var)) {
                    break;
                }
                if ($type == self::ISTRING || $type == self::ITEXT) {
                    $var = strtolower($var);
                }
                return $var;
            case (self::C_INT):
                if (!is_int($var)) {
                    break;
                }
                return $var;
            case (self::C_FLOAT):
                if (!is_float($var)) {
                    break;
                }
                return $var;
            case (self::C_BOOL):
                if (!is_bool($var)) {
                    break;
                }
                return $var;
            case (self::LOOKUP):
            case (self::ALIST):
            case (self::HASH):
                if (!is_array($var)) {
                    break;
                }
                if ($type === self::LOOKUP) {
                    foreach ($var as $k) {
                        if ($k !== true) {
                            $this->error('Lookup table contains value other than true');
                        }
                    }
                } elseif ($type === self::ALIST) {
                    $keys = array_keys($var);
                    if (array_keys($keys) !== $keys) {
                        $this->error('Indices for list are not uniform');
                    }
                }
                return $var;
            case (self::C_MIXED):
                return $var;
            default:
                $this->errorInconsistent(get_class($this), $type);
        }
        $this->errorGeneric($var, $type);
    }

    
    protected function parseImplementation($var, $type, $allow_null)
    {
        return $var;
    }

    
    protected function error($msg)
    {
        throw new HTMLPurifier_VarParserException($msg);
    }

    
    protected function errorInconsistent($class, $type)
    {
        throw new HTMLPurifier_Exception(
            "Inconsistency in $class: " . HTMLPurifier_VarParser::getTypeName($type) .
            " not implemented"
        );
    }

    
    protected function errorGeneric($var, $type)
    {
        $vtype = gettype($var);
        $this->error("Expected type " . HTMLPurifier_VarParser::getTypeName($type) . ", got $vtype");
    }

    
    public static function getTypeName($type)
    {
        static $lookup;
        if (!$lookup) {
            
            $lookup = array_flip(HTMLPurifier_VarParser::$types);
        }
        if (!isset($lookup[$type])) {
            return 'unknown';
        }
        return $lookup[$type];
    }
}


