<?php


class HTMLPurifier_AttrTransform_Length extends HTMLPurifier_AttrTransform
{

    
    protected $name;

    
    protected $cssName;

    public function __construct($name, $css_name = null)
    {
        $this->name = $name;
        $this->cssName = $css_name ? $css_name : $name;
    }

    
    public function transform($attr, $config, $context)
    {
        if (!isset($attr[$this->name])) {
            return $attr;
        }
        $length = $this->confiscateAttr($attr, $this->name);
        if (ctype_digit($length)) {
            $length .= 'px';
        }
        $this->prependCSS($attr, $this->cssName . ":$length;");
        return $attr;
    }
}


