<?php


namespace Espo\Core\Select\Text;

use Espo\ORM\Query\SelectBuilder;

use Espo\Core\Select\Text\Filter\Data;

interface Filter
{
    public function apply(SelectBuilder $queryBuilder, Data $data): void;
}
