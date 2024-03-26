<?php


namespace Espo\ORM\Executor;

use PDOStatement;


interface SqlExecutor
{
    
    public function execute(string $sql, bool $rerunIfDeadlock = false): PDOStatement;
}
