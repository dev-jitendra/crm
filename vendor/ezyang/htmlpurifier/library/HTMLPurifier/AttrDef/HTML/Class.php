<?php


class HTMLPurifier_AttrDef_HTML_Class extends HTMLPurifier_AttrDef_HTML_Nmtokens
{
    
    protected function split($string, $config, $context)
    {
        
        $name = $config->getDefinition('HTML')->doctype->name;
        if ($name == "XHTML 1.1" || $name == "XHTML 2.0") {
            return parent::split($string, $config, $context);
        } else {
            return preg_split('/\s+/', $string);
        }
    }

    
    protected function filter($tokens, $config, $context)
    {
        $allowed = $config->get('Attr.AllowedClasses');
        $forbidden = $config->get('Attr.ForbiddenClasses');
        $ret = array();
        foreach ($tokens as $token) {
            if (($allowed === null || isset($allowed[$token])) &&
                !isset($forbidden[$token]) &&
                
                
                !in_array($token, $ret, true)
            ) {
                $ret[] = $token;
            }
        }
        return $ret;
    }
}
