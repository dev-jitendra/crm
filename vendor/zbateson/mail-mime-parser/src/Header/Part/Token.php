<?php

namespace ZBateson\MailMimeParser\Header\Part;

use ZBateson\MailMimeParser\Header\Part\HeaderPart;
use ZBateson\MbWrapper\MbWrapper;


class Token extends HeaderPart
{
    
    public function __construct(MbWrapper $charsetConverter, $value)
    {
        parent::__construct($charsetConverter);
        $this->value = $value;
    }
    
    
    public function isSpace()
    {
        return (preg_match('/^\s+$/', $this->value) === 1);
    }
    
    
    public function ignoreSpacesBefore()
    {
        return $this->isSpace();
    }
    
    
    public function ignoreSpacesAfter()
    {
        return $this->isSpace();
    }
}
