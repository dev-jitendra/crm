<?php


class HTMLPurifier_AttrDef_CSS_Filter extends HTMLPurifier_AttrDef
{
    
    protected $intValidator;

    public function __construct()
    {
        $this->intValidator = new HTMLPurifier_AttrDef_Integer();
    }

    
    public function validate($value, $config, $context)
    {
        $value = $this->parseCDATA($value);
        if ($value === 'none') {
            return $value;
        }
        
        $function_length = strcspn($value, '(');
        $function = trim(substr($value, 0, $function_length));
        if ($function !== 'alpha' &&
            $function !== 'Alpha' &&
            $function !== 'progid:DXImageTransform.Microsoft.Alpha'
        ) {
            return false;
        }
        $cursor = $function_length + 1;
        $parameters_length = strcspn($value, ')', $cursor);
        $parameters = substr($value, $cursor, $parameters_length);
        $params = explode(',', $parameters);
        $ret_params = array();
        $lookup = array();
        foreach ($params as $param) {
            list($key, $value) = explode('=', $param);
            $key = trim($key);
            $value = trim($value);
            if (isset($lookup[$key])) {
                continue;
            }
            if ($key !== 'opacity') {
                continue;
            }
            $value = $this->intValidator->validate($value, $config, $context);
            if ($value === false) {
                continue;
            }
            $int = (int)$value;
            if ($int > 100) {
                $value = '100';
            }
            if ($int < 0) {
                $value = '0';
            }
            $ret_params[] = "$key=$value";
            $lookup[$key] = true;
        }
        $ret_parameters = implode(',', $ret_params);
        $ret_function = "$function($ret_parameters)";
        return $ret_function;
    }
}


