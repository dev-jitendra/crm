<?php


namespace Espo\Core\Select;

use Espo\ORM\Query\SelectBuilder as QueryBuilder;


class OrmSelectBuilder extends QueryBuilder
{
    
    public function setRawParams(array $params): void
    {
        $this->params = $params;
    }
}
