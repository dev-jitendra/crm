<?php


namespace Espo\Core\Select\Applier;

use Espo\ORM\Query\SelectBuilder;
use Espo\Core\Select\SearchParams;

interface AdditionalApplier
{
    public function apply(SelectBuilder $queryBuilder, SearchParams $searchParams): void;
}
