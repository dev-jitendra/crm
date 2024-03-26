<?php

namespace ZBateson\MailMimeParser\Header\Part;

use ZBateson\MbWrapper\MbWrapper;


class AddressGroupPart extends MimeLiteralPart
{
    
    protected $addresses;
    
    
    public function __construct(MbWrapper $charsetConverter, array $addresses, $name = '')
    {
        parent::__construct($charsetConverter, trim($name));
        $this->addresses = $addresses;
    }
    
    
    public function getAddresses()
    {
        return $this->addresses;
    }
    
    
    public function getAddress($index)
    {
        if (!isset($this->addresses[$index])) {
            return null;
        }
        return $this->addresses[$index];
    }
    
    
    public function getName()
    {
        return $this->value;
    }
}
