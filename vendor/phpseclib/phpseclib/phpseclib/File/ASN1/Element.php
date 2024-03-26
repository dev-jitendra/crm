<?php



namespace phpseclib3\File\ASN1;


class Element
{
    
    public $element;

    
    public function __construct($encoded)
    {
        $this->element = $encoded;
    }
}
