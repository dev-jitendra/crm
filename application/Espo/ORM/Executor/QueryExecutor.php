<?php


namespace Espo\ORM\Executor;

use Espo\ORM\Query\Query;

use PDOStatement;


interface QueryExecutor
{
    
    public function execute(Query $query): PDOStatement;
}
