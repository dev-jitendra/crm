<?php

class HTMLPurifier_AttrDef_CSS_AlphaValue extends HTMLPurifier_AttrDef_CSS_Number
{

    public function __construct()
    {
        parent::__construct(false); 
    }

    
    public function validate($number, $config, $context)
    {
        $result = parent::validate($number, $config, $context);
        if ($result === false) {
            return $result;
        }
        $float = (float)$result;
        if ($float < 0.0) {
            $result = '0';
        }
        if ($float > 1.0) {
            $result = '1';
        }
        return $result;
    }
}


