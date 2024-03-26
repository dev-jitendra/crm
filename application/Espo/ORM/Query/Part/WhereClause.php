<?php


namespace Espo\ORM\Query\Part;

use Espo\ORM\Query\Part\Where\AndGroup;


class WhereClause extends AndGroup
{
    public function getRaw(): array
    {
        return $this->getRawValue();
    }
}
