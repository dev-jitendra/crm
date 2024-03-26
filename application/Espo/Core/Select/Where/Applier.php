<?php


namespace Espo\Core\Select\Where;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Select\Where\Item as WhereItem;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

use Espo\Entities\User;

class Applier
{
    public function __construct(
        private string $entityType,
        private User $user,
        private ConverterFactory $converterFactory,
        private CheckerFactory $checkerFactory
    ) {}

    
    public function apply(QueryBuilder $queryBuilder, WhereItem $whereItem, Params $params): void
    {
        $checker = $this->checkerFactory->create($this->entityType, $this->user);
        $checker->check($whereItem, $params);

        $converter = $this->converterFactory->create($this->entityType, $this->user);
        $whereClause = $converter->convert($queryBuilder, $whereItem);

        $queryBuilder->where($whereClause);
    }
}
