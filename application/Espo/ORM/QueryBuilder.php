<?php


namespace Espo\ORM;

use Espo\ORM\Query\DeleteBuilder;
use Espo\ORM\Query\InsertBuilder;
use Espo\ORM\Query\Part\Expression;
use Espo\ORM\Query\Part\Selection;
use Espo\ORM\Query\Query;
use Espo\ORM\Query\SelectBuilder;
use Espo\ORM\Query\UnionBuilder;
use Espo\ORM\Query\UpdateBuilder;

use ReflectionClass;
use RuntimeException;


class QueryBuilder
{
    
    public function select($select = null, ?string $alias = null): SelectBuilder
    {
        $builder = new SelectBuilder();

        if ($select === null) {
            return $builder;
        }

        return $builder->select($select, $alias);
    }

    
    public function update(): UpdateBuilder
    {
        return new UpdateBuilder();
    }

    
    public function delete(): DeleteBuilder
    {
        return new DeleteBuilder();
    }

    
    public function insert(): InsertBuilder
    {
        return new InsertBuilder();
    }

    
    public function union(): UnionBuilder
    {
        return new UnionBuilder();
    }

    
    public function clone(Query $query): SelectBuilder|UpdateBuilder|DeleteBuilder|InsertBuilder|UnionBuilder
    {
        $class = new ReflectionClass($query);

        $methodName = ucfirst($class->getShortName());

        if (!method_exists($this, $methodName)) {
            throw new RuntimeException("Can't clone an unsupported query.");
        }

        return $this->$methodName()->clone($query);
    }
}
