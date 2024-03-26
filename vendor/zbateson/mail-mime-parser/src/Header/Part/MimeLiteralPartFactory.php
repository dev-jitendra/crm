<?php

namespace ZBateson\MailMimeParser\Header\Part;


class MimeLiteralPartFactory extends HeaderPartFactory
{
    
    public function newInstance($value)
    {
        return $this->newMimeLiteralPart($value);
    }
}
