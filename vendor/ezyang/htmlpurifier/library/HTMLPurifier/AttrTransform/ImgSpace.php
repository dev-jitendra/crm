<?php


class HTMLPurifier_AttrTransform_ImgSpace extends HTMLPurifier_AttrTransform
{
    
    protected $attr;

    
    protected $css = array(
        'hspace' => array('left', 'right'),
        'vspace' => array('top', 'bottom')
    );

    
    public function __construct($attr)
    {
        $this->attr = $attr;
        if (!isset($this->css[$attr])) {
            trigger_error(htmlspecialchars($attr) . ' is not valid space attribute');
        }
    }

    
    public function transform($attr, $config, $context)
    {
        if (!isset($attr[$this->attr])) {
            return $attr;
        }

        $width = $this->confiscateAttr($attr, $this->attr);
        

        if (!isset($this->css[$this->attr])) {
            return $attr;
        }

        $style = '';
        foreach ($this->css[$this->attr] as $suffix) {
            $property = "margin-$suffix";
            $style .= "$property:{$width}px;";
        }
        $this->prependCSS($attr, $style);
        return $attr;
    }
}


