<?php


namespace Espo\ORM\Executor;

use Espo\ORM\Query\Query;
use Espo\ORM\QueryComposer\QueryComposerWrapper;

use PDOStatement;

class DefaultQueryExecutor implements QueryExecutor
{
    public function __construct(
        private SqlExecutor $sqlExecutor,
        private QueryComposerWrapper $queryComposer
    ) {}

    public function execute(Query $query): PDOStatement
    {
        $sql = $this->queryComposer->compose($query);

        return $this->sqlExecutor->execute($sql, true);
    }
}
