<?php

namespace ZBateson\MailMimeParser\Header\Part;

use ZBateson\MbWrapper\MbWrapper;


abstract class HeaderPart
{
    
    protected $value;
    
    
    protected $charsetConverter;
    
    
    public function __construct(MbWrapper $charsetConverter)
    {
        $this->charsetConverter = $charsetConverter;
    }

    
    public function getValue()
    {
        return $this->value;
    }
    
    
    public function __toString()
    {
        return $this->value;
    }
    
    
    public function ignoreSpacesBefore()
    {
        return false;
    }
    
    
    public function ignoreSpacesAfter()
    {
        return false;
    }
    
    
    protected function convertEncoding($str, $from = 'ISO-8859-1', $force = false)
    {
        if ($from !== 'UTF-8') {
            
            
            if ($force || !($this->charsetConverter->checkEncoding($str, 'UTF-8'))) {
                return $this->charsetConverter->convert($str, $from, 'UTF-8');
            }
        }
        return $str;
    }
}
