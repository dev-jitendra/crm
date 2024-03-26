<?php


namespace Espo\ORM\QueryComposer;

use Espo\ORM\Query\Query as Query;
use Espo\ORM\Query\Select as SelectQuery;
use Espo\ORM\Query\Update as UpdateQuery;
use Espo\ORM\Query\Insert as InsertQuery;
use Espo\ORM\Query\Delete as DeleteQuery;
use Espo\ORM\Query\Union as UnionQuery;
use Espo\ORM\Query\LockTable as LockTableQuery;

use RuntimeException;

class QueryComposerWrapper implements QueryComposer
{
    private QueryComposer $queryComposer;

    public function __construct(QueryComposer $queryComposer)
    {
        $this->queryComposer = $queryComposer;
    }

    
    public function compose(Query $query): string
    {
        if ($query instanceof SelectQuery) {
            return $this->composeSelect($query);
        }

        if ($query instanceof UpdateQuery) {
            return $this->composeUpdate($query);
        }

        if ($query instanceof InsertQuery) {
            return $this->composeInsert($query);
        }

        if ($query instanceof DeleteQuery) {
            return $this->composeDelete($query);
        }

        if ($query instanceof UnionQuery) {
            return $this->composeUnion($query);
        }

        if ($query instanceof LockTableQuery) {
            return $this->composeLockTable($query);
        }

        throw new RuntimeException("ORM Query: Unknown query type passed.");
    }

    public function composeSelect(SelectQuery $query): string
    {
        return $this->queryComposer->composeSelect($query);
    }

    public function composeUpdate(UpdateQuery $query): string
    {
        return $this->queryComposer->composeUpdate($query);
    }

    public function composeDelete(DeleteQuery $query): string
    {
        return $this->queryComposer->composeDelete($query);
    }

    public function composeInsert(InsertQuery $query): string
    {
        return $this->queryComposer->composeInsert($query);
    }

    public function composeUnion(UnionQuery $query): string
    {
        return $this->queryComposer->composeUnion($query);
    }

    public function composeLockTable(LockTableQuery $query): string
    {
        return $this->queryComposer->composeLockTable($query);
    }

    public function composeCreateSavepoint(string $savepointName): string
    {
        return $this->queryComposer->composeCreateSavepoint($savepointName);
    }

    public function composeReleaseSavepoint(string $savepointName): string
    {
        return $this->queryComposer->composeReleaseSavepoint($savepointName);
    }

    public function composeRollbackToSavepoint(string $savepointName): string
    {
        return $this->queryComposer->composeRollbackToSavepoint($savepointName);
    }
}
