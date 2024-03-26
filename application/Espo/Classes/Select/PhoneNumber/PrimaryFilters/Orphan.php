<?php


namespace Espo\Classes\Select\PhoneNumber\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\SelectBuilder;

class Orphan implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'entityPhoneNumber.id' => null,
        ]);

        $queryBuilder->leftJoin(
            'EntityPhoneNumber',
            'entityPhoneNumber',
            [
                'phoneNumberId:' => 'id',
                'deleted' => false,
            ]
        );

        $queryBuilder->distinct();
    }
}
