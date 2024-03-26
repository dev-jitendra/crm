<?php

namespace Laminas\Mail\Storage\Part;

use ArrayIterator;
use Laminas\Mail\Header\HeaderInterface;
use Laminas\Mail\Headers;
use RecursiveIterator;

interface PartInterface extends RecursiveIterator
{
    
    public function isMultipart();

    
    public function getContent();

    
    public function getSize();

    
    public function getPart($num);

    
    public function countParts();

    
    public function getHeaders();

    
    public function getHeader($name, $format = null);

    
    public function getHeaderField($name, $wantedPart = '0', $firstName = '0');

    
    public function __get($name);

    
    public function __toString();
}
