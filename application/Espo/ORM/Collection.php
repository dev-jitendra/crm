<?php


namespace Espo\ORM;

use Traversable;
use stdClass;


interface Collection extends Traversable
{
    
    public function getValueMapList(): array;
}
