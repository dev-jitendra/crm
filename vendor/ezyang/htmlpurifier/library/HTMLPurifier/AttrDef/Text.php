<?php


class HTMLPurifier_AttrDef_Text extends HTMLPurifier_AttrDef
{

    
    public function validate($string, $config, $context)
    {
        return $this->parseCDATA($string);
    }
}


