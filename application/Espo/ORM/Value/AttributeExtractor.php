<?php


namespace Espo\ORM\Value;

use stdClass;


interface AttributeExtractor
{
    
    public function extract(object $value, string $field): stdClass;

    public function extractFromNull(string $field): stdClass;
}
