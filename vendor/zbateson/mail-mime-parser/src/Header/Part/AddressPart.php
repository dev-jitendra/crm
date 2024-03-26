<?php

namespace ZBateson\MailMimeParser\Header\Part;

use ZBateson\MbWrapper\MbWrapper;


class AddressPart extends ParameterPart
{
    
    public function __construct(MbWrapper $charsetConverter, $name, $email)
    {
        parent::__construct(
            $charsetConverter,
            $name,
            ''
        );
        
        $this->value = $this->convertEncoding(preg_replace('/\s+/', '', $email));
    }
    
    
    public function getEmail()
    {
        return $this->value;
    }
}
